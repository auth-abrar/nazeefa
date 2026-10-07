<?php

namespace App\Http\Controllers\Storefront;

use App\Domain\Orders\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderTrackingController
{
    public function index(Request $request)
    {
        $order = null;

        if ($trackingNumber = $request->input('tracking_number')) {
            try {
                $order = Order::with(['items.variant.product', 'shipments.events'])
                    ->where('order_number', $trackingNumber)
                    ->orWhere('tracking_number', $trackingNumber)
                    ->first();
            } catch (\Throwable $e) {
                // Return gracefully if table is not yet populated
            }
        }

        return Inertia::render('storefront/TrackOrder', [
            'order' => $order,
            'searchedNumber' => $request->input('tracking_number'),
        ]);
    }
}
