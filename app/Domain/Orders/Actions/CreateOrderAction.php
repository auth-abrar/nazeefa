<?php

namespace App\Domain\Orders\Actions;

use App\Domain\Catalog\Models\ProductVariant;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\OrderItem;
use App\Domain\Orders\Models\OrderAddress;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CreateOrderAction
{
    /**
     * Executes atomic order creation with Bangladesh fulfillment rules and stock reservation.
     *
     * @param array $payload Validated customer & cart payload
     * @return Order
     */
    public function execute(array $payload): Order
    {
        return DB::transaction(function () use ($payload) {
            $itemsData = $payload['items'] ?? [];
            if (empty($itemsData)) {
                throw new InvalidArgumentException('Cart cannot be empty.');
            }

            $subtotalPoisha = 0;
            $orderItemsToCreate = [];

            // 1. Lock and resolve variants
            foreach ($itemsData as $item) {
                /** @var ProductVariant $variant */
                $variant = ProductVariant::with('product')
                    ->where('id', $item['variant_id'])
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->firstOrFail();

                $quantity = (int) $item['quantity'];
                if ($quantity <= 0) {
                    throw new InvalidArgumentException('Invalid quantity specified.');
                }

                if ($variant->stock_on_hand - $variant->stock_reserved < $quantity) {
                    throw new InvalidArgumentException("Insufficient stock for {$variant->product->name} ({$variant->size}).");
                }

                $itemTotalPoisha = $variant->price_amount * $quantity;
                $subtotalPoisha += $itemTotalPoisha;

                // Reserve inventory immediately
                $variant->increment('stock_reserved', $quantity);

                $orderItemsToCreate[] = [
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'variant_sku' => $variant->sku,
                    'size' => $variant->size,
                    'color' => $variant->color_name,
                    'unit_price' => $variant->price_amount,
                    'quantity' => $quantity,
                    'total_price' => $itemTotalPoisha,
                ];
            }

            // 2. Compute Bangladesh shipping rate
            // Inside Dhaka: ৳ 70 (7000 poisha), Outside Dhaka: ৳ 130 (13000 poisha)
            // Free shipping if subtotal >= ৳ 2,500 (250000 poisha)
            $isInsideDhaka = strtolower(trim($payload['district'])) === 'dhaka';
            $shippingPoisha = $isInsideDhaka ? 7000 : 13000;
            if ($subtotalPoisha >= 250000) {
                $shippingPoisha = 0;
            }

            $discountPoisha = 0; // Extensible coupon calculation hook
            $grandTotalPoisha = max(0, $subtotalPoisha + $shippingPoisha - $discountPoisha);

            // 3. Create unique sequential-like human order number
            $orderNumber = 'NZ-' . date('Ym') . '-' . strtoupper(Str::random(5));

            // 4. Create Order record
            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_id' => $payload['customer_id'] ?? null,
                'guest_name' => $payload['full_name'],
                'guest_phone' => $payload['phone'],
                'guest_email' => $payload['email'] ?? null,
                'status' => OrderStatus::CONFIRMED,
                'items_subtotal' => $subtotalPoisha,
                'shipping_amount' => $shippingPoisha,
                'discount_amount' => $discountPoisha,
                'grand_total' => $grandTotalPoisha,
                'currency' => 'BDT',
                'payment_method' => $payload['payment_method'] ?? 'cod',
                'payment_status' => 'pending',
                'customer_notes' => $payload['notes'] ?? null,
                'ip_address' => request()->ip(),
            ]);

            // 5. Create Order Items
            foreach ($orderItemsToCreate as $itemData) {
                $order->items()->create($itemData);
            }

            // 6. Create Order Address
            $order->address()->create([
                'full_name' => $payload['full_name'],
                'phone' => $payload['phone'],
                'district' => $payload['district'],
                'thana_area' => $payload['thana_area'] ?? '',
                'street_address' => $payload['street_address'],
                'landmark' => $payload['landmark'] ?? null,
                'is_inside_dhaka' => $isInsideDhaka,
            ]);

            return $order;
        });
    }
}
