<?php

namespace App\Domain\Fulfillment\Actions;

use App\Domain\Fulfillment\Models\Shipment;
use App\Domain\Fulfillment\Services\FulfillmentManager;
use App\Domain\Orders\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;

class DispatchOrderAction
{
    public function __construct(protected FulfillmentManager $fulfillmentManager) {}

    public function execute(Order $order, string $courierName = 'pathao'): Shipment
    {
        return DB::transaction(function () use ($order, $courierName) {
            $provider = $this->fulfillmentManager->provider($courierName);

            $result = $provider->createShipment([
                'order_number' => $order->order_number,
                'amount' => $order->payment_status === 'paid' ? 0 : $order->grand_total,
                'recipient_name' => $order->guest_name,
                'recipient_phone' => $order->guest_phone,
                'district' => $order->address?->district ?? 'Dhaka',
                'address' => $order->address?->street_address ?? '',
            ]);

            $shipment = Shipment::create([
                'order_id' => $order->id,
                'courier_provider' => $courierName,
                'consignment_id' => $result['consignment_id'],
                'tracking_code' => $result['tracking_code'],
                'status' => 'picked_up',
                'cod_amount' => $order->payment_status === 'paid' ? 0 : $order->grand_total,
                'courier_fee' => $result['delivery_fee'] ?? 7000,
                'recipient_name' => $order->guest_name,
                'recipient_phone' => $order->guest_phone,
                'district' => $order->address?->district ?? 'Dhaka',
                'recipient_address' => $order->address?->street_address ?? '',
                'dispatched_at' => now(),
            ]);

            $shipment->events()->create([
                'status' => 'picked_up',
                'location' => 'Nazeefa Dhaka Central Hub',
                'description' => "Parcel handed over to {$courierName} courier.",
                'occurred_at' => now(),
            ]);

            $order->update([
                'status' => OrderStatus::SHIPPED,
                'courier_provider' => $courierName,
                'courier_tracking_code' => $result['tracking_code'],
            ]);

            return $shipment;
        });
    }
}
