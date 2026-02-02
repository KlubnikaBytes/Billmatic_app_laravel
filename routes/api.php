<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PartyController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\ReceivedPaymentController;


Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

Route::middleware('auth:api')->group(function () {

    // =======================
    // Business Details
    // =======================
    Route::post('/business-details', [AuthController::class, 'updateBusinessDetails']);
    Route::get('/business-info', [AuthController::class, 'businessInfo']);

    Route::get('/me', function () {
        return auth()->user();
    });

    // =======================
    // Parties API
    // =======================
    Route::get('/parties', [PartyController::class, 'index']);
    Route::post('/parties', [PartyController::class, 'store']);

    // =======================
    // Party Invoices (Amount Screen)
    // =======================
    Route::get('/parties/{id}/invoices', [PartyController::class, 'invoices']);

    // 🟢 NEW: All invoices (paid + partial + unpaid)
    Route::get('/parties/{id}/all-invoices', [PartyController::class, 'allInvoices']);

    // 🔄 Party Transactions (Invoices + Payments)
    Route::get('/parties/{id}/transactions', [PartyController::class, 'transactions']);

    Route::get('/parties/{id}', [PartyController::class, 'show']);

    Route::put('/parties/{id}', [PartyController::class, 'update']);

    Route::delete('/parties/{id}', [PartyController::class, 'destroy']);

     Route::get('/dashboard/totals', [PartyController::class, 'dashboardTotals']);

     Route::get('/dashboard/transactions', [PartyController::class, 'allTransactions']);



    // =======================
    // Items API
    // =======================
    Route::get('/items', [ItemController::class, 'index']);
    Route::post('/items', [ItemController::class, 'store']);
    Route::get('/items/{id}/timeline', [ItemController::class, 'timeline']);
    Route::put('/items/{id}', [ItemController::class, 'update']);
    Route::delete('/items/{id}', [ItemController::class, 'destroy']);
    Route::get('/stock-summary', [ItemController::class, 'stockSummary']);

    Route::get('/items/{id}', [ItemController::class, 'show']);






    // =======================
    // Invoices API
    // =======================
    Route::post('/invoices', [InvoiceController::class, 'store']);

    Route::get('/invoices/last-number', [InvoiceController::class, 'lastNumber']);

    Route::get('/sales-summary', [InvoiceController::class, 'salesSummary']);

    Route::get('/cash-bank-summary', [InvoiceController::class, 'cashBankSummary']);

    Route::get('/cash-bank-details', [InvoiceController::class, 'cashBankDetails']);




    // ✅ RECEIVED PAYMENT
    Route::post('/payments', [ReceivedPaymentController::class, 'store']);

    Route::get('/payments/next-number', [ReceivedPaymentController::class, 'nextNumber']);


});
