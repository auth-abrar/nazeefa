<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryAdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\ProductionAdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Order Operations
    Route::get('/orders', [OrderAdminController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderAdminController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/dispatch', [OrderAdminController::class, 'dispatch'])->name('orders.dispatch');
    Route::post('/orders/{id}/status', [OrderAdminController::class, 'updateStatus'])->name('orders.status');

    // Multi-Warehouse Inventory Operations
    Route::get('/inventory', [InventoryAdminController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/adjust', [InventoryAdminController::class, 'adjust'])->name('inventory.adjust');

    // POD & Production Pipeline
    Route::get('/pod', [ProductionAdminController::class, 'index'])->name('pod.index');
    Route::post('/pod/jobs/{id}/status', [ProductionAdminController::class, 'updateStatus'])->name('pod.status');
});
