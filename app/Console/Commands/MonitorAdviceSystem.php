<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Request as StockRequest;
use App\Jobs\GenerateStockAdvice;

class MonitorAdviceSystem extends Command
{
    protected $signature = 'advice:monitor';
    protected $description = 'Monitor the AI advice system status';

    public function handle()
    {
        $this->info('🔍 AI STOCK ADVISOR - SYSTEM MONITOR');
        $this->info('=====================================');
        $this->newLine();

        // Check total requests
        $totalRequests = StockRequest::count();
        $this->info("📊 Total Requests: {$totalRequests}");

        // Check requests with advice
        $requestsWithAdvice = StockRequest::whereNotNull('advice')->where('advice', '!=', '')->count();
        $this->info("✅ Requests with Advice: {$requestsWithAdvice}");

        // Check pending requests
        $pendingRequests = StockRequest::whereNull('advice')->orWhere('advice', '')->count();
        $this->info("⏳ Pending Requests: {$pendingRequests}");

        // Check jobs in queue
        $jobsInQueue = \Illuminate\Support\Facades\DB::table('jobs')->count();
        $this->info("📋 Jobs in Queue: {$jobsInQueue}");

        // Check failed jobs
        $failedJobs = \Illuminate\Support\Facades\DB::table('failed_jobs')->count();
        $this->info("❌ Failed Jobs: {$failedJobs}");

        $this->newLine();

        // Calculate success rate
        if ($totalRequests > 0) {
            $successRate = round(($requestsWithAdvice / $totalRequests) * 100, 2);
            $this->info("📈 Success Rate: {$successRate}%");
        }

        $this->newLine();

        // Show recent requests
        $this->info('📋 Recent Requests:');
        $recentRequests = StockRequest::latest()->take(5)->get();
        
        foreach ($recentRequests as $request) {
            $status = $request->advice ? '✅' : '⏳';
            $this->line("  {$status} ID {$request->id}: {$request->stock_code} - {$request->created_at->format('Y-m-d H:i')}");
        }

        $this->newLine();

        // Recommendations
        if ($pendingRequests > 0) {
            $this->warn("⚠️  Found {$pendingRequests} pending requests.");
            $this->info("💡 Run: php artisan advice:process-pending");
        }

        if ($jobsInQueue > 0) {
            $this->warn("⚠️  Found {$jobsInQueue} jobs in queue.");
            $this->info("💡 Make sure queue worker is running: php artisan queue:work");
        }

        if ($failedJobs > 0) {
            $this->error("❌ Found {$failedJobs} failed jobs.");
            $this->info("💡 Check failed jobs: php artisan queue:failed");
        }

        $this->newLine();
        $this->info('🎯 System Status: ' . ($pendingRequests == 0 && $failedJobs == 0 ? 'HEALTHY' : 'NEEDS ATTENTION'));
    }
} 