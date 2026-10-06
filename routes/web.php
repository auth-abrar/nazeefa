<?php

use App\Http\Controllers\Storefront\HomeController;
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
