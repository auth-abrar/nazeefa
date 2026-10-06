<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Catalog\Models\ProductVariant;
use App\Domain\Orders\Models\Order;
use App\Enums\OrderStatus;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController
{
    public function index(): Response
    {
        $todayStart = now()->startOfDay();

        $todaySalesPoisha = Order::where('created_at', '>=', $todayStart)
            ->where('status', '!=', OrderStatus::CANCELLED)
            ->sum('grand_total');

        $totalOrdersToday = Order::where('created_at', '>=', $todayStart)->count();

        $ordersToDispatchCount = Order::whereIn('status', [OrderStatus::CONFIRMED, OrderStatus::PROCESSING])
            ->whereNull('courier_tracking_code')
            ->count();

        $pendingCodPoisha = Order::where('payment_method', 'cod')
            ->where('payment_status', 'pending')
            ->where('status', '!=', OrderStatus::CANCELLED)
            ->sum('grand_total');

        $lowStockVariants = ProductVariant::with('product')
            ->where('stock_on_hand', '<=', 10)
            ->where('is_active', true)
            ->take(5)
            ->get();

        $recentOrders = Order::with(['items', 'address'])
            ->latest()
            ->take(8)
            ->get();

        return Inertia::render('admin/Dashboard', [
            'metrics' => [
                'todaySalesBDT' => (int) round($todaySalesPoisha / 100),
                'totalOrdersToday' => $totalOrdersToday,
                'aovBDT' => $totalOrdersToday > 0 ? (int) round(($todaySalesPoisha / $totalOrdersToday) / 100) : 0,
                'ordersToDispatchCount' => $ordersToDispatchCount,
                'pendingCodBDT' => (int) round($pendingCodPoisha / 100),
            ],
            'lowStockVariants' => $lowStockVariants,
            'recentOrders' => $recentOrders,
        ]);
    }
}
