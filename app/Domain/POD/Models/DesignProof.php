<?php

namespace App\Domain\POD\Models;

use App\Domain\Orders\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesignProof extends Model
{
    protected $fillable = [
        'order_item_id',
        'artwork_id',
        'proof_image_url',
        'version',
        'status',
        'customer_feedback',
        'approved_at',
    ];

    protected $casts = [
        'version' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }
}
