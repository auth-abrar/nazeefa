<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Catalog\Models\ProductVariant;
use App\Domain\Inventory\Actions\AdjustInventoryAction;
use App\Domain\Inventory\Models\InventoryItem;
use App\Domain\Inventory\Models\Warehouse;
use App\Enums\StockMovementType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InventoryAdminController
{
    public function index(Request $request): Response
    {
        $variants = ProductVariant::with(['product'])->paginate(25);
        $warehouses = Warehouse::where('is_active', true)->get();

        return Inertia::render('admin/InventoryIndex', [
            'variants' => $variants,
            'warehouses' => $warehouses,
        ]);
    }

    public function adjust(Request $request, AdjustInventoryAction $adjustAction): RedirectResponse
    {
        $validated = $request->validate([
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'quantity_delta' => ['required', 'integer'],
            'type' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $item = InventoryItem::findOrFail($validated['inventory_item_id']);
        $adjustAction->execute(
            $item,
            (int) $validated['quantity_delta'],
            StockMovementType::from($validated['type']),
            $validated['notes'] ?? ''
        );

        return back()->with('success', 'Stock adjustment recorded with audit log.');
    }
}
