<?php

namespace App\Domain\Inventory\Actions;

use App\Domain\Inventory\Models\InventoryItem;
use App\Domain\Inventory\Models\StockMovement;
use App\Enums\StockMovementType;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AdjustInventoryAction
{
    public function execute(
        InventoryItem $item,
        int $quantityDelta,
        StockMovementType $type,
        string $notes = '',
        ?int $userId = null
    ): StockMovement {
        return DB::transaction(function () use ($item, $quantityDelta, $type, $notes, $userId) {
            $item->lockForUpdate();

            $newOnHand = $item->on_hand + $quantityDelta;
            if ($newOnHand < 0) {
                throw new InvalidArgumentException('Inventory on hand cannot drop below zero.');
            }

            $item->update(['on_hand' => $newOnHand]);

            // Sync with variant's global on_hand count
            $item->variant->increment('stock_on_hand', $quantityDelta);

            return StockMovement::create([
                'inventory_item_id' => $item->id,
                'type' => $type,
                'quantity' => $quantityDelta,
                'reference_type' => 'ManualAdjustment',
                'notes' => $notes,
                'user_id' => $userId,
            ]);
        });
    }
}
