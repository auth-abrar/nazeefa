<?php

namespace App\Domain\CRM\Models;

use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\OrderAddress;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'phone',
        'name',
        'email',
        'default_district',
        'total_orders_count',
        'delivered_orders_count',
        'returned_orders_count',
        'total_spent_amount',
        'cod_return_risk_score',
        'risk_tier',
        'tags',
        'admin_notes',
    ];

    protected $casts = [
        'total_orders_count' => 'integer',
        'delivered_orders_count' => 'integer',
        'returned_orders_count' => 'integer',
        'total_spent_amount' => 'integer',
        'cod_return_risk_score' => 'float',
        'tags' => 'array',
    ];

    public function getLtvBdtAttribute(): float
    {
        return $this->total_spent_amount / 100;
    }

    public function isHighRisk(): bool
    {
        return $this->risk_tier === 'high_risk' || $this->cod_return_risk_score >= 0.35;
    }

    public function isVip(): bool
    {
        return $this->risk_tier === 'verified_vip' || $this->total_spent_amount >= 1000000;
    }
}
