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

        try {
            GenerateSignals::dispatchSync();
            $this->info('Signal generation completed successfully!');

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

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Signal generation failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
