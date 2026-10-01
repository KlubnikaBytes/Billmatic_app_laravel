<?php

use Illuminate\Support\Facades\Route;
//need to add
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PurchaseController;


Route::get('/', function () {
    return view('welcome');
});


Route::view('/start-free-billing', 'auth')->name('start.free.billing');

// Business details (multi-step setup)
Route::view('/business-details', 'business-details')->name('business.details');

// Dashboard
Route::view('/dashboard', 'dashboard')->name('dashboard');

// Create Sales Invoice //need to add
Route::view('/sales-invoice/create', 'sales-invoice-create')
    ->name('sales.invoice.create');

// Parties
Route::view('/parties', 'parties')
    ->name('parties');    

// Create Party
Route::view('/parties/create', 'create-party')
    ->name('parties.create');

// Edit Party
Route::view('/parties/{id}/edit', 'create-party')
    ->name('parties.edit'); 
    
// Items
Route::view('/items', 'items')
    ->name('items');  
    
// Sales Invoices
Route::view('/sales', 'sales')
    ->name('sales');    

// Payment In
Route::view('/payment-in', 'payment-in')
    ->name('payment.in');    

// Create Payment In
Route::view('/payment-in/create', 'payment-in-create')
    ->name('payment.in.create');    

// Purchase Invoices
Route::view('/purchases', 'purchases')
    ->name('purchases');

// Create Purchase Invoice
Route::view('/purchases/create', 'purchase_create')
    ->name('purchases.create');    
    
    
    
    
Route::view('/privacy-policy', 'privacy-policy')->name('privacy.policy');
Route::view('/terms-of-service', 'terms-of-service')->name('terms.of.service');

// routes/web.php
Route::view('/contact-us', 'contact-us')->name('contact.us');

// routes/web.php
Route::view('/about-us', 'about-us')->name('about.us');

// routes/web.php
Route::view('/cancellation-refund-policy', 'cancellation-refund-policy')
    ->name('cancellation.refund.policy');



//need to add

Route::get('/invoice-preview/{id}', [InvoiceController::class, 'preview'])
    ->name('invoice.preview');


Route::get('/purchase-preview/{id}', [PurchaseController::class, 'preview'])
    ->name('purchase.preview');    