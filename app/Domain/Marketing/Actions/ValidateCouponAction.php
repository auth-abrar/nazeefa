<?php

namespace App\Domain\Marketing\Actions;

use App\Domain\Orders\Models\Coupon;

class ValidateCouponAction
{
    /**
     * Validate coupon code and calculate discount poisha against order subtotal.
     */
    public function execute(string $code, int $subtotalPoisha): array
    {
        $code = strtoupper(trim($code));
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return [
                'valid' => false,
                'message' => "Coupon code '{$code}' not found.",
                'discount_poisha' => 0,
            ];
        }

        if (!$coupon->is_active) {
            return [
                'valid' => false,
                'message' => "Coupon code '{$code}' is currently inactive.",
                'discount_poisha' => 0,
            ];
        }

        if ($coupon->starts_at && now()->lt($coupon->starts_at)) {
            return [
                'valid' => false,
                'message' => "Coupon code '{$code}' promotion has not started yet.",
                'discount_poisha' => 0,
            ];
        }

        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            return [
                'valid' => false,
                'message' => "Coupon code '{$code}' has expired.",
                'discount_poisha' => 0,
            ];
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            return [
                'valid' => false,
                'message' => "Coupon code '{$code}' has reached its global redemption limit.",
                'discount_poisha' => 0,
            ];
        }

        if ($coupon->min_spend_amount !== null && $subtotalPoisha < $coupon->min_spend_amount) {
            $minBdt = number_format($coupon->min_spend_amount / 100);
            return [
                'valid' => false,
                'message' => "Minimum spend of ৳{$minBdt} required to apply this coupon.",
                'discount_poisha' => 0,
            ];
        }

        // Calculate discount amount
        $discountPoisha = 0;
        $isFreeShipping = false;

        if ($coupon->type === 'fixed_amount') {
            $discountPoisha = min($subtotalPoisha, $coupon->value_amount);
        } elseif ($coupon->type === 'percentage') {
            $rawDiscount = (int) round($subtotalPoisha * ($coupon->value_amount / 10000));
            if ($coupon->max_discount_amount !== null) {
                $discountPoisha = min($rawDiscount, $coupon->max_discount_amount);
            } else {
                $discountPoisha = $rawDiscount;
            }
        } elseif ($coupon->type === 'free_shipping') {
            $isFreeShipping = true;
            $discountPoisha = 0; // Handled in shipping fee deduction
        }

        return [
            'valid' => true,
            'coupon_id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'discount_poisha' => $discountPoisha,
            'discount_bdt' => $discountPoisha / 100,
            'is_free_shipping' => $isFreeShipping,
            'message' => $isFreeShipping ? 'Free shipping applied!' : "Coupon applied! Saved ৳" . number_format($discountPoisha / 100),
        ];
    }
}
