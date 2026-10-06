<?php

use App\Http\Controllers\Storefront\PaymentController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/payments/{provider}', [PaymentController::class, 'callback'])->name('webhooks.payment');
