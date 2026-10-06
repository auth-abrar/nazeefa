<?php

namespace App\Domain\Suppliers\Models;

use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierProduct extends Model
{
    protected $fillable = [
        'supplier_id',
        'product_id',
        'external_product_id',
        'external_sku',
        'title',
        'image_url',
        'supplier_cost_cents',
        'supplier_currency',
        'estimated_freight_cents',
        'raw_payload',
        'status',
        'last_synced_at',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'last_synced_at' => 'datetime',
        'supplier_cost_cents' => 'integer',
        'estimated_freight_cents' => 'integer',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(SupplierVariant::class);
    }
}
