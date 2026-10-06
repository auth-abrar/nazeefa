<?php

namespace App\Domain\Fulfillment\Contracts;

interface CourierProviderInterface
{
    public function getIdentifier(): string;

    public function checkServiceability(string $district, string $thana): bool;

    public function getQuote(string $district, int $weightGrams, int $codAmount = 0): array;

    public function createShipment(array $shipmentData): array;

    public function cancelShipment(string $trackingCode): bool;

    public function trackShipment(string $trackingCode): array;

    public function handleWebhook(array $payload): array;
}
