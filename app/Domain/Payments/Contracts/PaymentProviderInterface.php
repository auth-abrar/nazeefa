<?php

namespace App\Domain\Payments\Contracts;

interface PaymentProviderInterface
{
    public function getIdentifier(): string;

    public function createPayment(array $paymentData): array;

    public function verifyPayment(string $transactionId, array $payload = []): array;

    public function handleCallback(array $payload): array;

    public function refund(string $transactionId, int $amountCents, string $reason = ''): array;

    public function supportsCurrency(string $currency): bool;

    public function supportsMethod(string $method): bool;
}
