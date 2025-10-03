<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\GenerateSignals;

class GenerateSignalsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'signals:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate trading signals for top 50 IDX stocks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting signal generation...');
        $this->info('Checking logs at storage/logs/laravel.log for detailed progress');

        try {
            $beforeCount = \App\Models\Signal::count();
            $beforeActive = \App\Models\Signal::active()->count();

            GenerateSignals::dispatchSync();

            $afterCount = \App\Models\Signal::count();
            $afterActive = \App\Models\Signal::active()->count();

            $this->info('Signal generation completed!');
            $this->info("New signals created: " . ($afterCount - $beforeCount));

            // Show stats
            $totalSignals = \App\Models\Signal::count();
            $activeSignals = \App\Models\Signal::active()->count();
            $todaySignals = \App\Models\Signal::whereDate('created_at', today())->count();

            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total Signals', $totalSignals],
                    ['Active Signals', $activeSignals],
                    ['Today\'s Signals', $todaySignals],
                ]
            );

            // Check recent logs
            $this->info("\nCheck logs for details: tail -100 storage/logs/laravel.log | grep -i signal");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Signal generation failed: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
