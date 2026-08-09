<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminMenuController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminVoucherController;

Route::get('/', function () {
    return view('welcome');
});

// Admin Panel & Kitchen Display System Routes
Route::prefix('admin')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Kitchen Display & Order Monitor
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::post('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.update-status');

    // Menu Management & Stock Toggle
    Route::get('/menus', [AdminMenuController::class, 'index'])->name('admin.menus.index');
    Route::post('/menus', [AdminMenuController::class, 'store'])->name('admin.menus.store');
    Route::post('/menus/{id}/toggle-stock', [AdminMenuController::class, 'toggleStock'])->name('admin.menus.toggle-stock');

    // Reports & Sales Analytics
    Route::get('/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');

    // Voucher & Promo Management
    Route::get('/vouchers', [AdminVoucherController::class, 'index'])->name('admin.vouchers.index');
    Route::post('/vouchers', [AdminVoucherController::class, 'store'])->name('admin.vouchers.store');
    Route::post('/vouchers/{id}/toggle', [AdminVoucherController::class, 'toggleActive'])->name('admin.vouchers.toggle');
    Route::delete('/vouchers/{id}', [AdminVoucherController::class, 'destroy'])->name('admin.vouchers.destroy');
});

