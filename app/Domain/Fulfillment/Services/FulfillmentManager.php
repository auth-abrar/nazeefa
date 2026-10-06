<?php

namespace App\Domain\Fulfillment\Services;

use App\Domain\Fulfillment\Contracts\CourierProviderInterface;
use App\Domain\Fulfillment\Providers\PathaoCourierProvider;
use App\Domain\Fulfillment\Providers\SteadfastCourierProvider;
use InvalidArgumentException;

class FulfillmentManager
{
    protected array $providers = [];

    public function __construct()
    {
        $this->register('pathao', new PathaoCourierProvider());
        $this->register('steadfast', new SteadfastCourierProvider());
    }

    public function register(string $name, CourierProviderInterface $provider): void
    {
        $this->providers[$name] = $provider;
    }

    public function provider(string $name): CourierProviderInterface
    {
        if (!isset($this->providers[$name])) {
            throw new InvalidArgumentException("Courier provider [{$name}] is not supported.");
        }

        return $this->providers[$name];
    }
}
