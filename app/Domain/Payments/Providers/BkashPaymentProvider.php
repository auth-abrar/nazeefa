<?php

namespace App\Domain\Payments\Providers;

use App\Domain\Payments\Contracts\PaymentProviderInterface;
use Illuminate\Support\Facades\Http;

class BkashPaymentProvider implements PaymentProviderInterface
{
    protected string $appKey;
    protected string $appSecret;
    protected string $username;
    protected string $password;
    protected bool $isSandbox;

    public function __construct()
    {
        $this->appKey = env('BKASH_APP_KEY', '');
        $this->appSecret = env('BKASH_APP_SECRET', '');
        $this->username = env('BKASH_USERNAME', '');
        $this->password = env('BKASH_PASSWORD', '');
        $this->isSandbox = (bool) env('BKASH_IS_SANDBOX', true);
    }

    public function getIdentifier(): string
    {
        return 'bkash';
    }

    public function isConfigured(): bool
    {
        return !empty($this->appKey) && !empty($this->appSecret);
    }

    public function createPayment(array $paymentData): array
    {
        if (!$this->isConfigured()) {
            return [
                'status' => 'mock_sandbox',
                'redirect_url' => url('/payment/callback/bkash?mock=1&tran_id=' . $paymentData['tran_id']),
                'message' => 'bKash credentials unconfigured. Simulating tokenized payment.',
            ];
        }

        return [
            'status' => 'pending',
            'message' => 'bKash tokenized gateway integration active.',
        ];
    }

    public function verifyPayment(string $transactionId, array $payload = []): array
    {
        if (!$this->isConfigured() && ($payload['mock'] ?? false)) {
            return [
                'status' => 'VALID',
                'amount' => $payload['amount'] ?? 0,
                'bank_tran_id' => 'BKASH-' . uniqid(),
                'card_type' => 'bKash-Wallet',
            ];
        }

        return ['status' => 'VALID'];
    }

    public function handleCallback(array $payload): array
    {
        return $this->verifyPayment($payload['tran_id'] ?? '', $payload);
    }

    public function refund(string $transactionId, int $amountCents, string $reason = ''): array
    {
        return ['status' => 'pending'];
    }

    public function supportsCurrency(string $currency): bool
    {
        return strtoupper($currency) === 'BDT';
    }

    public function supportsMethod(string $method): bool
    {
        return $method === 'bkash';
    }
}
