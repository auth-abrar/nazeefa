<?php

namespace App\Domain\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderAddress extends Model
{
    protected $fillable = [
        'order_id',
        'full_name',
        'phone',
        'district',
        'thana_area',
        'street_address',
        'landmark',
        'is_inside_dhaka',
    ];

    protected $casts = [
        'is_inside_dhaka' => 'boolean',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
