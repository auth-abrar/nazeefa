<?php

use App\Http\Controllers\Storefront\CouponController;
use App\Http\Controllers\Storefront\CustomDesignerController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\OrderTrackingController;
use App\Http\Controllers\Storefront\ProductController;
use App\Http\Controllers\Storefront\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/track-order', [OrderTrackingController::class, 'index'])->name('order.track');

// POD Custom Designer Studio
Route::get('/custom-designer', [CustomDesignerController::class, 'show'])->name('custom-designer.show');
Route::post('/custom-designer/artwork', [CustomDesignerController::class, 'uploadArtwork'])->name('custom-designer.artwork');

// Promotions & Checkout Abandonment API
Route::post('/api/coupons/apply', [CouponController::class, 'apply'])->name('coupons.apply');
Route::post('/api/checkout/track-progress', [CouponController::class, 'trackProgress'])->name('checkout.track-progress');
