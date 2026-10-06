<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Fulfillment\Actions\DispatchOrderAction;
use App\Domain\Orders\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderAdminController
{
    public function index(Request $request): Response
    {
        $query = Order::with(['items', 'address', 'items.product'])->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('guest_phone', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        return Inertia::render('admin/OrdersIndex', [
            'orders' => $orders,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function show(int $id): Response
    {
        $order = Order::with(['items.product', 'address'])->findOrFail($id);

        return Inertia::render('admin/OrderDetail', [
            'order' => $order,
        ]);
    }

    public function dispatch(Request $request, int $id, DispatchOrderAction $dispatchAction): RedirectResponse
    {
        $request->validate([
            'courier' => ['required', 'in:pathao,steadfast'],
        ]);

        $order = Order::findOrFail($id);
        $dispatchAction->execute($order, $request->input('courier'));

        return back()->with('success', "Order #{$order->order_number} successfully dispatched via {$request->input('courier')}.");
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'string'],
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->input('status')]);

        return back()->with('success', "Order status updated to {$request->input('status')}.");
    }
}
