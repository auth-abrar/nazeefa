<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Suppliers\Actions\CalculateLandedPriceAction;
use App\Domain\Suppliers\Actions\ImportCjProductAction;
use App\Domain\Suppliers\Models\Supplier;
use App\Domain\Suppliers\Models\SupplierOrder;
use App\Domain\Suppliers\Models\SupplierProduct;
use App\Domain\Suppliers\Models\SupplierVariant;
use App\Domain\Suppliers\Providers\CjDropshippingProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SupplierAdminController
{
    public function __construct(
        protected CjDropshippingProvider $cjProvider,
        protected CalculateLandedPriceAction $priceCalculator,
        protected ImportCjProductAction $importAction
    ) {}

    public function index(): Response
    {
        // Ensure default suppliers exist
        $cjSupplier = Supplier::firstOrCreate(
            ['code' => 'cj_dropshipping'],
            [
                'name' => 'CJ Dropshipping',
                'provider_class' => CjDropshippingProvider::class,
                'api_endpoint' => 'https://developers.cjdropshipping.com/api2.0/v1',
                'is_active' => true,
                'reliability_rating' => 4.65,
                'lead_time_days' => 10,
                'config' => ['exchange_rate' => 122.00, 'default_margin' => 0.35],
            ]
        );

        $alibabaSupplier = Supplier::firstOrCreate(
            ['code' => 'alibaba'],
            [
                'name' => 'Alibaba Global B2B',
                'provider_class' => 'App\Domain\Suppliers\Providers\AlibabaProvider',
                'api_endpoint' => 'https://openapi.alibaba.com',
                'is_active' => false,
                'reliability_rating' => 4.40,
                'lead_time_days' => 18,
                'config' => ['exchange_rate' => 122.00, 'default_margin' => 0.40],
            ]
        );

        $suppliers = Supplier::withCount(['products', 'orders'])->get();
        
        $mappedProducts = SupplierProduct::with(['product', 'variants'])
            ->latest()
            ->paginate(15);

        $recentSupplierOrders = SupplierOrder::with(['order', 'supplier'])
            ->latest()
            ->take(10)
            ->get();

        // Sample initial sourcing catalog from CJ Provider
        $initialSourcingCatalog = $this->cjProvider->searchProducts('');

        return Inertia::render('admin/SuppliersIndex', [
            'suppliers' => $suppliers,
            'isCjConfigured' => $this->cjProvider->isConfigured(),
            'mappedProducts' => $mappedProducts,
            'recentSupplierOrders' => $recentSupplierOrders,
            'initialSourcingCatalog' => $initialSourcingCatalog,
            'settings' => [
                'exchange_rate' => 122.00,
                'default_margin' => 35,
            ],
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $keyword = (string) $request->input('keyword', '');
        $results = $this->cjProvider->searchProducts($keyword);

        return response()->json($results);
    }

    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pid' => ['required', 'string'],
            'title' => ['required', 'string'],
            'image_url' => ['required', 'string'],
            'sell_price_usd' => ['required', 'numeric'],
            'estimated_freight_usd' => ['nullable', 'numeric'],
            'target_margin' => ['nullable', 'numeric'],
            'exchange_rate' => ['nullable', 'numeric'],
            'variants' => ['required', 'array'],
        ]);

        $cjSupplier = Supplier::where('code', 'cj_dropshipping')->firstOrFail();

        $costCents = (int) round($validated['sell_price_usd'] * 100);
        $freightCents = (int) round(($validated['estimated_freight_usd'] ?? 4.20) * 100);

        // Record or find SupplierProduct
        $supplierProduct = SupplierProduct::firstOrCreate(
            [
                'supplier_id' => $cjSupplier->id,
                'external_product_id' => $validated['pid'],
            ],
            [
                'title' => $validated['title'],
                'image_url' => $validated['image_url'],
                'supplier_cost_cents' => $costCents,
                'estimated_freight_cents' => $freightCents,
                'status' => 'draft',
                'raw_payload' => [
                    'variants' => $validated['variants'],
                ],
            ]
        );

        // Record SupplierVariants
        foreach ($validated['variants'] as $v) {
            SupplierVariant::firstOrCreate(
                [
                    'supplier_product_id' => $supplierProduct->id,
                    'external_variant_id' => $v['vid'] ?? ('VAR-' . uniqid()),
                ],
                [
                    'variant_name' => $v['variantName'] ?? 'Standard / Free Size',
                    'supplier_cost_cents' => (int) round(($v['variantSellPrice'] ?? $validated['sell_price_usd']) * 100),
                    'supplier_stock' => 100,
                    'weight_grams' => 350,
                ]
            );
        }

        // Execute import into Catalog
        $this->importAction->execute(
            $supplierProduct->load('variants', 'supplier'),
            [
                'target_margin' => ($validated['target_margin'] ?? 35) / 100,
                'exchange_rate' => $validated['exchange_rate'] ?? 122.00,
            ]
        );

        return redirect()->back()->with('success', "Product '{$validated['title']}' successfully imported into Nazeefa Catalog as draft!");
    }

    public function syncStock(Request $request): JsonResponse
    {
        $mappedVariants = SupplierVariant::whereNotNull('product_variant_id')->pluck('external_variant_id')->toArray();
        $result = $this->cjProvider->syncInventory($mappedVariants);

        return response()->json([
            'status' => 'success',
            'message' => 'Stock inventory synchronised with CJ Dropshipping.',
            'details' => $result,
        ]);
    }
}
