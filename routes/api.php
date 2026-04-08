<?php

use App\Http\Controllers\Api\CanteenController;
use Illuminate\Support\Facades\Route;

Route::prefix('canteen')->group(function () {
    Route::get('/vendors', [CanteenController::class, 'vendors']);
    Route::get('/vendors/{idvendor}/menus', [CanteenController::class, 'menusByVendor']);

    Route::post('/vendor/menus', [CanteenController::class, 'createVendorMenu']);

    Route::post('/orders', [CanteenController::class, 'createPesanan']);
    Route::patch('/orders/{idpesanan}/payment-status', [CanteenController::class, 'updatePaymentStatus']);

    Route::post('/payments/midtrans/callback', [CanteenController::class, 'paymentCallback']);

    Route::get('/vendors/{idvendor}/orders/lunas', [CanteenController::class, 'vendorPaidOrders']);
});
