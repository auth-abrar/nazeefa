<?php

namespace App\Domain\Orders\Actions;

use App\Domain\Inventory\Models\InventoryItem;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class RouteOrderFulfillmentAction
{
    /**
     * Inspect order items and route through the hybrid fulfillment engine:
     * - local_warehouse: Dispatched from Dhaka Central Hub (24-48h Pathao/Steadfast)
     * - print_on_demand: Floor DTF/Screen printing (2-3 days custom lead time)
     * - dropship: International direct fulfillment (CJ Dropshipping)
     * - hybrid: Split shipment combining multiple channels
     */
    public function execute(Order $order): array
    {
        return DB::transaction(function () use ($order) {
            $order->load(['items.product', 'items.variant']);

            $itemStrategies = [];
            $hasLocal = false;
            $hasPod = false;
            $hasDropship = false;

            foreach ($order->items as $item) {
                $strategy = $this->determineItemStrategy($item);
                $itemStrategies[$item->id] = $strategy;

                if ($strategy === 'local_warehouse') {
                    $hasLocal = true;
                } elseif ($strategy === 'print_on_demand') {
                    $hasPod = true;
                } elseif ($strategy === 'dropship') {
                    $hasDropship = true;
                }
            }

            // Check if order requires split fulfillment across distinct channels
            $distinctStrategies = array_unique(array_values($itemStrategies));
            $isSplit = count($distinctStrategies) > 1;

            $overallStrategy = match (count($distinctStrategies)) {
                0 => 'local_warehouse',
                1 => reset($distinctStrategies),
                default => 'hybrid',
            };

            $order->update([
                'fulfillment_strategy' => $overallStrategy,
                'is_split_shipment' => $isSplit,
            ]);

            return [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'overall_strategy' => $overallStrategy,
                'is_split_shipment' => $isSplit,
                'item_routes' => $itemStrategies,
                'channels' => [
                    'local_warehouse' => $hasLocal,
                    'print_on_demand' => $hasPod,
                    'dropship' => $hasDropship,
                ],
            ];
        });
    }

    private function determineItemStrategy(OrderItem $item): string
    {
        $product = $item->product;

        // 1. Is it a custom POD item?
        if ($product && $product->is_customizable) {
            return 'print_on_demand';
        }

        // 2. Is it flagged as an international dropship product?
        if ($product && isset($product->metadata['dropship']) && $product->metadata['dropship'] === true) {
            return 'dropship';
        }

        // 3. Check local central warehouse inventory
        if ($item->product_variant_id) {
            $inventory = InventoryItem::where('product_variant_id', $item->product_variant_id)->first();
            if ($inventory && ($inventory->on_hand - $inventory->reserved) >= $item->quantity) {
                return 'local_warehouse';
            }
        }

        // Default to local warehouse fulfillment queue
        return 'local_warehouse';
    }
}
