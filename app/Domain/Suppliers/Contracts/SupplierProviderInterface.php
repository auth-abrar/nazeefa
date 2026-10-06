<?php

namespace App\Domain\Suppliers\Contracts;

interface SupplierProviderInterface
{
    public function getIdentifier(): string;

    public function authenticate(): bool;

    public function searchProducts(string $keyword, int $page = 1): array;

    public function getProductDetails(string $externalProductId): array;

    public function calculateFreight(string $externalVariantId, int $quantity, string $destinationCountry): array;

    public function placeOrder(array $orderData): array;

    public function getTracking(string $externalOrderId): array;

    public function syncInventory(string $externalVariantId): int;
}
