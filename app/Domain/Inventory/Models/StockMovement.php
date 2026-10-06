<?php

namespace App\Domain\Inventory\Models;

use App\Enums\StockMovementType;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'inventory_item_id',
        'type',
        'quantity',
        'reference_type',
        'reference_id',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'type' => StockMovementType::class,
        'quantity' => 'integer',
    ];

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
