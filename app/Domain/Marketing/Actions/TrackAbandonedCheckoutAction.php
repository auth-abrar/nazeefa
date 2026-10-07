<?php

namespace App\Domain\Marketing\Actions;

use App\Domain\Marketing\Models\AbandonedCheckout;
use Illuminate\Support\Str;

class TrackAbandonedCheckoutAction
{
    public function execute(array $data): AbandonedCheckout
    {
        $phone = preg_replace('/[^0-9]/', '', $data['customer_phone'] ?? '');

        return AbandonedCheckout::updateOrCreate(
            [
                'session_id' => $data['session_id'],
            ],
            [
                'customer_phone' => $phone,
                'customer_name' => $data['customer_name'] ?? null,
                'customer_email' => $data['customer_email'] ?? null,
                'district' => $data['district'] ?? 'Dhaka',
                'cart_payload' => $data['cart_payload'] ?? [],
                'subtotal_amount' => $data['subtotal_amount'] ?? 0,
                'recovery_token' => 'rec_' . Str::random(24),
                'status' => 'abandoned',
            ]
        );
    }
}
