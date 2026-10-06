<?php

namespace App\Domain\Suppliers\Models;

use App\Domain\Suppliers\Contracts\SupplierProviderInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'code',
        'provider_class',
        'api_endpoint',
        'api_key',
        'api_secret',
        'access_token',
        'access_token_expires_at',
        'is_active',
        'reliability_rating',
        'lead_time_days',
        'config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
        'access_token_expires_at' => 'datetime',
        'reliability_rating' => 'float',
        'lead_time_days' => 'integer',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(SupplierProduct::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(SupplierOrder::class);
    }

    public function getProvider(): ?SupplierProviderInterface
    {
        if (class_exists($this->provider_class)) {
            return app($this->provider_class);
        }

        return null;
    }
}
