<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Request as StockRequest;
use Carbon\Carbon;

/**
 * Price Monitoring Service
 * 
 * Monitors real-time stock prices and determines WIN/LOSS results
 * based on AI trading advice targets and stop loss levels
 */
class PriceMonitoringService
{
    private YahooFinanceService $yahooService;
    
    public function __construct(YahooFinanceService $yahooService)
    {
        $this->yahooService = $yahooService;
    }

    /**
     * Monitor all active requests (MONITORING status)
     */
    public function monitorAllActiveRequests(): array
    {
        // TIMEZONE FIX: Use Jakarta timezone for proper comparison with monitoring_until
        $jakartaNow = now()->setTimezone('Asia/Jakarta');

        $activeRequests = StockRequest::where('result', 'MONITORING')
            ->where('monitoring_until', '>', $jakartaNow)
            ->whereNotNull('entry_price')
            ->get();

        Log::info('Starting price monitoring for active requests', [
            'count' => $activeRequests->count()
        ]);

        $results = [];
        
        foreach ($activeRequests as $request) {
            $result = $this->monitorSingleRequest($request);
            $results[] = $result;
        }

        // Check for timeout requests
        $this->processTimeoutRequests();
        
        return $results;
    }

    /**
     * Monitor single request for price targets
     */
    public function monitorSingleRequest(StockRequest $request): array
    {
        try {
            Log::info('Monitoring request', [
                'request_id' => $request->id,
                'stock_code' => $request->stock_code,
                'entry_price' => $request->entry_price,
                'target_1' => $request->target_1,
                'target_2' => $request->target_2,
                'stop_loss' => $request->stop_loss
            ]);

            // Get current price
            $currentPrice = $this->getCurrentPrice($request->stock_code);
            
            if (!$currentPrice) {
                Log::warning('Failed to get current price', [
                    'request_id' => $request->id,
                    'stock_code' => $request->stock_code
                ]);
                
                return [
                    'request_id' => $request->id,
                    'status' => 'price_fetch_failed',
                    'current_price' => null
                ];
            }

            // Update highest price reached
            if (!$request->highest_price_reached || $currentPrice > $request->highest_price_reached) {
                $request->update(['highest_price_reached' => $currentPrice]);
            }

            // Check for results
            $result = $this->evaluateResult($request, $currentPrice);
            
            if ($result['status'] !== 'monitoring') {
                $this->updateRequestResult($request, $result, $currentPrice);
            }

            return [
                'request_id' => $request->id,
                'status' => $result['status'],
                'current_price' => $currentPrice,
                'entry_price' => $request->entry_price,
                'target_1' => $request->target_1,
                'target_2' => $request->target_2,
                'stop_loss' => $request->stop_loss,
                'result_details' => $result
            ];

        } catch (\Exception $e) {
            Log::error('Error monitoring request', [
                'request_id' => $request->id,
                'error' => $e->getMessage()
            ]);

            return [
                'request_id' => $request->id,
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get current stock price from Yahoo Finance
     */
    private function getCurrentPrice(string $stockCode): ?float
    {
        try {
            $yahooData = $this->yahooService->getStockData($stockCode, '1d');
            
            if (!$yahooData) {
                return null;
            }

            $stockData = $this->yahooService->extractRelevantData($yahooData);
            return (float) $stockData['current_price'];

        } catch (\Exception $e) {
            Log::error('Failed to get current price', [
                'stock_code' => $stockCode,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Evaluate if target or stop loss has been hit
     */
    private function evaluateResult(StockRequest $request, float $currentPrice): array
    {
        // Check stop loss first (highest priority)
        if ($request->stop_loss && $currentPrice <= $request->stop_loss) {
            return [
                'status' => 'loss',
                'reason' => 'stop_loss_hit',
                'price_achieved' => $currentPrice,
                'target_hit' => 'stop_loss'
            ];
        }

        // Check Target 2 (Super Win)
        if ($request->target_2 && $currentPrice >= $request->target_2) {
            return [
                'status' => 'super_win',
                'reason' => 'target_2_achieved',
                'price_achieved' => $currentPrice,
                'target_hit' => 'target_2'
            ];
        }

        // Check Target 1 (Win)
        if ($request->target_1 && $currentPrice >= $request->target_1) {
            return [
                'status' => 'win',
                'reason' => 'target_1_achieved', 
                'price_achieved' => $currentPrice,
                'target_hit' => 'target_1'
            ];
        }

        // Still monitoring
        return [
            'status' => 'monitoring',
            'reason' => 'targets_not_reached',
            'price_achieved' => $currentPrice,
            'target_hit' => null
        ];
    }

    /**
     * Update request with final result
     */
    private function updateRequestResult(StockRequest $request, array $result, float $currentPrice): void
    {
        $status_map = [
            'win' => 'WIN',
            'super_win' => 'SUPER_WIN', 
            'loss' => 'LOSS'
        ];

        $request->update([
            'result' => $status_map[$result['status']],
            'highest_price_reached' => max($request->highest_price_reached ?? 0, $currentPrice),
            'result_achieved_at' => now()
        ]);

        Log::info('Request result updated', [
            'request_id' => $request->id,
            'result' => $status_map[$result['status']],
            'reason' => $result['reason'],
            'price_achieved' => $currentPrice,
            'entry_price' => $request->entry_price,
            'target_hit' => $result['target_hit']
        ]);
    }

    /**
     * Process requests that have exceeded monitoring time (timeout)
     */
    public function processTimeoutRequests(): int
    {
        // TIMEZONE FIX: Use Jakarta timezone for proper comparison with monitoring_until
        $jakartaNow = now()->setTimezone('Asia/Jakarta');

        $timeoutRequests = StockRequest::where('result', 'MONITORING')
            ->where('monitoring_until', '<=', $jakartaNow)
            ->get();

        foreach ($timeoutRequests as $request) {
            $request->update([
                'result' => 'TIMEOUT',
                'result_achieved_at' => now()
            ]);

            Log::info('Request timed out', [
                'request_id' => $request->id,
                'stock_code' => $request->stock_code,
                'monitoring_until' => $request->monitoring_until,
                'highest_price_reached' => $request->highest_price_reached
            ]);
        }

        return $timeoutRequests->count();
    }

    /**
     * Get winning rate statistics
     */
    public function getWinningRateStats(): array
    {
        // Only count WIN, SUPER_WIN, and LOSS - exclude TIMEOUT from accuracy calculation
        $totalRequests = StockRequest::whereIn('result', ['WIN', 'SUPER_WIN', 'LOSS'])->count();
        
        if ($totalRequests === 0) {
            return [
                'winning_rate' => 0,
                'total_requests' => 0,
                'wins' => 0,
                'super_wins' => 0,
                'losses' => 0,
                'timeouts' => 0
            ];
        }

        $wins = StockRequest::where('result', 'WIN')->count();
        $superWins = StockRequest::where('result', 'SUPER_WIN')->count();
        $losses = StockRequest::where('result', 'LOSS')->count();
        $timeouts = StockRequest::where('result', 'TIMEOUT')->count(); // For display only
        
        $totalWins = $wins + $superWins;
        $winningRate = $totalRequests > 0 ? ($totalWins / $totalRequests) * 100 : 0;

        return [
            'winning_rate' => round($winningRate, 1),
            'total_requests' => $totalRequests,
            'wins' => $wins,
            'super_wins' => $superWins, 
            'losses' => $losses,
            'timeouts' => $timeouts, // For display only, not included in accuracy
            'total_wins' => $totalWins
        ];
    }

    /**
     * Extract targets from AI advice text for existing requests
     */
    public function extractTargetsFromAdvice(string $advice): array
    {
        $targets = [
            'entry_price' => null,
            'target_1' => null,
            'target_2' => null,
            'stop_loss' => null
        ];

        // Check if action is Hold first - if Hold, don't extract any targets for monitoring
        if (preg_match('/Action:\s*Hold/i', $advice) || 
            strpos(strtolower($advice), 'recommendation: hold') !== false ||
            strpos(strtolower($advice), 'action: hold') !== false) {
            // For Hold recommendations, return all null targets (no monitoring needed)
            return $targets;
        }

        // NEWEST FORMAT: Extract from lines like "Action: Buy at IDR 4,030"
        if (preg_match('/Action:\s*Buy at\s*(?:IDR|Rp)\s*([\d,]+\.?\d*)/i', $advice, $matches)) {
            $targets['entry_price'] = (float) str_replace(',', '', $matches[1]);
        }
        
        // NEW FORMAT: Extract from lines like "Entry: IDR 8,800.00" (fallback)
        if (!$targets['entry_price'] && preg_match('/Entry:\s*(?:IDR|Rp)\s*([\d,]+\.?\d*)/i', $advice, $matches)) {
            $targets['entry_price'] = (float) str_replace(',', '', $matches[1]);
        }
        
        // NEW FORMAT: Extract from lines like "Target 1: IDR 8,932.00"
        if (preg_match('/Target 1:\s*(?:IDR|Rp)\s*([\d,]+\.?\d*)/i', $advice, $matches)) {
            $targets['target_1'] = (float) str_replace(',', '', $matches[1]);
        }
        
        // NEW FORMAT: Extract from lines like "Target 2: IDR 9,064.00"
        if (preg_match('/Target 2:\s*(?:IDR|Rp)\s*([\d,]+\.?\d*)/i', $advice, $matches)) {
            $targets['target_2'] = (float) str_replace(',', '', $matches[1]);
        }
        
        // NEW FORMAT: Extract from lines like "Stop Loss: IDR 8,624.00"
        if (preg_match('/Stop Loss:\s*(?:IDR|Rp)\s*([\d,]+\.?\d*)/i', $advice, $matches)) {
            $targets['stop_loss'] = (float) str_replace(',', '', $matches[1]);
        }

        // FALLBACK: Old format patterns (keeping for backward compatibility)
        if (!$targets['entry_price'] && preg_match('/Set Buy:\s*(?:IDR|Rp)\s*([\d,]+\.?\d*)/i', $advice, $matches)) {
            $targets['entry_price'] = (float) str_replace(',', '', $matches[1]);
        }
        
        if (!$targets['target_1'] && preg_match('/Set Sell 1:\s*(?:IDR|Rp)\s*([\d,]+\.?\d*)/i', $advice, $matches)) {
            $targets['target_1'] = (float) str_replace(',', '', $matches[1]);
        }

        // Extract target 2 (new format: "Set Sell 2:")
        if (preg_match('/Set Sell 2:\s*(?:IDR|Rp)\s*([\d,]+\.?\d*)/i', $advice, $matches)) {
            $targets['target_2'] = (float) str_replace(',', '', $matches[1]);
        }
        
        // Fallback for old format  
        if (!$targets['target_2'] && preg_match('/Set Target Hits 2:\s*(?:IDR|Rp)\s*([\d,]+\.?\d*)/i', $advice, $matches)) {
            $targets['target_2'] = (float) str_replace(',', '', $matches[1]);
        }

        // Extract stop loss
        if (preg_match('/Set Stop Loss:\s*(?:IDR|Rp)\s*([\d,]+\.?\d*)/i', $advice, $matches)) {
            $targets['stop_loss'] = (float) str_replace(',', '', $matches[1]);
        }

        return $targets;
    }

    /**
     * Backfill existing requests with target data from advice
     */
    public function backfillExistingRequests(): int
    {
        $requests = StockRequest::whereNull('entry_price')
            ->whereNotNull('advice')
            ->where('advice', '!=', '')
            ->get();

        $updated = 0;

        foreach ($requests as $request) {
            $targets = $this->extractTargetsFromAdvice($request->advice);
            
            if ($targets['entry_price']) {
                $request->update([
                    'entry_price' => $targets['entry_price'],
                    'target_1' => $targets['target_1'],
                    'target_2' => $targets['target_2'],
                    'stop_loss' => $targets['stop_loss'],
                    'monitoring_until' => $this->calculateMonitoringEndTime($request->created_at, $request->timeframe),
                    'result' => now() > $request->created_at->addHour() ? 'TIMEOUT' : 'MONITORING'
                ]);
                $updated++;
            }
        }

        Log::info('Backfilled existing requests with target data', [
            'updated_count' => $updated
        ]);

        return $updated;
    }

    /**
     * Calculate monitoring end time based on user-selected timeframe
     */
    private function calculateMonitoringEndTime(Carbon $createdAt, string $timeframe): Carbon
    {
        return match($timeframe) {
            '1h' => $createdAt->copy()->addHour(),
            '1d' => $createdAt->copy()->addDay(),
            '1w' => $createdAt->copy()->addWeek(),
            '1m' => $createdAt->copy()->addMonth(),
            default => $createdAt->copy()->addHour() // Fallback
        };
    }
}