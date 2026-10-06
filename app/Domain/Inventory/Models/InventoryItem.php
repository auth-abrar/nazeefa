<?php

namespace App\Domain\Inventory\Models;

use App\Domain\Catalog\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $fillable = [
        'warehouse_id',
        'product_variant_id',
        'on_hand',
        'reserved',
        'safety_stock',
    ];

    protected $casts = [
        'on_hand' => 'integer',
        'reserved' => 'integer',
        'safety_stock' => 'integer',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getAvailableAttribute(): int
    {
        return max(0, $this->on_hand - $this->reserved);
    }
}
