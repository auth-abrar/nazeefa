<?php

namespace App\Domain\POD\Models;

use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\OrderItem;
use App\Enums\ProductionStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionJob extends Model
{
    protected $fillable = [
        'job_number',
        'order_id',
        'order_item_id',
        'artwork_id',
        'print_method',
        'status',
        'assigned_operator_id',
        'production_cost',
        'operator_notes',
        'completed_at',
    ];

    protected $casts = [
        'status' => ProductionStatus::class,
        'production_cost' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_operator_id');
    }
}
