<?php

namespace App\Domain\Fulfillment\Models;

use App\Domain\Orders\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'courier_provider',
        'consignment_id',
        'tracking_code',
        'status',
        'cod_amount',
        'courier_fee',
        'recipient_name',
        'recipient_phone',
        'district',
        'recipient_address',
        'dispatched_at',
        'delivered_at',
        'courier_metadata',
    ];

    protected $casts = [
        'cod_amount' => 'integer',
        'courier_fee' => 'integer',
        'dispatched_at' => 'datetime',
        'delivered_at' => 'datetime',
        'courier_metadata' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(ShipmentEvent::class);
    }
}
