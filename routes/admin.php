<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryAdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\ProductionAdminController;
use App\Http\Controllers\Admin\SupplierAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin & BusinessOS Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['web'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Orders
    Route::get('/orders', [OrderAdminController::class, 'index'])->name('admin.orders.index');
    Route::post('/orders/{id}/dispatch', [OrderAdminController::class, 'dispatchOrder'])->name('admin.orders.dispatch');
    
    // Inventory
    Route::get('/inventory', [InventoryAdminController::class, 'index'])->name('admin.inventory.index');
    Route::post('/inventory/adjust', [InventoryAdminController::class, 'adjustStock'])->name('admin.inventory.adjust');

    // POD & Production
    Route::get('/pod', [ProductionAdminController::class, 'index'])->name('admin.pod.index');
    Route::post('/pod/jobs/{id}/status', [ProductionAdminController::class, 'updateStatus'])->name('admin.pod.update-status');

    // Suppliers & Dropshipping
    Route::get('/suppliers', [SupplierAdminController::class, 'index'])->name('admin.suppliers.index');
    Route::get('/suppliers/search', [SupplierAdminController::class, 'search'])->name('admin.suppliers.search');
    Route::post('/suppliers/import', [SupplierAdminController::class, 'import'])->name('admin.suppliers.import');
    Route::post('/suppliers/sync-stock', [SupplierAdminController::class, 'syncStock'])->name('admin.suppliers.sync-stock');
});
