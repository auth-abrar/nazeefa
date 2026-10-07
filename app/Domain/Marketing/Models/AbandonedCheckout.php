<?php

namespace App\Domain\Marketing\Models;

use Illuminate\Database\Eloquent\Model;

class AbandonedCheckout extends Model
{
    protected $fillable = [
        'session_id',
        'customer_phone',
        'customer_name',
        'customer_email',
        'district',
        'cart_payload',
        'subtotal_amount',
        'recovery_token',
        'status',
        'recovery_sent_count',
        'recovered_at',
    ];

    protected $casts = [
        'cart_payload' => 'array',
        'subtotal_amount' => 'integer',
        'recovery_sent_count' => 'integer',
        'recovered_at' => 'datetime',
    ];

    public function getSubtotalBdtAttribute(): float
    {
        return $this->subtotal_amount / 100;
    }
}
