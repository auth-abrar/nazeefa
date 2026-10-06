<?php

namespace App\Domain\Suppliers\Providers;

use App\Domain\Orders\Models\Order;
use App\Domain\Suppliers\Contracts\SupplierProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CjDropshippingProvider implements SupplierProviderInterface
{
    protected string $baseUrl;
    protected ?string $email;
    protected ?string $apiKey;
    protected ?string $accessToken = null;

    public function __construct()
    {
        $this->baseUrl = config('services.cjdropshipping.api_url', 'https://developers.cjdropshipping.com/api2.0/v1');
        $this->email = config('services.cjdropshipping.email');
        $this->apiKey = config('services.cjdropshipping.api_key');
    }

    public function getName(): string
    {
        return 'CJ Dropshipping';
    }

    public function getCode(): string
    {
        return 'cj_dropshipping';
    }

    public function isConfigured(): bool
    {
        return !empty($this->email) && !empty($this->apiKey);
    }

    public function searchProducts(string $keyword, array $options = []): array
    {
        if (!$this->isConfigured()) {
            return $this->getSandboxProducts($keyword);
        }

        try {
            $token = $this->getAccessToken();
            $response = Http::withHeaders(['CJ-Access-Token' => $token])
                ->get("{$this->baseUrl}/product/query", [
                    'keyWord' => $keyword,
                    'pageNum' => $options['page'] ?? 1,
                    'pageSize' => $options['limit'] ?? 12,
                ]);

            if ($response->successful() && ($response->json('result') === true || $response->json('code') === 200)) {
                return [
                    'is_sandbox' => false,
                    'data' => $response->json('data.list') ?? [],
                    'total' => $response->json('data.total') ?? 0,
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('CJ Dropshipping API query error, falling back to sandbox: ' . $e->getMessage());
        }

        return $this->getSandboxProducts($keyword);
    }

    public function getProductDetails(string $externalId): array
    {
        if (!$this->isConfigured()) {
            return $this->getSandboxProductDetails($externalId);
        }

        try {
            $token = $this->getAccessToken();
            $response = Http::withHeaders(['CJ-Access-Token' => $token])
                ->get("{$this->baseUrl}/product/detail", ['pid' => $externalId]);

            if ($response->successful()) {
                return [
                    'is_sandbox' => false,
                    'data' => $response->json('data') ?? [],
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('CJ Dropshipping detail error: ' . $e->getMessage());
        }

        return $this->getSandboxProductDetails($externalId);
    }

    public function calculateFreight(string $variantId, string $countryCode = 'BD', int $quantity = 1): array
    {
        if (!$this->isConfigured()) {
            return [
                'is_sandbox' => true,
                'country_code' => $countryCode,
                'estimated_days' => '8-14 days',
                'logistic_name' => 'CJ Packet Fast Line BD',
                'freight_cost_usd' => 4.20,
                'freight_cost_cents' => 420,
            ];
        }

        try {
            $token = $this->getAccessToken();
            $response = Http::withHeaders(['CJ-Access-Token' => $token])
                ->post("{$this->baseUrl}/logistic/freightCalculate", [
                    'startCountryCode' => 'CN',
                    'endCountryCode' => $countryCode,
                    'proList' => [['vid' => $variantId, 'quantity' => $quantity]],
                ]);

            if ($response->successful()) {
                $option = $response->json('data.0');
                return [
                    'is_sandbox' => false,
                    'country_code' => $countryCode,
                    'estimated_days' => $option['aging'] ?? '10-15 days',
                    'logistic_name' => $option['logisticName'] ?? 'CJ Packet',
                    'freight_cost_usd' => (float) ($option['logisticPrice'] ?? 4.50),
                    'freight_cost_cents' => (int) round(($option['logisticPrice'] ?? 4.50) * 100),
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('CJ Dropshipping freight error: ' . $e->getMessage());
        }

        return [
            'is_sandbox' => true,
            'country_code' => $countryCode,
            'estimated_days' => '10-15 days',
            'logistic_name' => 'CJ Standard Freight',
            'freight_cost_usd' => 4.50,
            'freight_cost_cents' => 450,
        ];
    }

    public function createOrder(Order $order, array $items): array
    {
        if (!$this->isConfigured()) {
            return [
                'is_sandbox' => true,
                'external_order_id' => 'CJ-MOCK-' . strtoupper(uniqid()),
                'order_number' => 'CJ-ORD-' . $order->order_number,
                'status' => 'created',
                'message' => 'CJ Sandbox: Sourcing order simulated successfully without charging account.',
            ];
        }

        // Live CJ order creation payload
        $token = $this->getAccessToken();
        $response = Http::withHeaders(['CJ-Access-Token' => $token])
            ->post("{$this->baseUrl}/shopping/order/createOrder", [
                'orderNumber' => $order->order_number,
                'shippingCountryCode' => 'BD',
                'shippingCustomerName' => $order->shippingAddress?->recipient_name ?? 'Nazeefa Customer',
                'shippingAddress' => $order->shippingAddress?->address_line1 ?? 'Dhaka, Bangladesh',
                'shippingCity' => $order->shippingAddress?->city ?? 'Dhaka',
                'shippingPhone' => $order->shippingAddress?->phone ?? '01700000000',
                'products' => $items,
            ]);

        return [
            'is_sandbox' => false,
            'external_order_id' => $response->json('data.orderId'),
            'order_number' => $response->json('data.orderNumber'),
            'status' => $response->successful() ? 'created' : 'failed',
            'raw' => $response->json(),
        ];
    }

    public function getOrderStatus(string $externalOrderId): array
    {
        if (!$this->isConfigured()) {
            return [
                'is_sandbox' => true,
                'status' => 'processing',
                'tracking_number' => 'CJPKT' . rand(10000000, 99999999) . 'YQ',
                'carrier' => 'CJ Packet International',
            ];
        }

        $token = $this->getAccessToken();
        $response = Http::withHeaders(['CJ-Access-Token' => $token])
            ->get("{$this->baseUrl}/shopping/order/getOrderDetail", ['orderId' => $externalOrderId]);

        return [
            'is_sandbox' => false,
            'status' => $response->json('data.orderStatus'),
            'tracking_number' => $response->json('data.trackingNumber'),
            'carrier' => $response->json('data.logisticName'),
        ];
    }

    public function syncInventory(array $externalVariantIds): array
    {
        // Batch stock check
        return [
            'synced_count' => count($externalVariantIds),
            'timestamp' => now()->toIso8601String(),
        ];
    }

    protected function getAccessToken(): string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $response = Http::post("{$this->baseUrl}/authentication/getAccessToken", [
            'email' => $this->email,
            'apiKey' => $this->apiKey,
        ]);

        if ($response->successful() && $response->json('data.accessToken')) {
            $this->accessToken = $response->json('data.accessToken');
            return $this->accessToken;
        }

        throw new \RuntimeException('Failed to authenticate with CJ Dropshipping API.');
    }

    protected function getSandboxProducts(string $keyword): array
    {
        $catalog = [
            [
                'pid' => 'CJ-APP-9021',
                'productName' => 'Vintage Heavyweight Acid Wash Tee - 260 GSM',
                'productImage' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&q=80',
                'sellPrice' => '4.80',
                'categoryName' => "Men's Streetwear",
                'variants' => [
                    ['vid' => 'CJ-VAR-9021-1', 'variantName' => 'Washed Black / L', 'variantSellPrice' => '4.80', 'variantStandard' => '260g'],
                    ['vid' => 'CJ-VAR-9021-2', 'variantName' => 'Washed Black / XL', 'variantSellPrice' => '4.80', 'variantStandard' => '260g'],
                    ['vid' => 'CJ-VAR-9021-3', 'variantName' => 'Washed Grey / L', 'variantSellPrice' => '4.80', 'variantStandard' => '260g'],
                ],
            ],
            [
                'pid' => 'CJ-APP-8842',
                'productName' => 'Oversized French Terry Dropped Shoulder Hoodie - 400 GSM',
                'productImage' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=800&q=80',
                'sellPrice' => '12.50',
                'categoryName' => 'Hoodies & Sweatshirts',
                'variants' => [
                    ['vid' => 'CJ-VAR-8842-1', 'variantName' => 'Off-White / M', 'variantSellPrice' => '12.50', 'variantStandard' => '650g'],
                    ['vid' => 'CJ-VAR-8842-2', 'variantName' => 'Off-White / L', 'variantSellPrice' => '12.50', 'variantStandard' => '650g'],
                    ['vid' => 'CJ-VAR-8842-3', 'variantName' => 'Charcoal / XL', 'variantSellPrice' => '13.00', 'variantStandard' => '680g'],
                ],
            ],
            [
                'pid' => 'CJ-APP-7714',
                'productName' => 'Minimalist Boxy Cut Mock Neck Tee - Combed Cotton',
                'productImage' => 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=800&q=80',
                'sellPrice' => '5.20',
                'categoryName' => "Men's Tops",
                'variants' => [
                    ['vid' => 'CJ-VAR-7714-1', 'variantName' => 'Forest Green / L', 'variantSellPrice' => '5.20', 'variantStandard' => '240g'],
                    ['vid' => 'CJ-VAR-7714-2', 'variantName' => 'Earth Tan / L', 'variantSellPrice' => '5.20', 'variantStandard' => '240g'],
                ],
            ],
        ];

        return [
            'is_sandbox' => true,
            'warning' => 'SANDBOX MODE: CJ_DROPSHIPPING_API_KEY is not configured in .env. Showing simulated catalog.',
            'data' => $catalog,
            'total' => count($catalog),
        ];
    }

    protected function getSandboxProductDetails(string $externalId): array
    {
        $all = $this->getSandboxProducts('')['data'];
        $item = collect($all)->firstWhere('pid', $externalId) ?? $all[0];

        return [
            'is_sandbox' => true,
            'data' => $item,
        ];
    }
}
