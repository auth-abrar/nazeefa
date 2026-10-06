<?php

namespace App\Http\Controllers\Webhooks;

use App\Domain\Fulfillment\Models\Shipment;
use App\Domain\Fulfillment\Services\FulfillmentManager;
use App\Enums\OrderStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourierWebhookController
{
    public function handle(Request $request, string $providerName, FulfillmentManager $fulfillmentManager): JsonResponse
    {
        $provider = $fulfillmentManager->provider($providerName);
        $payload = $request->all();

        Log::info("Received Courier Webhook from {$providerName}", $payload);

        $parsed = $provider->handleWebhook($payload);
        $consignmentId = $parsed['consignment_id'] ?? null;
        $normalizedStatus = $parsed['status'] ?? null;

        if (!$consignmentId || !$normalizedStatus) {
            return response()->json(['status' => 'ignored', 'message' => 'Missing consignment or status.'], 400);
        }

        $shipment = Shipment::where('consignment_id', $consignmentId)->first();
        if (!$shipment) {
            return response()->json(['status' => 'not_found'], 404);
        }

        DB::transaction(function () use ($shipment, $normalizedStatus, $parsed, $payload) {
            $shipment->update([
                'status' => $normalizedStatus,
                'delivered_at' => $normalizedStatus === 'delivered' ? now() : $shipment->delivered_at,
            ]);

            $shipment->events()->create([
                'status' => $normalizedStatus,
                'location' => $payload['location'] ?? 'Hub',
                'description' => "Status changed to {$normalizedStatus} (Provider raw: {$parsed['raw_status']})",
                'raw_payload' => $payload,
                'occurred_at' => now(),
            ]);

            $order = $shipment->order;
            if ($normalizedStatus === 'delivered') {
                $order->update([
                    'status' => OrderStatus::DELIVERED,
                    'payment_status' => 'paid', // Cash Collected on Delivery
                ]);
            } elseif ($normalizedStatus === 'out_for_delivery') {
                $order->update(['status' => OrderStatus::OUT_FOR_DELIVERY]);
            }
        });

        return response()->json(['status' => 'success']);
    }
}
