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
        // Monitor stock prices every 5 minutes during trading hours (9 AM - 4 PM WIB) for active price checking
        $schedule->command('stock:monitor-prices')
                 ->everyFiveMinutes()
                 ->between('09:00', '16:00')
                 ->timezone('Asia/Jakarta')
                 ->withoutOverlapping()
                 ->runInBackground();

        // TIMEOUT PROCESSING: Run every 10 minutes 24/7 to catch timeouts even outside trading hours
        // This ensures requests timeout properly even when market is closed
        $schedule->command('stock:monitor-prices')
                 ->everyTenMinutes()
                 ->timezone('Asia/Jakarta')
                 ->withoutOverlapping()
                 ->runInBackground();

        // SIGNAL GENERATION: Generate trading signals every 15 minutes during trading hours
        // Scans top 50 IDX stocks and creates signals with confidence ≥70%
        $schedule->job(new \App\Jobs\GenerateSignals)
                 ->everyFifteenMinutes()
                 ->between('09:00', '16:00')
                 ->timezone('Asia/Jakarta')
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