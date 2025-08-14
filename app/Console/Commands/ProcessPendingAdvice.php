<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Request as StockRequest;
use App\Jobs\GenerateStockAdvice;

class ProcessPendingAdvice extends Command
{
    protected $signature = 'advice:process-pending';
    protected $description = 'Process all pending stock advice requests';

    public function handle()
    {
        $this->info('🔍 Processing pending advice requests...');

        // Get all requests without advice
        $pendingRequests = StockRequest::whereNull('advice')->orWhere('advice', '')->get();

        if ($pendingRequests->isEmpty()) {
            $this->info('✅ No pending requests found.');
            return;
        }

        $this->info("📋 Found {$pendingRequests->count()} pending requests.");

        $bar = $this->output->createProgressBar($pendingRequests->count());
        $bar->start();

        foreach ($pendingRequests as $request) {
            // Dispatch job for each pending request
            GenerateStockAdvice::dispatch($request);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('✅ All pending requests have been queued for processing.');

        // Check if queue worker is running
        $this->info('💡 Make sure queue worker is running: php artisan queue:work');
    }
} 