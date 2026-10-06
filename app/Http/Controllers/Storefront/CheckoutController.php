<?php

namespace App\Http\Controllers\Storefront;

use App\Domain\Catalog\Models\ProductVariant;
use App\Domain\Orders\Actions\CreateOrderAction;
use App\Domain\Orders\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController
{
    public function show(Request $request): Response
    {
        return Inertia::render('checkout/Checkout');
    }

    public function store(Request $request, CreateOrderAction $createOrderAction): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^(?:\+?880|0)1[3-9]\d{8}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'district' => ['required', 'string', 'max:50'],
            'thana_area' => ['nullable', 'string', 'max:80'],
            'street_address' => ['required', 'string', 'max:255'],
            'landmark' => ['nullable', 'string', 'max:150'],
            'payment_method' => ['required', 'in:cod,bkash,nagad'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.variant_id' => ['required', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $order = $createOrderAction->execute($validated);

        return redirect()->route('checkout.confirmation', ['order_number' => $order->order_number]);
    }

    public function confirmation(string $orderNumber): Response
    {
        $order = Order::with(['items.product', 'address'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return Inertia::render('checkout/Confirmation', [
            'order' => $order,
        ]);
    }
}
