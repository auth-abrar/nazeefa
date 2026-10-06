<?php

namespace App\Domain\Payments\Providers;

use App\Domain\Payments\Contracts\PaymentProviderInterface;

class CodPaymentProvider implements PaymentProviderInterface
{
    public function getIdentifier(): string
    {
        return 'cod';
    }

    public function createPayment(array $paymentData): array
    {
        return [
            'status' => 'success',
            'action' => 'confirm',
            'transaction_id' => 'COD-' . uniqid(),
            'message' => 'Cash on Delivery order registered.',
        ];
    }

    public function verifyPayment(string $transactionId, array $payload = []): array
    {
        return [
            'status' => 'pending_delivery',
            'transaction_id' => $transactionId,
        ];
    }

    public function handleCallback(array $payload): array
    {
        return ['status' => 'success'];
    }

    public function refund(string $transactionId, int $amountCents, string $reason = ''): array
    {
        return ['status' => 'unsupported', 'message' => 'Cash on delivery refunds are processed manually.'];
    }

    public function supportsCurrency(string $currency): bool
    {
        return strtoupper($currency) === 'BDT';
    }

    public function supportsMethod(string $method): bool
    {
        return $method === 'cod';
    }
}
