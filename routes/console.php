<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule stock price monitoring every minute during trading hours (09:00-16:00 WIB)
Schedule::command('stock:monitor-prices')
    ->everyMinute()
    ->between('09:00', '16:00')
    ->timezone('Asia/Jakarta')
    ->runInBackground();
