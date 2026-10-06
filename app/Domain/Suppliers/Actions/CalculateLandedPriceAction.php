<?php

namespace App\Domain\Suppliers\Actions;

class CalculateLandedPriceAction
{
    /**
     * Calculate Landed Cost in BDT and Recommended Retail Price.
     * All monetary inputs in USD cents or BDT poisha.
     */
    public function execute(
        int $supplierCostCents,
        int $estimatedFreightCents = 0,
        float $exchangeRate = 122.00,
        float $customsBuffer = 0.10,
        float $gatewayFee = 0.025,
        float $targetMargin = 0.35
    ): array {
        $costUsd = $supplierCostCents / 100;
        $freightUsd = $estimatedFreightCents / 100;
        $totalUsd = $costUsd + $freightUsd;

        // Base Landed BDT: (Wholesale + Freight) * Rate * (1 + customs buffer)
        $landedBdt = $totalUsd * $exchangeRate * (1 + $customsBuffer);
        
        // Retail Price Formula: Landed / (1 - targetMargin - gatewayFee)
        $divisor = max(0.1, 1 - ($targetMargin + $gatewayFee));
        $rawRetailBdt = $landedBdt / $divisor;

        // Round to psychologically appealing retail price (e.g. 1450, 1890, 2250)
        $roundedRetailBdt = $this->roundToCleanPrice($rawRetailBdt);
        
        $profitBdt = $roundedRetailBdt - $landedBdt - ($roundedRetailBdt * $gatewayFee);
        $effectiveMargin = $roundedRetailBdt > 0 ? ($profitBdt / $roundedRetailBdt) * 100 : 0;

        return [
            'supplier_cost_usd' => round($costUsd, 2),
            'freight_usd' => round($freightUsd, 2),
            'total_usd' => round($totalUsd, 2),
            'exchange_rate' => $exchangeRate,
            'landed_cost_bdt' => (int) round($landedBdt),
            'recommended_retail_bdt' => (int) $roundedRetailBdt,
            'recommended_retail_poisha' => (int) round($roundedRetailBdt * 100),
            'gross_profit_bdt' => (int) round($profitBdt),
            'effective_margin_percent' => round($effectiveMargin, 1),
        ];
    }

    private function roundToCleanPrice(float $price): float
    {
        if ($price <= 500) {
            return ceil($price / 10) * 10;
        }
        if ($price <= 2000) {
            // Round to nearest 50 (e.g. 1450, 1500)
            return ceil($price / 50) * 50;
        }
        // Round to nearest 90 or 50 (e.g. 2490, 2950)
        $base = ceil($price / 100) * 100;
        return $base > $price ? $base - 10 : $base;
    }
}
