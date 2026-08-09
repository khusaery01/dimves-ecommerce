<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// =================== AUTHENTICATION ===================
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// =================== PUBLIC DATA ===================
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/menus', [MenuController::class, 'index']);
Route::get('/menus/{id}', [MenuController::class, 'show']);

// Webhook Midtrans (Public)
Route::post('/payments/webhook', [PaymentController::class, 'webhook']);

// =================== PROTECTED ROUTES (SANCTUM) ===================
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);

    // Voucher Check
    Route::post('/vouchers/check', [VoucherController::class, 'check']);

    // Order & Checkout
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::get('/orders/{id}/status', [OrderController::class, 'statusCheck']); // Real-time Polling Check

    // Payment Gateway (Midtrans - Optional)
    Route::post('/payments/create/{orderId}', [PaymentController::class, 'createPayment']);
});