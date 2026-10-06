<?php

namespace App\Domain\Payments\Providers;

use App\Domain\Payments\Contracts\PaymentProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SslcommerzPaymentProvider implements PaymentProviderInterface
{
    protected string $storeId;
    protected string $storePassword;
    protected bool $isSandbox;

    public function __construct()
    {
        $this->storeId = env('SSLCOMMERZ_STORE_ID', '');
        $this->storePassword = env('SSLCOMMERZ_STORE_PASSWORD', '');
        $this->isSandbox = (bool) env('SSLCOMMERZ_IS_SANDBOX', true);
    }

    public function getIdentifier(): string
    {
        return 'sslcommerz';
    }

    protected function getApiBaseUrl(): string
    {
        return $this->isSandbox
            ? 'https://sandbox.sslcommerz.com'
            : 'https://securepay.sslcommerz.com';
    }

    public function isConfigured(): bool
    {
        return !empty($this->storeId) && !empty($this->storePassword);
    }

    public function createPayment(array $paymentData): array
    {
        if (!$this->isConfigured()) {
            return [
                'status' => 'mock_sandbox',
                'redirect_url' => url('/payment/callback/sslcommerz?mock=1&tran_id=' . $paymentData['tran_id']),
                'message' => 'SSLCOMMERZ credentials unconfigured. Simulating sandbox response.',
            ];
        }

        $postData = [
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'total_amount' => $paymentData['amount'] / 100, // converted to decimal BDT
            'currency' => 'BDT',
            'tran_id' => $paymentData['tran_id'],
            'success_url' => route('payment.callback', ['provider' => 'sslcommerz']),
            'fail_url' => route('payment.callback', ['provider' => 'sslcommerz']),
            'cancel_url' => route('payment.cancel', ['provider' => 'sslcommerz']),
            'ipn_url' => route('webhooks.payment', ['provider' => 'sslcommerz']),
            'cus_name' => $paymentData['customer_name'],
            'cus_email' => $paymentData['customer_email'] ?? 'customer@nazeefa.com',
            'cus_add1' => $paymentData['address'],
            'cus_city' => $paymentData['city'] ?? 'Dhaka',
            'cus_country' => 'Bangladesh',
            'cus_phone' => $paymentData['customer_phone'],
            'shipping_method' => 'COURIER',
            'product_name' => 'Nazeefa Apparel',
            'product_category' => 'Clothing',
            'product_profile' => 'physical-goods',
        ];

        $response = Http::asForm()->post($this->getApiBaseUrl() . '/gwprocess/v4/api.php', $postData);

        if ($response->successful()) {
            $data = $response->json();
            if (($data['status'] ?? '') === 'SUCCESS') {
                return [
                    'status' => 'success',
                    'redirect_url' => $data['GatewayPageURL'],
                    'sessionkey' => $data['sessionkey'] ?? null,
                ];
            }
        }

        Log::error('SSLCOMMERZ Session Creation Failed', ['response' => $response->body()]);

        return [
            'status' => 'failed',
            'message' => 'Unable to connect to payment gateway.',
        ];
    }

    public function verifyPayment(string $transactionId, array $payload = []): array
    {
        if (!$this->isConfigured() && ($payload['mock'] ?? false)) {
            return [
                'status' => 'VALID',
                'amount' => $payload['amount'] ?? 0,
                'bank_tran_id' => 'MOCK-BANK-' . uniqid(),
                'card_type' => 'VISA-Mock',
            ];
        }

        $valId = $payload['val_id'] ?? null;
        if (!$valId) {
            return ['status' => 'INVALID', 'message' => 'Missing validation ID.'];
        }

        $validationUrl = $this->getApiBaseUrl() . "/validator/api/merchantTransIDvalidationAPI.php";

        $response = Http::get($validationUrl, [
            'val_id' => $valId,
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'format' => 'json',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (($data['status'] ?? '') === 'VALID' || ($data['status'] ?? '') === 'VALIDATED') {
                return [
                    'status' => 'VALID',
                    'amount' => (int) round(((float) $data['amount']) * 100),
                    'bank_tran_id' => $data['bank_tran_id'] ?? null,
                    'card_type' => $data['card_type'] ?? null,
                ];
            }
        }

        return ['status' => 'FAILED'];
    }

    public function handleCallback(array $payload): array
    {
        return $this->verifyPayment($payload['tran_id'] ?? '', $payload);
    }

    public function refund(string $transactionId, int $amountCents, string $reason = ''): array
    {
        return ['status' => 'pending_manual_review'];
    }

    public function supportsCurrency(string $currency): bool
    {
        return in_array(strtoupper($currency), ['BDT', 'USD']);
    }

    public function supportsMethod(string $method): bool
    {
        return in_array($method, ['sslcommerz', 'card', 'mobile_banking']);
    }
}
