<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PriceMonitoringService;

class MonitorStockPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:monitor-prices 
                            {--backfill : Backfill existing requests with targets from advice}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor stock prices and update WIN/LOSS results based on AI trading advice';

    /**
     * Execute the console command.
     */
    public function handle(PriceMonitoringService $monitoringService)
    {
        $this->info('🎯 Starting Stock Price Monitoring...');
        
        // Backfill existing requests if requested
        if ($this->option('backfill')) {
            $this->info('📊 Backfilling existing requests...');
            $backfilledCount = $monitoringService->backfillExistingRequests();
            $this->info("✅ Backfilled {$backfilledCount} requests with target data");
        }
        
        // Monitor active requests
        $results = $monitoringService->monitorAllActiveRequests();
        
        $this->info("📈 Monitored " . count($results) . " active requests");
        
        // Display results
        foreach ($results as $result) {
            if ($result['status'] === 'error') {
                $this->error("❌ Request {$result['request_id']}: {$result['error']}");
            } elseif (in_array($result['status'], ['win', 'super_win', 'loss'])) {
                $emoji = $result['status'] === 'super_win' ? '🏆' : ($result['status'] === 'win' ? '✅' : '❌');
                $this->info("{$emoji} Request {$result['request_id']}: " . strtoupper($result['status']) . " at {$result['current_price']}");
            } else {
                $this->line("⏳ Request {$result['request_id']}: Monitoring at {$result['current_price']}");
            }
        }
        
        // Show winning rate stats
        $stats = $monitoringService->getWinningRateStats();
        if ($stats['total_requests'] > 0) {
            $this->info("📊 AI Accuracy: {$stats['winning_rate']}% ({$stats['total_wins']} Win, {$stats['losses']} Loss, {$stats['timeouts']} Timeout)");
        }
        
        $this->info('✅ Monitoring completed!');
        
        return Command::SUCCESS;
    }
}
