<?php

namespace App\Http\Controllers\Storefront;

use App\Domain\Marketing\Actions\TrackAbandonedCheckoutAction;
use App\Domain\Marketing\Actions\ValidateCouponAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController
{
    public function __construct(
        protected ValidateCouponAction $validateCouponAction,
        protected TrackAbandonedCheckoutAction $trackAbandonedAction
    ) {}

    public function apply(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
            'subtotal_poisha' => ['required', 'integer'],
        ]);

        $result = $this->validateCouponAction->execute(
            code: $validated['code'],
            subtotalPoisha: $validated['subtotal_poisha']
        );

        return response()->json($result);
    }

    public function trackProgress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => ['required', 'string'],
            'customer_phone' => ['required', 'string'],
            'customer_name' => ['nullable', 'string'],
            'district' => ['nullable', 'string'],
            'cart_payload' => ['required', 'array'],
            'subtotal_amount' => ['required', 'integer'],
        ]);

        $abandoned = $this->trackAbandonedAction->execute($validated);

        return response()->json([
            'status' => 'tracked',
            'recovery_token' => $abandoned->recovery_token,
        ]);
    }
}
