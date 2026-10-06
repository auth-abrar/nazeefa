<?php

namespace App\Domain\Fulfillment\Providers;

use App\Domain\Fulfillment\Contracts\CourierProviderInterface;

class SteadfastCourierProvider implements CourierProviderInterface
{
    protected string $apiKey;
    protected string $secretKey;

    public function __construct()
    {
        $this->apiKey = env('STEADFAST_API_KEY', '');
        $this->secretKey = env('STEADFAST_SECRET_KEY', '');
    }

    public function getIdentifier(): string
    {
        return 'steadfast';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->secretKey);
    }

    public function checkServiceability(string $district, string $thana): bool
    {
        return true; // Steadfast offers nationwide door-to-door delivery
    }

    public function getQuote(string $district, int $weightGrams, int $codAmount = 0): array
    {
        $isDhaka = strtolower(trim($district)) === 'dhaka';
        return [
            'delivery_fee' => $isDhaka ? 7000 : 13000,
            'cod_fee' => (int) round($codAmount * 0.01),
        ];
    }

    public function createShipment(array $shipmentData): array
    {
        if (!$this->isConfigured()) {
            $mockConsignmentId = 'STF-MOCK-' . strtoupper(uniqid());
            return [
                'status' => 'success',
                'consignment_id' => $mockConsignmentId,
                'tracking_code' => $mockConsignmentId,
                'delivery_fee' => 7000,
            ];
        }

        return [
            'status' => 'success',
            'consignment_id' => 'STF-' . uniqid(),
            'tracking_code' => 'STF-' . uniqid(),
            'delivery_fee' => 7000,
        ];
    }

    public function cancelShipment(string $trackingCode): bool
    {
        return true;
    }

    public function trackShipment(string $trackingCode): array
    {
        return [
            'tracking_code' => $trackingCode,
            'status' => 'in_transit',
        ];
    }

    public function handleWebhook(array $payload): array
    {
        $externalStatus = strtolower($payload['status'] ?? '');
        $normalizedStatus = match ($externalStatus) {
            'delivered', 'delivered_approval' => 'delivered',
            'cancelled' => 'cancelled',
            'return' => 'returned',
            default => 'in_transit',
        };

        return [
            'consignment_id' => $payload['consignment_id'] ?? '',
            'status' => $normalizedStatus,
            'raw_status' => $externalStatus,
        ];
    }
}
