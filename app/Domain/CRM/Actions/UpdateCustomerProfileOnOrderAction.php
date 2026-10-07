<?php

namespace App\Domain\CRM\Actions;

use App\Domain\CRM\Models\Customer;
use App\Domain\Orders\Models\Order;
use Illuminate\Support\Facades\DB;

class UpdateCustomerProfileOnOrderAction
{
    /**
     * Update customer LTV, completed orders, and COD Return Risk score.
     */
    public function execute(Order $order, string $eventType = 'order_placed'): Customer
    {
        return DB::transaction(function () use ($order, $eventType) {
            $order->load('shippingAddress');
            $phone = preg_replace('/[^0-9]/', '', $order->shippingAddress?->phone ?? '01700000000');
            $name = $order->shippingAddress?->recipient_name ?? 'Nazeefa Shopper';
            $district = $order->shippingAddress?->city ?? 'Dhaka';

            $customer = Customer::firstOrCreate(
                ['phone' => $phone],
                [
                    'name' => $name,
                    'email' => $order->customer_email,
                    'default_district' => $district,
                    'total_orders_count' => 0,
                    'delivered_orders_count' => 0,
                    'returned_orders_count' => 0,
                    'total_spent_amount' => 0,
                    'cod_return_risk_score' => 0.00,
                    'risk_tier' => 'low_risk',
                    'tags' => ['new_shopper'],
                ]
            );

            if ($eventType === 'order_placed') {
                $customer->increment('total_orders_count');
                $customer->increment('total_spent_amount', $order->grand_total_amount);
            } elseif ($eventType === 'order_delivered') {
                $customer->increment('delivered_orders_count');
            } elseif ($eventType === 'order_returned' || $eventType === 'order_cancelled') {
                $customer->increment('returned_orders_count');
            }

            // Re-calculate COD return risk ratio
            $completedTrips = $customer->delivered_orders_count + $customer->returned_orders_count;
            $riskScore = 0.00;

            if ($completedTrips > 0) {
                $riskScore = round($customer->returned_orders_count / $completedTrips, 2);
            }

            // Assign Risk Tier & VIP Status
            $tier = 'low_risk';
            $tags = $customer->tags ?? [];

            if ($customer->total_spent_amount >= 1000000 && $riskScore < 0.20) { // ৳10,000+
                $tier = 'verified_vip';
                if (!in_array('vip', $tags)) $tags[] = 'vip';
            } elseif ($riskScore >= 0.35 && $completedTrips >= 2) {
                $tier = 'high_risk';
                if (!in_array('high_return_risk', $tags)) $tags[] = 'high_return_risk';
            } elseif ($riskScore >= 0.15) {
                $tier = 'moderate_risk';
            }

            $customer->update([
                'name' => $name,
                'default_district' => $district,
                'cod_return_risk_score' => $riskScore,
                'risk_tier' => $tier,
                'tags' => array_values(array_unique($tags)),
            ]);

            return $customer;
        });
    }
}
