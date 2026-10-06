<?php

namespace App\Domain\Suppliers\Actions;

use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductVariant;
use App\Domain\Suppliers\Models\SupplierProduct;
use App\Domain\Suppliers\Models\SupplierVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportCjProductAction
{
    public function __construct(
        protected CalculateLandedPriceAction $calculator
    ) {}

    public function execute(
        SupplierProduct $supplierProduct,
        array $overrideParams = []
    ): Product {
        return DB::transaction(function () use ($supplierProduct, $overrideParams) {
            $defaultCategory = Category::firstOrCreate(
                ['slug' => 'international-dropship'],
                ['name' => 'International Curated', 'description' => 'Sourced garments from global partner suppliers']
            );

            $margin = $overrideParams['target_margin'] ?? 0.35;
            $exchangeRate = $overrideParams['exchange_rate'] ?? 122.00;

            $pricing = $this->calculator->execute(
                supplierCostCents: $supplierProduct->supplier_cost_cents,
                estimatedFreightCents: $supplierProduct->estimated_freight_cents,
                exchangeRate: $exchangeRate,
                targetMargin: $margin
            );

            $retailPricePoisha = $overrideParams['retail_price_poisha'] ?? $pricing['recommended_retail_poisha'];

            // Create or update local catalog product
            $product = Product::create([
                'category_id' => $defaultCategory->id,
                'name' => $supplierProduct->title,
                'slug' => Str::slug($supplierProduct->title) . '-' . Str::random(5),
                'description' => $supplierProduct->raw_payload['description'] ?? 'Sourced premium apparel from CJ Dropshipping international supplier network.',
                'material' => $supplierProduct->raw_payload['material'] ?? 'Heavyweight Cotton Blend',
                'images' => [$supplierProduct->image_url],
                'is_active' => false, // Start as draft for merchant review
                'is_featured' => false,
                'is_customizable' => false,
                'metadata' => [
                    'dropship' => true,
                    'supplier_code' => $supplierProduct->supplier->code,
                    'external_product_id' => $supplierProduct->external_product_id,
                    'landed_cost_bdt' => $pricing['landed_cost_bdt'],
                    'lead_time_days' => $supplierProduct->supplier->lead_time_days,
                ],
            ]);

            // Link supplier product to catalog product
            $supplierProduct->update([
                'product_id' => $product->id,
                'status' => 'mapped',
                'last_synced_at' => now(),
            ]);

            // Import or map variants
            foreach ($supplierProduct->variants as $sVariant) {
                $variantPricing = $this->calculator->execute(
                    supplierCostCents: $sVariant->supplier_cost_cents,
                    estimatedFreightCents: $supplierProduct->estimated_freight_cents,
                    exchangeRate: $exchangeRate,
                    targetMargin: $margin
                );

                $sku = 'CJ-' . strtoupper(Str::random(4)) . '-' . strtoupper(substr($sVariant->variant_name, 0, 3));

                $productVariant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $sku,
                    'size' => $this->parseSize($sVariant->variant_name),
                    'color_name' => $this->parseColor($sVariant->variant_name),
                    'color_hex' => '#1F2937',
                    'price_amount' => $variantPricing['recommended_retail_poisha'],
                    'compare_at_price_amount' => (int) ($variantPricing['recommended_retail_poisha'] * 1.25),
                    'stock_quantity' => min(50, $sVariant->supplier_stock), // Safe buffer
                    'weight_grams' => $sVariant->weight_grams,
                    'barcode' => null,
                ]);

                $sVariant->update([
                    'product_variant_id' => $productVariant->id,
                ]);
            }

            return $product->load('variants');
        });
    }

    private function parseSize(string $variantName): string
    {
        $parts = explode('/', $variantName);
        return trim(end($parts)) ?: 'Free Size';
    }

    private function parseColor(string $variantName): string
    {
        $parts = explode('/', $variantName);
        return trim($parts[0]) ?: 'Default';
    }
}
