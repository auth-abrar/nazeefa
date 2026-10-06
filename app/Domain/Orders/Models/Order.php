<?php

namespace App\Domain\Orders\Models;

use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductVariant;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_id',
        'coupon_id',
        'guest_name',
        'guest_phone',
        'guest_email',
        'status',
        'items_subtotal',
        'shipping_amount',
        'discount_amount',
        'grand_total',
        'currency',
        'payment_method',
        'payment_status',
        'courier_provider',
        'courier_tracking_code',
        'customer_notes',
        'internal_notes',
        'ip_address',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'items_subtotal' => 'integer',
        'shipping_amount' => 'integer',
        'discount_amount' => 'integer',
        'grand_total' => 'integer',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(OrderAddress::class);
    }

    public function getFormattedGrandTotalAttribute(): string
    {
        return '৳ ' . number_format($this->grand_total / 100, 0);
    }
}
