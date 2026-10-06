<?php

namespace App\Domain\Suppliers\Models;

use App\Domain\Catalog\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierVariant extends Model
{
    protected $fillable = [
        'supplier_product_id',
        'product_variant_id',
        'external_variant_id',
        'external_sku',
        'variant_name',
        'supplier_cost_cents',
        'supplier_stock',
        'weight_grams',
    ];

    protected $casts = [
        'supplier_cost_cents' => 'integer',
        'supplier_stock' => 'integer',
        'weight_grams' => 'integer',
    ];

    public function supplierProduct(): BelongsTo
    {
        return $this->belongsTo(SupplierProduct::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
