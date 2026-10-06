<?php

namespace App\Domain\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'size',
        'color_name',
        'color_hex',
        'price_amount',
        'compare_at_amount',
        'currency',
        'weight_grams',
        'stock_on_hand',
        'stock_reserved',
        'is_active',
    ];

    protected $casts = [
        'price_amount' => 'integer',
        'compare_at_amount' => 'integer',
        'stock_on_hand' => 'integer',
        'stock_reserved' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->stock_on_hand - $this->stock_reserved);
    }
}
