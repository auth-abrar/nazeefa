<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Inventory\Actions\AdjustInventoryAction;
use App\Domain\Inventory\Models\Warehouse;
use App\Domain\Procurement\Actions\CreatePurchaseOrderAction;
use App\Domain\Procurement\Models\PurchaseOrder;
use App\Domain\Suppliers\Models\Supplier;
use App\Domain\Suppliers\Providers\AlibabaProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProcurementAdminController
{
    public function __construct(
        protected AlibabaProvider $alibabaProvider,
        protected CreatePurchaseOrderAction $createPoAction,
        protected AdjustInventoryAction $adjustInventoryAction
    ) {}

    public function index(): Response
    {
        $supplier = Supplier::firstOrCreate(
            ['code' => 'alibaba'],
            [
                'name' => 'Alibaba Global B2B',
                'provider_class' => AlibabaProvider::class,
                'api_endpoint' => 'https://openapi.alibaba.com',
                'is_active' => true,
                'reliability_rating' => 4.80,
                'lead_time_days' => 18,
                'config' => ['exchange_rate' => 122.00],
            ]
        );

        $warehouses = Warehouse::all();
        $purchaseOrders = PurchaseOrder::with(['supplier', 'warehouse', 'items'])
            ->latest()
            ->paginate(15);

        // Compute KPIs
        $activePos = PurchaseOrder::whereIn('status', ['submitted', 'deposit_paid', 'in_production', 'in_transit', 'customs_clearance'])->get();
        $inTransitValueUsd = $activePos->sum('total_cost_cents') / 100;
        $inTransitValueBdt = $inTransitValueUsd * 122.00;

        // Alibaba bulk blank listings
        $bulkCatalog = $this->alibabaProvider->searchProducts('');

        return Inertia::render('admin/ProcurementIndex', [
            'purchaseOrders' => $purchaseOrders,
            'bulkCatalog' => $bulkCatalog,
            'warehouses' => $warehouses,
            'supplier' => $supplier,
            'kpis' => [
                'active_pos_count' => $activePos->count(),
                'in_transit_usd' => round($inTransitValueUsd, 2),
                'in_transit_bdt' => round($inTransitValueBdt),
                'customs_pending_count' => $activePos->where('status', 'customs_clearance')->count(),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'currency' => ['nullable', 'string', 'max:3'],
            'payment_terms' => ['required', 'in:30_70_milestone,100_advance,net_30'],
            'port_of_entry' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string'],
            'items.*.quantity_ordered' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost_cents' => ['required', 'integer', 'min:1'],
            'items.*.product_variant_id' => ['nullable', 'integer'],
        ]);

        $po = $this->createPoAction->execute($validated);

        return redirect()->back()->with('success', "Purchase Order {$po->po_number} created successfully!");
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:draft,submitted,deposit_paid,in_production,in_transit,customs_clearance,received,cancelled'],
            'tracking_bol_number' => ['nullable', 'string'],
        ]);

        $po = PurchaseOrder::with(['items', 'warehouse'])->findOrFail($id);
        $oldStatus = $po->status;
        $newStatus = $validated['status'];

        $updateData = ['status' => $newStatus];
        if (!empty($validated['tracking_bol_number'])) {
            $updateData['tracking_bol_number'] = $validated['tracking_bol_number'];
        }

        if ($newStatus === 'deposit_paid' && !$po->deposit_paid_at) {
            $updateData['deposit_paid_at'] = now();
        }

        if ($newStatus === 'received') {
            $updateData['actual_received_at'] = now();
            $updateData['balance_paid_at'] = now();

            // Auto-intake items into warehouse inventory
            foreach ($po->items as $item) {
                if ($item->product_variant_id) {
                    $this->adjustInventoryAction->execute(
                        warehouseId: $po->warehouse_id,
                        variantId: $item->product_variant_id,
                        quantityChange: $item->quantity_ordered,
                        type: 'purchase_order_intake',
                        reason: "Bulk shipment intake from PO: {$po->po_number}"
                    );
                    $item->update(['quantity_received' => $item->quantity_ordered]);
                }
            }
        }

        $po->update($updateData);

        return redirect()->back()->with('success', "Purchase Order {$po->po_number} transitioned from {$oldStatus} to {$newStatus}.");
    }
}
