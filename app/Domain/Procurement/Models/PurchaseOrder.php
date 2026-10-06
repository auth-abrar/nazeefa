<?php

namespace App\Domain\Procurement\Models;

use App\Domain\Inventory\Models\Warehouse;
use App\Domain\Suppliers\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'supplier_id',
        'warehouse_id',
        'po_number',
        'status',
        'currency',
        'total_cost_cents',
        'shipping_freight_cents',
        'customs_duty_cents',
        'payment_terms',
        'deposit_paid_at',
        'balance_paid_at',
        'estimated_arrival_at',
        'actual_received_at',
        'port_of_entry',
        'tracking_bol_number',
        'notes',
    ];

    protected $casts = [
        'total_cost_cents' => 'integer',
        'shipping_freight_cents' => 'integer',
        'customs_duty_cents' => 'integer',
        'deposit_paid_at' => 'datetime',
        'balance_paid_at' => 'datetime',
        'estimated_arrival_at' => 'datetime',
        'actual_received_at' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function getLandedTotalCentsAttribute(): int
    {
        return $this->total_cost_cents + $this->shipping_freight_cents + $this->customs_duty_cents;
    }
}
