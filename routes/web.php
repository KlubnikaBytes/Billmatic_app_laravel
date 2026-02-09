<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::view('/start-free-billing', 'auth')->name('start.free.billing');

// Business details (multi-step setup)
Route::view('/business-details', 'business-details')->name('business.details');

// Dashboard
Route::view('/dashboard', 'dashboard')->name('dashboard');

Route::view('/privacy-policy', 'privacy-policy')->name('privacy.policy');
Route::view('/terms-of-service', 'terms-of-service')->name('terms.of.service');

// routes/web.php
Route::view('/contact-us', 'contact-us')->name('contact.us');

// routes/web.php
Route::view('/about-us', 'about-us')->name('about.us');

// routes/web.php
Route::view('/cancellation-refund-policy', 'cancellation-refund-policy')
    ->name('cancellation.refund.policy');



