<?php

use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\OrderTrackingController;
use App\Http\Controllers\Storefront\PaymentController;
use App\Http\Controllers\Storefront\ProductController;
use App\Http\Controllers\Storefront\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Custom & Print-On-Demand direct entrance
Route::get('/custom-print', function () {
    return redirect()->route('shop', ['customizable' => 1]);
})->name('custom.index');

// Checkout & Order Placement
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/confirmation/{order_number}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

// Payment Gateway Routes
Route::get('/payment/initiate/{order_number}', [PaymentController::class, 'initiate'])->name('payment.initiate');
Route::match(['get', 'post'], '/payment/callback/{provider}', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/payment/cancel/{provider}', [PaymentController::class, 'cancel'])->name('payment.cancel');

// Order Tracking
Route::get('/track-order', [OrderTrackingController::class, 'show'])->name('track.order');
