<?php

namespace App\Domain\Suppliers\Providers;

use App\Domain\Orders\Models\Order;
use App\Domain\Suppliers\Contracts\SupplierProviderInterface;
use Illuminate\Support\Facades\Log;

class AlibabaProvider implements SupplierProviderInterface
{
    protected string $baseUrl;
    protected ?string $appKey;
    protected ?string $appSecret;

    public function __construct()
    {
        $this->baseUrl = config('services.alibaba.api_url', 'https://openapi.alibaba.com');
        $this->appKey = config('services.alibaba.app_key');
        $this->appSecret = config('services.alibaba.app_secret');
    }

    public function getName(): string
    {
        return 'Alibaba Global B2B';
    }

    public function getCode(): string
    {
        return 'alibaba';
    }

    public function isConfigured(): bool
    {
        return !empty($this->appKey) && !empty($this->appSecret);
    }

    public function searchProducts(string $keyword, array $options = []): array
    {
        // When unconfigured, return realistic high-grade textile & blank manufacturing fixtures
        return $this->getSandboxWholesaleListings($keyword);
    }

    public function getProductDetails(string $externalId): array
    {
        $listings = $this->getSandboxWholesaleListings('')['data'];
        $item = collect($listings)->firstWhere('item_id', $externalId) ?? $listings[0];

        return [
            'is_sandbox' => true,
            'data' => $item,
        ];
    }

    public function calculateFreight(string $variantId, string $countryCode = 'BD', int $quantity = 500): array
    {
        // Bulk sea freight / air freight quote calculations to Bangladesh
        $unitWeightKg = 0.45;
        $totalWeightKg = $quantity * $unitWeightKg;

        $seaRatePerKg = 0.85; // $0.85/kg to Chittagong Port
        $seaFreightUsd = max(250.0, $totalWeightKg * $seaRatePerKg);

        return [
            'is_sandbox' => true,
            'destination' => 'Chittagong Sea Port (BDCGP)',
            'method' => 'Ocean LCL (Less than Container Load)',
            'transit_days' => '18-24 days',
            'total_weight_kg' => round($totalWeightKg, 1),
            'freight_cost_usd' => round($seaFreightUsd, 2),
            'freight_cost_cents' => (int) round($seaFreightUsd * 100),
            'air_freight_alternative_usd' => round($totalWeightKg * 4.50, 2), // Air cargo to Dhaka Airport
        ];
    }

    public function createOrder(Order $order, array $items): array
    {
        return [
            'is_sandbox' => true,
            'trade_assurance_id' => 'ALIBABA-TA-' . rand(1000000, 9999999),
            'status' => 'draft_inquiry',
            'message' => 'Alibaba B2B Trade Assurance inquiry draft created.',
        ];
    }

    public function getOrderStatus(string $externalOrderId): array
    {
        return [
            'is_sandbox' => true,
            'status' => 'in_production',
            'milestone' => 'Fabric Dyeing & Cutting',
            'inspection_status' => 'Pending pre-shipment QA',
        ];
    }

    public function syncInventory(array $externalVariantIds): array
    {
        return [
            'synced_count' => count($externalVariantIds),
            'timestamp' => now()->toIso8601String(),
        ];
    }

    protected function getSandboxWholesaleListings(string $keyword): array
    {
        $catalog = [
            [
                'item_id' => 'ALI-B2B-101',
                'title' => 'Custom 280 GSM Heavy French Terry Cotton Hoodie Blanks',
                'supplier_name' => 'Ningbo Master Garment Co., Ltd.',
                'verified_supplier' => true,
                'image_url' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=800&q=80',
                'moq' => 100,
                'lead_time_days' => 18,
                'tiers' => [
                    ['min_qty' => 100, 'price_usd' => 6.50],
                    ['min_qty' => 500, 'price_usd' => 5.20],
                    ['min_qty' => 1000, 'price_usd' => 4.40],
                ],
                'certifications' => ['OEKO-TEX Standard 100', 'BSCI Audited'],
                'sample_cost_usd' => 25.00,
            ],
            [
                'item_id' => 'ALI-B2B-102',
                'title' => '100% Combed Compact Cotton 220 GSM Single Jersey T-Shirt Fabric Rolls',
                'supplier_name' => 'Shaoxing Keqiao Textile Mill Ltd.',
                'verified_supplier' => true,
                'image_url' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&q=80',
                'moq' => 200,
                'lead_time_days' => 14,
                'tiers' => [
                    ['min_qty' => 200, 'price_usd' => 4.80],
                    ['min_qty' => 500, 'price_usd' => 3.90],
                    ['min_qty' => 1000, 'price_usd' => 3.20],
                ],
                'certifications' => ['GOTS Organic Cotton', 'ISO 9001'],
                'sample_cost_usd' => 15.00,
            ],
            [
                'item_id' => 'ALI-B2B-103',
                'title' => 'Vintage Oversized Drop-Shoulder Acid Washed Streetwear Blanks',
                'supplier_name' => 'Guangdong Apparel Craftsmanship Co.',
                'verified_supplier' => true,
                'image_url' => 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=800&q=80',
                'moq' => 50,
                'lead_time_days' => 21,
                'tiers' => [
                    ['min_qty' => 50, 'price_usd' => 4.20],
                    ['min_qty' => 200, 'price_usd' => 3.40],
                    ['min_qty' => 500, 'price_usd' => 2.80],
                ],
                'certifications' => ['Sedex SMETA'],
                'sample_cost_usd' => 20.00,
            ],
        ];

        return [
            'is_sandbox' => true,
            'warning' => 'SANDBOX MODE: Alibaba Trade Assurance B2B wholesale listings simulated with verified MOQ tiers.',
            'data' => $catalog,
            'total' => count($catalog),
        ];
    }
}
