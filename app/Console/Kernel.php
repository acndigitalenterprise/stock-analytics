<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Monitor stock prices every 5 minutes during trading hours (9 AM - 4 PM WIB)
        $schedule->command('stock:monitor-prices')
                 ->everyFiveMinutes()
                 ->between('09:00', '16:00')
                 ->timezone('Asia/Jakarta')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Process timeouts every hour to catch any missed timeouts
        $schedule->command('stock:monitor-prices')
                 ->hourly()
                 ->withoutOverlapping()
                 ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}