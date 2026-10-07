<?php

namespace App\Http\Controllers\Admin;

use App\Domain\CRM\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CrmAdminController
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $riskFilter = $request->input('risk_tier');

        // Seed initial Bangladesh fashion customers if empty
        if (Customer::count() === 0) {
            Customer::create([
                'phone' => '01711223344',
                'name' => 'Tanvir Ahmed',
                'email' => 'tanvir@gmail.com',
                'default_district' => 'Dhaka',
                'total_orders_count' => 6,
                'delivered_orders_count' => 6,
                'returned_orders_count' => 0,
                'total_spent_amount' => 1450000, // ৳14,500 LTV
                'cod_return_risk_score' => 0.00,
                'risk_tier' => 'verified_vip',
                'tags' => ['vip', 'frequent_buyer', 'streetwear_enthusiast'],
                'admin_notes' => 'Reliable corporate customer. Always accepts parcel immediately on call.',
            ]);

            Customer::create([
                'phone' => '01899887766',
                'name' => 'Mehedi Hasan',
                'email' => 'mehedi@yahoo.com',
                'default_district' => 'Gazipur',
                'total_orders_count' => 4,
                'delivered_orders_count' => 1,
                'returned_orders_count' => 3,
                'total_spent_amount' => 220000,
                'cod_return_risk_score' => 0.75, // 75% return rate
                'risk_tier' => 'high_risk',
                'tags' => ['high_return_risk'],
                'admin_notes' => 'Rejected 3 parcels upon delivery arrival citing change of mind. ALWAYS require advance delivery charge.',
            ]);
        }

        $query = Customer::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($riskFilter) {
            $query->where('risk_tier', $riskFilter);
        }

        $customers = $query->orderByDesc('total_spent_amount')->paginate(15);

        $totalCustomers = Customer::count();
        $vipCount = Customer::where('risk_tier', 'verified_vip')->count();
        $highRiskCount = Customer::where('risk_tier', 'high_risk')->count();
        $totalLtvBdt = Customer::sum('total_spent_amount') / 100;

        return Inertia::render('admin/CustomersIndex', [
            'customers' => $customers,
            'filters' => [
                'search' => $search,
                'risk_tier' => $riskFilter,
            ],
            'kpis' => [
                'total_customers' => $totalCustomers,
                'vip_customers' => $vipCount,
                'high_risk_customers' => $highRiskCount,
                'total_ltv_bdt' => round($totalLtvBdt),
            ],
        ]);
    }

    public function updateNotes(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string'],
            'risk_tier' => ['required', 'in:low_risk,moderate_risk,high_risk,verified_vip'],
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($validated);

        return redirect()->back()->with('success', "Customer {$customer->name} profile updated.");
    }
}
