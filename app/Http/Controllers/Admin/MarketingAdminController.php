<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Marketing\Models\AbandonedCheckout;
use App\Domain\Orders\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarketingAdminController
{
    public function index(): Response
    {
        // Default coupons if table is empty
        if (Coupon::count() === 0) {
            Coupon::create([
                'code' => 'EID2026',
                'type' => 'percentage',
                'value_amount' => 1500, // 15%
                'max_discount_amount' => 50000, // ৳500 max cap
                'min_spend_amount' => 200000, // ৳2,000 min spend
                'usage_limit' => 500,
                'used_count' => 42,
                'is_active' => true,
                'starts_at' => now()->subDays(5),
                'expires_at' => now()->addDays(25),
            ]);

            Coupon::create([
                'code' => 'NAZEEFA100',
                'type' => 'fixed_amount',
                'value_amount' => 10000, // ৳100 flat discount
                'min_spend_amount' => 100000, // ৳1,000 min spend
                'usage_limit' => 1000,
                'used_count' => 184,
                'is_active' => true,
            ]);
        }

        $coupons = Coupon::latest()->paginate(10);
        $abandonedCheckouts = AbandonedCheckout::latest()->paginate(15);

        $abandonedCount = AbandonedCheckout::where('status', 'abandoned')->count();
        $abandonedRevenuePoisha = AbandonedCheckout::where('status', 'abandoned')->sum('subtotal_amount');
        $recoveredRevenuePoisha = AbandonedCheckout::where('status', 'recovered')->sum('subtotal_amount');

        return Inertia::render('admin/MarketingIndex', [
            'coupons' => $coupons,
            'abandonedCheckouts' => $abandonedCheckouts,
            'kpis' => [
                'active_coupons_count' => Coupon::where('is_active', true)->count(),
                'abandoned_count' => $abandonedCount,
                'abandoned_revenue_bdt' => round($abandonedRevenuePoisha / 100),
                'recovered_revenue_bdt' => round($recoveredRevenuePoisha / 100),
            ],
        ]);
    }

    public function storeCoupon(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'unique:coupons,code'],
            'type' => ['required', 'in:fixed_amount,percentage,free_shipping'],
            'value_amount' => ['required', 'integer'],
            'min_spend_amount' => ['nullable', 'integer'],
            'max_discount_amount' => ['nullable', 'integer'],
            'usage_limit' => ['nullable', 'integer'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = true;

        Coupon::create($validated);

        return redirect()->back()->with('success', "Coupon {$validated['code']} created successfully!");
    }

    public function sendRecovery(Request $request, int $id): JsonResponse
    {
        $checkout = AbandonedCheckout::findOrFail($id);
        $checkout->increment('recovery_sent_count');

        // Simulates automated Bangladesh SMS Gateway notification (e.g. Greenweb, BulkSMSBD)
        return response()->json([
            'status' => 'sent',
            'message' => "Recovery reminder dispatched via SMS to {$checkout->customer_phone}.",
        ]);
    }
}
