<?php

namespace App\Domain\Payments\Models;

use App\Domain\Orders\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAttempt extends Model
{
    protected $fillable = [
        'order_id',
        'provider',
        'transaction_id',
        'status',
        'amount',
        'currency',
        'gateway_redirect_url',
        'request_payload',
        'response_payload',
        'ip_address',
    ];

    protected $casts = [
        'amount' => 'integer',
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
