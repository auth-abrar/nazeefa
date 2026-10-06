<?php

use App\Http\Controllers\Storefront\PaymentController;
use App\Http\Controllers\Webhooks\CourierWebhookController;
use Illuminate\Support\Facades\Route;

// Payment IPN Webhooks
Route::post('/webhooks/payments/{provider}', [PaymentController::class, 'callback'])->name('webhooks.payment');

// Courier Tracking & Delivery Webhooks
Route::post('/webhooks/couriers/{provider}', [CourierWebhookController::class, 'handle'])->name('webhooks.courier');
