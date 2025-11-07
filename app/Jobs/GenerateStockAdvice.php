<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Request as StockRequest;
use App\Models\User;
use App\Services\YahooFinanceService;
use App\Services\AlphaVantageService;
use App\Services\TechnicalAnalysisService;
use App\Services\ChatGPTService;
use App\Services\QwenService;
use App\Services\PriceMonitoringService;
use App\Jobs\SendStockAdviceEmail;
use Illuminate\Support\Facades\Log;

class GenerateStockAdvice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The maximum number of seconds the job can run before timing out.
     */
    public $timeout = 180; // 3 minutes default, will be adjusted per timeframe

    protected StockRequest $stockRequest;

    public function __construct(StockRequest $stockRequest)
    {
        $this->stockRequest = $stockRequest;

        // Adjust timeout based on timeframe to protect against long-running jobs
        $this->timeout = match($stockRequest->timeframe) {
            '1h' => 120,   // 2 minutes for hourly
            '1d' => 300,   // 5 minutes for daily
            '1w' => 600,   // 10 minutes for weekly (more data to analyze)
            '1m' => 900,   // 15 minutes for monthly (much more data)
            default => 120 // Fallback to 2 minutes
        };
    }

    public function handle(YahooFinanceService $yahooService, AlphaVantageService $alphaVantageService, TechnicalAnalysisService $technicalService, ChatGPTService $chatgptService, QwenService $qwenService, PriceMonitoringService $monitoringService): void
    {
        try {
            Log::info('Starting stock advice generation', [
                'request_id' => $this->stockRequest->id,
                'stock_code' => $this->stockRequest->stock_code,
                'timeframe' => $this->stockRequest->timeframe
            ]);

            // Get stock data from Yahoo Finance
            $yahooData = $yahooService->getStockData(
                $this->stockRequest->stock_code, 
                $this->stockRequest->timeframe
            );

            if (!$yahooData) {
                Log::error('Failed to get Yahoo Finance data', [
                    'request_id' => $this->stockRequest->id
                ]);
                $this->updateRequestWithError('Failed to fetch stock data from Yahoo Finance');
                return;
            }

            // Extract relevant data
            $stockData = $yahooService->extractRelevantData($yahooData);

            // Get comprehensive data from Alpha Vantage for technical analysis
            $alphaVantageData = $alphaVantageService->getComprehensiveStockData($this->stockRequest->stock_code);
            
            // Perform technical analysis
            $technicalAnalysis = [];
            try {
                if ($alphaVantageData && isset($alphaVantageData['daily_data'])) {
                    Log::info('Performing comprehensive technical analysis', [
                        'request_id' => $this->stockRequest->id,
                        'data_points' => count($alphaVantageData['daily_data'])
                    ]);
                    
                    // Use Alpha Vantage OHLCV data for scalping analysis
                    $ohlcvData = $alphaVantageData['daily_data'];
                    $technicalAnalysis = $technicalService->getScalpingAnalysis($ohlcvData, $this->stockRequest->stock_code, $this->stockRequest->timeframe);
                    
                    // Merge Alpha Vantage technical indicators if available
                    if (isset($alphaVantageData['technical_indicators'])) {
                        $formattedIndicators = $alphaVantageService->formatTechnicalIndicators($alphaVantageData['technical_indicators']);
                        $technicalAnalysis = array_merge($technicalAnalysis, $formattedIndicators);
                    }
                } else {
                    Log::warning('Using basic Yahoo Finance data for technical analysis', [
                        'request_id' => $this->stockRequest->id
                    ]);
                    
                    // Fallback: use Yahoo Finance data for scalping analysis
                    $ohlcvData = $this->convertYahooToOHLCV($yahooData);
                    $technicalAnalysis = $technicalService->getScalpingAnalysis($ohlcvData, $this->stockRequest->stock_code, $this->stockRequest->timeframe);
                }
            } catch (\DivisionByZeroError $e) {
                Log::error('Division by zero in technical analysis', [
                    'request_id' => $this->stockRequest->id,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                $this->updateRequestWithError('Division by zero in technical analysis: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
                return;
            } catch (\Exception $e) {
                Log::error('Error in technical analysis', [
                    'request_id' => $this->stockRequest->id,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                $this->updateRequestWithError('Error in technical analysis: ' . $e->getMessage());
                return;
            }

            Log::info('Technical analysis completed', [
                'request_id' => $this->stockRequest->id,
                'indicators_count' => count($technicalAnalysis)
            ]);

            // Generate triple advice (Claude + ChatGPT + Qwen) with comprehensive technical analysis
            try {
                Log::info('About to call generateStockAdvice for all 3 AI services', ['request_id' => $this->stockRequest->id]);

                // Get Claude + ChatGPT advice
                $dualAdvice = $chatgptService->generateStockAdvice(
                    $stockData,
                    $technicalAnalysis,
                    $this->stockRequest->timeframe,
                    $this->stockRequest->action ?? 'BUY',
                    $this->stockRequest->purchase_price
                );

                // Get Qwen advice separately
                $qwenAdvice = $qwenService->generateStockAdvice(
                    $stockData,
                    $technicalAnalysis,
                    $this->stockRequest->timeframe,
                    $this->stockRequest->action ?? 'BUY',
                    $this->stockRequest->purchase_price
                );

                Log::info('All AI services completed successfully', [
                    'request_id' => $this->stockRequest->id,
                    'claude_success' => isset($dualAdvice['claude']),
                    'chatgpt_success' => isset($dualAdvice['chatgpt']),
                    'qwen_success' => !empty($qwenAdvice)
                ]);
            } catch (\DivisionByZeroError $e) {
                Log::error('Division by zero in generateStockAdvice', [
                    'request_id' => $this->stockRequest->id,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                $this->updateRequestWithError('Division by zero error in advice generation: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
                return;
            } catch (\Exception $e) {
                Log::error('General error in generateStockAdvice', [
                    'request_id' => $this->stockRequest->id,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                $this->updateRequestWithError('Error in advice generation: ' . $e->getMessage());
                return;
            }

            if (!$dualAdvice || !isset($dualAdvice['claude'])) {
                Log::error('Failed to generate dual advice', [
                    'request_id' => $this->stockRequest->id
                ]);
                $this->updateRequestWithError('Failed to generate AI advice');
                return;
            }

            // Use Claude advice for monitoring (consistent format)
            $claudeAdvice = $dualAdvice['claude'];
            $chatgptAdvice = $dualAdvice['chatgpt'] ?? null;

            // Extract targets from Claude advice (deterministic format)
            $targets = $monitoringService->extractTargetsFromAdvice($claudeAdvice);

            // Update the request with all 3 advice types and targets
            $updateData = [
                'advice' => $claudeAdvice,
                'advice_chatgpt' => $chatgptAdvice,
                'advice_qwen' => $qwenAdvice ?? null
            ];
            
            if ($targets['entry_price']) {
                // Only set monitoring for actionable recommendations (Buy/Sell)
                $updateData = array_merge($updateData, [
                    'entry_price' => $targets['entry_price'],
                    'target_1' => $targets['target_1'],
                    'target_2' => $targets['target_2'],
                    'stop_loss' => $targets['stop_loss'],
                    'monitoring_until' => $this->calculateMonitoringEndTime($this->stockRequest->timeframe),
                    'result' => 'MONITORING'
                ]);
            } else {
                // For Hold recommendations or no actionable targets, set as completed
                $updateData['result'] = null; // Keep result as null (no status needed for Hold)
            }
            
            $this->stockRequest->update($updateData);

            Log::info('Successfully generated triple advice with targets', [
                'request_id' => $this->stockRequest->id,
                'claude_advice_length' => strlen($claudeAdvice),
                'chatgpt_advice_length' => $chatgptAdvice ? strlen($chatgptAdvice) : 0,
                'qwen_advice_length' => $qwenAdvice ? strlen($qwenAdvice) : 0,
                'entry_price' => $targets['entry_price'] ?? null,
                'target_1' => $targets['target_1'] ?? null,
                'target_2' => $targets['target_2'] ?? null,
                'stop_loss' => $targets['stop_loss'] ?? null
            ]);

            // Send advice email to user (use Claude advice for email)
            Log::info('About to send advice email', [
                'request_id' => $this->stockRequest->id
            ]);
            $this->sendAdviceEmail($claudeAdvice);

        } catch (\Exception $e) {
            Log::error('Error generating stock advice', [
                'request_id' => $this->stockRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->updateRequestWithError('An error occurred while generating advice: ' . $e->getMessage());
        }
    }

    private function updateRequestWithError(string $errorMessage): void
    {
        $this->stockRequest->update([
            'advice' => "Error: {$errorMessage}. Please try again later or contact support."
        ]);
    }

    private function sendAdviceEmail(string $advice): void
    {
        Log::info('sendAdviceEmail method called', [
            'request_id' => $this->stockRequest->id,
            'user_id' => $this->stockRequest->user_id
        ]);
        
        try {
            // Get user from request
            $user = User::find($this->stockRequest->user_id);
            
            if (!$user) {
                Log::warning('User not found for advice email', [
                    'request_id' => $this->stockRequest->id,
                    'user_id' => $this->stockRequest->user_id
                ]);
                return;
            }

            // Dispatch email job
            SendStockAdviceEmail::dispatch($user, $this->stockRequest);
            
            Log::info('Stock advice email job dispatched', [
                'request_id' => $this->stockRequest->id,
                'user_email' => $user->email
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to dispatch advice email job', [
                'request_id' => $this->stockRequest->id,
                'error' => $e->getMessage()
            ]);
            // Don't throw exception - email failure shouldn't break advice generation
        }
    }

    /**
     * Convert Yahoo Finance data format to OHLCV array for technical analysis
     */
    private function convertYahooToOHLCV(array $yahooData): array
    {
        $result = $yahooData['chart']['result'][0];
        $timestamps = $result['timestamp'] ?? [];
        $indicators = $result['indicators']['quote'][0] ?? [];
        $meta = $result['meta'] ?? [];
        
        $ohlcvData = [];
        
        // If no historical data available, create minimal data for current analysis
        if (empty($timestamps)) {
            $currentPrice = $meta['regularMarketPrice'] ?? 0;
            return [[
                'date' => date('Y-m-d'),
                'open' => $currentPrice,
                'high' => $currentPrice,
                'low' => $currentPrice,
                'close' => $currentPrice,
                'volume' => $meta['regularMarketVolume'] ?? 0,
            ]];
        }
        
        for ($i = 0; $i < count($timestamps); $i++) {
            $ohlcvData[] = [
                'date' => date('Y-m-d', $timestamps[$i]),
                'open' => ($indicators['open'][$i] ?? null) ?? 0,
                'high' => ($indicators['high'][$i] ?? null) ?? 0,
                'low' => ($indicators['low'][$i] ?? null) ?? 0,
                'close' => ($indicators['close'][$i] ?? null) ?? 0,
                'volume' => ($indicators['volume'][$i] ?? null) ?? 0,
            ];
        }
        
        // Add real-time current data as most recent entry (index 0 after sorting)
        // This ensures technical analysis uses same prices as UI display
        $currentData = [
            'date' => date('Y-m-d'),
            'open' => $meta['regularMarketPrice'], // Use current price as approximation
            'high' => $meta['regularMarketDayHigh'] ?? $meta['regularMarketPrice'],
            'low' => $meta['regularMarketDayLow'] ?? $meta['regularMarketPrice'], 
            'close' => $meta['regularMarketPrice'], // CURRENT PRICE = regularMarketPrice
            'volume' => $meta['regularMarketVolume'] ?? 0,
        ];
        
        // Add previous close data as second entry to ensure consistent price change calculation
        // This ensures technical analysis uses same previous_close as UI display
        $previousClose = $meta['previousClose'] ?? $meta['chartPreviousClose'] ?? $meta['regularMarketPrice'] ?? 0;
        $previousData = [
            'date' => date('Y-m-d', strtotime('-1 day')),
            'open' => $previousClose,
            'high' => $previousClose,
            'low' => $previousClose,
            'close' => $previousClose, // PREVIOUS PRICE = previousClose with fallback
            'volume' => 0,
        ];
        
        // Prepend both current and previous data to the beginning of array
        array_unshift($ohlcvData, $previousData); // Index 1 after sort
        array_unshift($ohlcvData, $currentData);  // Index 0 after sort
        
        // Sort by date (most recent first for consistency with Alpha Vantage)
        usort($ohlcvData, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return $ohlcvData;
    }

    /**
     * Calculate monitoring end time based on user-selected timeframe
     */
    private function calculateMonitoringEndTime(string $timeframe): \Carbon\Carbon
    {
        return match($timeframe) {
            '1h' => now()->addHour(),
            '1d' => now()->addDay(),
            '1w' => now()->addWeek(),
            '1m' => now()->addMonth(),
            default => now()->addHour() // Fallback
        };
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('GenerateStockAdvice job failed permanently', [
            'request_id' => $this->stockRequest->id,
            'stock_code' => $this->stockRequest->stock_code,
            'timeframe' => $this->stockRequest->timeframe,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Could optionally mark request as failed or notify admin
        // But keeping existing data intact for manual retry
    }
} 