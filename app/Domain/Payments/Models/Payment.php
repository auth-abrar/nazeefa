<?php

namespace App\Domain\Payments\Models;

use App\Domain\Orders\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'provider',
        'transaction_id',
        'amount',
        'fee_amount',
        'currency',
        'status',
        'bank_tran_id',
        'card_type',
        'payload_response',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'fee_amount' => 'integer',
        'payload_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
