<?php

namespace App\Domain\Payments\Services;

use App\Domain\Payments\Contracts\PaymentProviderInterface;
use App\Domain\Payments\Providers\BkashPaymentProvider;
use App\Domain\Payments\Providers\CodPaymentProvider;
use App\Domain\Payments\Providers\SslcommerzPaymentProvider;
use InvalidArgumentException;

class PaymentManager
{
    protected array $providers = [];

    public function __construct()
    {
        $this->register('cod', new CodPaymentProvider());
        $this->register('sslcommerz', new SslcommerzPaymentProvider());
        $this->register('bkash', new BkashPaymentProvider());
    }

    public function register(string $name, PaymentProviderInterface $provider): void
    {
        $this->providers[$name] = $provider;
    }

    public function provider(string $name): PaymentProviderInterface
    {
        if (!isset($this->providers[$name])) {
            throw new InvalidArgumentException("Payment provider [{$name}] is not supported.");
        }

        return $this->providers[$name];
    }
}
