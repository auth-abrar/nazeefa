<?php

namespace App\Http\Controllers\Storefront;

use App\Domain\Orders\Models\Order;
use App\Domain\Payments\Models\Payment;
use App\Domain\Payments\Models\PaymentAttempt;
use App\Domain\Payments\Services\PaymentManager;
use App\Enums\OrderStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController
{
    public function initiate(string $orderNumber, PaymentManager $paymentManager): RedirectResponse
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        $providerName = $order->payment_method;
        if ($providerName === 'cod') {
            return redirect()->route('checkout.confirmation', ['order_number' => $order->order_number]);
        }

        $provider = $paymentManager->provider($providerName);
        $transactionId = 'TXN-' . date('Ymd') . '-' . strtoupper(Str::random(8));

        $attempt = PaymentAttempt::create([
            'order_id' => $order->id,
            'provider' => $providerName,
            'transaction_id' => $transactionId,
            'status' => 'initiated',
            'amount' => $order->grand_total,
            'currency' => $order->currency,
            'ip_address' => request()->ip(),
        ]);

        $paymentResult = $provider->createPayment([
            'tran_id' => $transactionId,
            'amount' => $order->grand_total,
            'customer_name' => $order->guest_name,
            'customer_phone' => $order->guest_phone,
            'customer_email' => $order->guest_email,
            'address' => $order->address?->street_address ?? '',
            'city' => $order->address?->district ?? 'Dhaka',
        ]);

        if (isset($paymentResult['redirect_url'])) {
            $attempt->update(['gateway_redirect_url' => $paymentResult['redirect_url']]);
            return redirect()->away($paymentResult['redirect_url']);
        }

        return redirect()->route('checkout.confirmation', ['order_number' => $order->order_number])
            ->with('error', 'Online payment gateway session failed. Contact customer support.');
    }

    public function callback(Request $request, string $providerName, PaymentManager $paymentManager): RedirectResponse
    {
        $provider = $paymentManager->provider($providerName);
        $verification = $provider->handleCallback($request->all());

        $transactionId = $request->input('tran_id');
        $attempt = PaymentAttempt::where('transaction_id', $transactionId)->first();

        if ($attempt && ($verification['status'] ?? '') === 'VALID') {
            DB::transaction(function () use ($attempt, $verification, $providerName, $transactionId) {
                $attempt->update(['status' => 'successful', 'response_payload' => $verification]);

                Payment::create([
                    'order_id' => $attempt->order_id,
                    'provider' => $providerName,
                    'transaction_id' => $transactionId,
                    'amount' => $attempt->amount,
                    'currency' => $attempt->currency,
                    'status' => 'paid',
                    'bank_tran_id' => $verification['bank_tran_id'] ?? null,
                    'card_type' => $verification['card_type'] ?? null,
                    'paid_at' => now(),
                ]);

                $order = $attempt->order;
                $order->update([
                    'payment_status' => 'paid',
                    'status' => OrderStatus::CONFIRMED,
                ]);
            });

            return redirect()->route('checkout.confirmation', ['order_number' => $attempt->order->order_number]);
        }

        if ($attempt) {
            $attempt->update(['status' => 'failed', 'response_payload' => $verification]);
            return redirect()->route('checkout.confirmation', ['order_number' => $attempt->order->order_number])
                ->with('error', 'Online payment could not be verified.');
        }

        return redirect()->route('home');
    }

    public function cancel(string $providerName): RedirectResponse
    {
        return redirect()->route('home')->with('info', 'Payment session was cancelled.');
    }
}
