<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PartyController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\InvoiceController;

Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

Route::middleware('auth:api')->group(function () {

    // =======================
    // Business Details
    // =======================
    Route::post('/business-details', [AuthController::class, 'updateBusinessDetails']);
    Route::get('/me', function () {
        return auth()->user();
    });

    // =======================
    // Parties API
    // =======================
    Route::get('/parties', [PartyController::class, 'index']);
    Route::post('/parties', [PartyController::class, 'store']);

    // =======================
    // Items API
    // =======================
    Route::get('/items', [ItemController::class, 'index']);
    Route::post('/items', [ItemController::class, 'store']);

    // =======================
    // Invoices API
    // =======================
    Route::post('/invoices', [InvoiceController::class, 'store']);

    Route::get('/invoices/last-number', [InvoiceController::class, 'lastNumber']);

});
