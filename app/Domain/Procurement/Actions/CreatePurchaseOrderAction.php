<?php

namespace App\Domain\Procurement\Actions;

use App\Domain\Inventory\Models\Warehouse;
use App\Domain\Procurement\Models\PurchaseOrder;
use App\Domain\Procurement\Models\PurchaseOrderItem;
use App\Domain\Suppliers\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreatePurchaseOrderAction
{
    public function execute(array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($data) {
            $supplier = Supplier::findOrFail($data['supplier_id']);
            $warehouse = Warehouse::firstOrCreate(
                ['code' => 'WH-DHK-CENTRAL'],
                ['name' => 'Dhaka Central Distribution Hub', 'location' => 'Tejgaon, Dhaka']
            );

            $poNumber = 'PO-' . date('Y') . '-' . strtoupper(Str::random(5));
            $items = $data['items'] ?? [];

            $totalCostCents = 0;
            foreach ($items as $item) {
                $qty = (int) $item['quantity_ordered'];
                $unitCost = (int) $item['unit_cost_cents'];
                $totalCostCents += ($qty * $unitCost);
            }

            // Freight and customs duties estimation
            $freightCents = (int) ($data['shipping_freight_cents'] ?? 35000); // Default $350
            $customsDutyCents = (int) ($data['customs_duty_cents'] ?? round($totalCostCents * 0.15)); // 15% import duty

            $po = PurchaseOrder::create([
                'supplier_id' => $supplier->id,
                'warehouse_id' => $warehouse->id,
                'po_number' => $poNumber,
                'status' => 'draft',
                'currency' => $data['currency'] ?? 'USD',
                'total_cost_cents' => $totalCostCents,
                'shipping_freight_cents' => $freightCents,
                'customs_duty_cents' => $customsDutyCents,
                'payment_terms' => $data['payment_terms'] ?? '30_70_milestone',
                'port_of_entry' => $data['port_of_entry'] ?? 'Chittagong Sea Port',
                'estimated_arrival_at' => now()->addDays($supplier->lead_time_days ?: 18),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $qty = (int) $item['quantity_ordered'];
                $unitCost = (int) $item['unit_cost_cents'];

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'item_name' => $item['item_name'],
                    'external_sku' => $item['external_sku'] ?? null,
                    'quantity_ordered' => $qty,
                    'quantity_received' => 0,
                    'unit_cost_cents' => $unitCost,
                    'total_cost_cents' => $qty * $unitCost,
                ]);
            }

            return $po->load(['supplier', 'warehouse', 'items']);
        });
    }
}
