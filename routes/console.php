<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('ca:send-monthly-reports')
    ->monthlyOn(1, '09:00')
    ->timezone('Asia/Kolkata')
    ->withoutOverlapping();
