<?php

namespace App\Domain\Suppliers\Models;

use App\Domain\Orders\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierOrder extends Model
{
    protected $fillable = [
        'order_id',
        'supplier_id',
        'external_order_id',
        'order_number',
        'status',
        'tracking_number',
        'tracking_carrier',
        'supplier_cost_total_cents',
        'supplier_freight_total_cents',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'supplier_cost_total_cents' => 'integer',
        'supplier_freight_total_cents' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
