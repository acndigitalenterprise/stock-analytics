<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PriceMonitoringService;

class ProcessTimeouts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:process-timeouts {--force : Force process all timeout requests}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process timeout requests that exceeded monitoring time';

    /**
     * Execute the console command.
     */
    public function handle(PriceMonitoringService $monitoringService)
    {
        $this->info('⏰ Processing timeout requests...');

        $timeoutCount = $monitoringService->processTimeoutRequests();

        if ($timeoutCount > 0) {
            $this->info("✅ Processed {$timeoutCount} timeout requests");
        } else {
            $this->info("✅ No timeout requests found");
        }

        // Show current stats
        $stats = $monitoringService->getWinningRateStats();
        if ($stats['total_requests'] > 0) {
            $this->info("📊 Current Stats: {$stats['winning_rate']}% accuracy ({$stats['total_wins']} Win, {$stats['losses']} Loss, {$stats['timeouts']} Timeout)");
        }

        return Command::SUCCESS;
    }
}