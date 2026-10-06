<?php

namespace App\Http\Controllers\Storefront;

use App\Domain\Orders\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderTrackingController
{
    public function show(Request $request): Response
    {
        $orderNumber = $request->input('order_number');
        $phone = $request->input('phone');
        $order = null;

        if ($orderNumber) {
            $query = Order::with(['items.product', 'address'])
                ->where('order_number', trim($orderNumber));

            if ($phone) {
                $query->where('guest_phone', 'like', '%' . substr(trim($phone), -10));
            }

            $order = $query->first();
        }

        return Inertia::render('storefront/TrackOrder', [
            'searchedOrder' => $order,
            'filters' => [
                'order_number' => $orderNumber,
                'phone' => $phone,
            ],
        ]);
    }
}
