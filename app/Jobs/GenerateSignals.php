<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Signal;
use App\Services\YahooFinanceService;
use App\Services\TechnicalAnalysisService;
use App\Services\StockService;
use App\Services\ChatGPTService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GenerateSignals implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The maximum number of seconds the job can run before timing out.
     */
    public $timeout = 300; // 5 minutes for full scan

    /**
     * Top 50 most liquid IDX stocks to monitor
     */
    private $topStocks = [
        'BBCA.JK', 'BBRI.JK', 'BMRI.JK', 'TLKM.JK', 'ASII.JK',
        'UNVR.JK', 'GGRM.JK', 'ICBP.JK', 'KLBF.JK', 'INTP.JK',
        'SMGR.JK', 'ADRO.JK', 'PTBA.JK', 'ANTM.JK', 'ITMG.JK',
        'CPIN.JK', 'JPFA.JK', 'SIDO.JK', 'KAEF.JK', 'MYOR.JK',
        'ULTJ.JK', 'INDF.JK', 'UNTR.JK', 'AALI.JK', 'LSIP.JK',
        'CTRA.JK', 'PGAS.JK', 'PWON.JK', 'ERAA.JK', 'GEMS.JK',
        'WSKT.JK', 'WIKA.JK', 'PTPP.JK', 'ADHI.JK', 'DGIK.JK',
        'EXCL.JK', 'FREN.JK', 'ISAT.JK', 'TOWR.JK', 'TAXI.JK',
        'MCAS.JK', 'MAPI.JK', 'LPPF.JK', 'TPIA.JK', 'AKRA.JK',
        'HERO.JK', 'AMRT.JK', 'COCO.JK', 'CSAP.JK', 'ALTO.JK'
    ];

    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(YahooFinanceService $yahooService, TechnicalAnalysisService $technicalService, StockService $stockService, ChatGPTService $chatgptService): void
    {
        try {
            Log::info('Starting signals generation', [
                'timestamp' => now()->toDateTimeString(),
                'stocks_to_scan' => count($this->topStocks)
            ]);

            $signalsGenerated = 0;
            $errors = 0;

            // Clear expired signals first
            $this->clearExpiredSignals();

            foreach ($this->topStocks as $stockCode) {
                try {
                    $timeframes = ['1h', '1d'];

                    foreach ($timeframes as $timeframe) {
                        $signal = $this->analyzeStock($stockCode, $timeframe, $yahooService, $technicalService, $stockService, $chatgptService);

                        if ($signal && $signal['confidence_percentage'] >= 60) {
                            $this->storeSignal($signal);
                            $signalsGenerated++;

                            Log::info('Signal generated', [
                                'stock_code' => $stockCode,
                                'timeframe' => $timeframe,
                                'confidence' => $signal['confidence_percentage']
                            ]);
                        }

                        // Small delay to be respectful to APIs
                        usleep(100000); // 0.1 second
                    }
                } catch (\Exception $e) {
                    $errors++;
                    Log::warning('Error analyzing stock', [
                        'stock_code' => $stockCode,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Signals generation completed', [
                'signals_generated' => $signalsGenerated,
                'errors' => $errors,
                'duration' => 'completed'
            ]);

        } catch (\Exception $e) {
            Log::error('Fatal error in signals generation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Analyze a stock for trading signals
     */
    private function analyzeStock($stockCode, $timeframe, $yahooService, $technicalService, $stockService, $chatgptService)
    {
        // Skip if we already have a recent signal for this stock/timeframe
        $existingSignal = Signal::where('stock_code', $stockCode)
            ->where('timeframe', $timeframe)
            ->where('status', 'active')
            ->where('created_at', '>', now()->subHours(2))
            ->first();

        if ($existingSignal) {
            return null;
        }

        // Get stock data
        $stockData = $yahooService->getStockData($stockCode, $timeframe);
        if (!$stockData) {
            return null;
        }

        // Extract relevant data
        $relevantData = $yahooService->extractRelevantData($stockData);
        if (!$relevantData) {
            return null;
        }

        $currentPrice = $relevantData['current_price'];
        $volume = $relevantData['volume'] ?? 0;

        if (!$currentPrice || $currentPrice <= 0) {
            return null;
        }

        // Extract historical data for technical analysis
        $result = $stockData['chart']['result'][0];
        $indicators = $result['indicators']['quote'][0] ?? [];

        $prices = $indicators['close'] ?? [];
        $highs = $indicators['high'] ?? [];
        $lows = $indicators['low'] ?? [];

        // Filter out null values and ensure we have enough data
        $prices = array_filter($prices, function($price) { return $price !== null && $price > 0; });
        $highs = array_filter($highs, function($high) { return $high !== null && $high > 0; });
        $lows = array_filter($lows, function($low) { return $low !== null && $low > 0; });

        if (count($prices) < 20 || count($highs) < 20 || count($lows) < 20) {
            return null;
        }

        // Convert to indexed arrays for technical analysis
        $prices = array_values($prices);
        $highs = array_values($highs);
        $lows = array_values($lows);

        // Calculate technical indicators
        $rsi = $technicalService->calculateRSI($prices);
        $macd = $technicalService->calculateMACD($prices);
        $bb = $technicalService->calculateBollingerBands($prices);
        $sma9 = $technicalService->calculateSMA($prices, 9);
        $ema9 = $technicalService->calculateEMA($prices, 9);
        $stoch = $technicalService->calculateStochastic($highs, $lows, $prices);

        // Calculate signal score and confidence
        $analysis = $this->calculateSignalScore($currentPrice, $rsi, $macd, $bb, $sma9, $ema9, $stoch, $volume);

        if (!$analysis || $analysis['confidence_percentage'] < 60) {
            return null;
        }

        // Calculate entry, targets, and stop loss
        $levels = $this->calculateTradingLevels($currentPrice, $bb, $timeframe);

        // Get ChatGPT analysis for signal
        $chatgptAnalysis = $this->getChatGPTSignalAnalysis(
            $stockCode,
            $currentPrice,
            $levels,
            $rsi,
            $macd,
            $volume,
            $timeframe,
            $chatgptService
        );

        return [
            'stock_code' => $stockCode,
            'company_name' => $stockService->getCompanyName($stockCode),
            'timeframe' => $timeframe,
            'current_price' => $currentPrice,
            'entry_price' => $levels['entry'],
            'target_1' => $levels['target_1'],
            'target_2' => $levels['target_2'],
            'stop_loss' => $levels['stop_loss'],
            'risk_reward' => $levels['risk_reward'],
            'confidence_level' => $analysis['confidence_level'],
            'confidence_percentage' => $analysis['confidence_percentage'],
            'analysis_reason' => $analysis['reason'],
            'chatgpt_reason' => $chatgptAnalysis['reason'] ?? null,
            'chatgpt_confidence_percentage' => $chatgptAnalysis['confidence'] ?? null,
            'rsi' => $rsi,
            'macd_signal' => $macd['macd'] > 0 ? 'Buy' : 'Sell',
            'volume' => $volume,
            'scalping_score' => $analysis['scalping_score'],
            'expires_at' => $timeframe === '1h' ? now()->addHours(2) : now()->addHours(24),
        ];
    }

    /**
     * Calculate signal score based on technical indicators
     */
    private function calculateSignalScore($currentPrice, $rsi, $macd, $bb, $sma9, $ema9, $stoch, $volume)
    {
        $score = 0;
        $maxScore = 100;
        $reasons = [];

        // RSI Analysis (25 points)
        if ($rsi) {
            if ($rsi >= 30 && $rsi <= 70) {
                $score += 25;
                $reasons[] = "RSI in healthy zone ({$rsi})";
            } elseif ($rsi < 30) {
                $score += 15;
                $reasons[] = "RSI oversold ({$rsi}) - potential bounce";
            } else {
                $score += 5;
                $reasons[] = "RSI overbought ({$rsi}) - risky";
            }
        }

        // MACD Analysis (25 points)
        if ($macd && $macd['macd'] !== null) {
            if ($macd['macd'] > 0 && $macd['histogram'] > 0) {
                $score += 25;
                $reasons[] = "MACD bullish momentum";
            } elseif ($macd['macd'] > 0) {
                $score += 15;
                $reasons[] = "MACD above zero line";
            } else {
                $score += 5;
                $reasons[] = "MACD bearish";
            }
        }

        // Price vs Moving Averages (25 points)
        if ($sma9 && $ema9) {
            if ($currentPrice > $sma9 && $currentPrice > $ema9) {
                $score += 25;
                $reasons[] = "Price above SMA9 and EMA9";
            } elseif ($currentPrice > $sma9 || $currentPrice > $ema9) {
                $score += 15;
                $reasons[] = "Price above one moving average";
            } else {
                $score += 5;
                $reasons[] = "Price below moving averages";
            }
        }

        // Bollinger Bands Analysis (15 points)
        if ($bb && $bb['middle']) {
            if ($currentPrice > $bb['middle'] && $currentPrice < $bb['upper']) {
                $score += 15;
                $reasons[] = "Price in upper BB channel";
            } elseif ($currentPrice > $bb['lower'] && $currentPrice < $bb['middle']) {
                $score += 10;
                $reasons[] = "Price in lower BB channel";
            } else {
                $score += 5;
                $reasons[] = "Price at BB extremes";
            }
        }

        // Volume Analysis (10 points)
        if ($volume > 1000000) {
            $score += 10;
            $reasons[] = "Good trading volume";
        } elseif ($volume > 500000) {
            $score += 5;
            $reasons[] = "Moderate volume";
        }

        $confidencePercentage = min(85, max(50, $score));

        // Determine confidence level
        $confidenceLevel = 'Speculative';
        if ($confidencePercentage >= 80) {
            $confidenceLevel = 'Conservative';
        } elseif ($confidencePercentage >= 75) {
            $confidenceLevel = 'Moderate';
        } elseif ($confidencePercentage >= 65) {
            $confidenceLevel = 'Aggressive';
        }

        // Only return signals with decent confidence
        if ($confidencePercentage < 60) {
            return null;
        }

        return [
            'confidence_percentage' => $confidencePercentage,
            'confidence_level' => $confidenceLevel,
            'scalping_score' => round($score / 10, 1),
            'reason' => implode('. ', $reasons) . '.'
        ];
    }

    /**
     * Calculate trading levels (entry, targets, stop loss)
     */
    private function calculateTradingLevels($currentPrice, $bb, $timeframe)
    {
        // Conservative entry slightly below current price
        $entryPrice = $currentPrice * 0.995; // 0.5% below current

        // Targets based on timeframe
        if ($timeframe === '1h') {
            $target1 = $currentPrice * 1.018; // 1.8% profit
            $target2 = $currentPrice * 1.033; // 3.3% profit
            $stopLoss = $currentPrice * 0.983; // 1.7% loss
        } else {
            $target1 = $currentPrice * 1.025; // 2.5% profit
            $target2 = $currentPrice * 1.045; // 4.5% profit
            $stopLoss = $currentPrice * 0.975; // 2.5% loss
        }

        // Calculate risk reward ratio
        $potentialProfit1 = $target1 - $entryPrice;
        $potentialProfit2 = $target2 - $entryPrice;
        $potentialLoss = $entryPrice - $stopLoss;

        $rr1 = $potentialLoss > 0 ? round($potentialProfit1 / $potentialLoss, 1) : 1;
        $rr2 = $potentialLoss > 0 ? round($potentialProfit2 / $potentialLoss, 1) : 1;

        return [
            'entry' => round($entryPrice, 2),
            'target_1' => round($target1, 2),
            'target_2' => round($target2, 2),
            'stop_loss' => round($stopLoss, 2),
            'risk_reward' => "1:{$rr1} - 1:{$rr2}"
        ];
    }

    /**
     * Get ChatGPT analysis for signal
     */
    private function getChatGPTSignalAnalysis($stockCode, $currentPrice, $levels, $rsi, $macd, $volume, $timeframe, $chatgptService)
    {
        try {
            // Build prompt for ChatGPT
            $prompt = $this->buildChatGPTSignalPrompt($stockCode, $currentPrice, $levels, $rsi, $macd, $volume, $timeframe);

            // Call ChatGPT API (simplified version)
            $response = $chatgptService->generateChatGPTAdvice(
                [
                    'symbol' => $stockCode,
                    'current_price' => $currentPrice,
                    'volume' => $volume,
                    'currency' => 'IDR',
                    'previous_close' => $currentPrice
                ],
                [
                    'entry_price' => $levels['entry'],
                    'target_1' => $levels['target_1'],
                    'target_2' => $levels['target_2'],
                    'stop_loss' => $levels['stop_loss'],
                    'rsi_7' => $rsi,
                    'scalping_score' => 7
                ],
                $timeframe,
                'BUY' // Signals are always BUY recommendations
            );

            // Extract confidence from response
            $confidence = $this->extractConfidenceFromChatGPT($response);

            return [
                'reason' => $response,
                'confidence' => $confidence
            ];
        } catch (\Exception $e) {
            Log::warning('ChatGPT signal analysis failed', [
                'stock_code' => $stockCode,
                'error' => $e->getMessage()
            ]);

            return [
                'reason' => null,
                'confidence' => null
            ];
        }
    }

    /**
     * Build prompt for ChatGPT signal analysis
     */
    private function buildChatGPTSignalPrompt($stockCode, $currentPrice, $levels, $rsi, $macd, $volume, $timeframe)
    {
        $timeframeText = $timeframe === '1h' ? '1 hour scalping' : '1 day trading';

        return "Trading Signal Analysis for {$stockCode}:\n\n" .
               "Timeframe: {$timeframeText}\n" .
               "Current Price: IDR " . number_format($currentPrice, 2) . "\n" .
               "Entry: IDR " . number_format($levels['entry'], 2) . "\n" .
               "Target 1: IDR " . number_format($levels['target_1'], 2) . "\n" .
               "Target 2: IDR " . number_format($levels['target_2'], 2) . "\n" .
               "Stop Loss: IDR " . number_format($levels['stop_loss'], 2) . "\n" .
               "RSI: {$rsi}\n" .
               "MACD: " . ($macd['macd'] > 0 ? 'Bullish' : 'Bearish') . "\n" .
               "Volume: " . number_format($volume) . "\n\n" .
               "Analyze this trading signal and provide your assessment. " .
               "Include confidence level (0-100%) at the end.";
    }

    /**
     * Extract confidence percentage from ChatGPT response
     */
    private function extractConfidenceFromChatGPT($response)
    {
        // Try to find "Confidence Level: XX%" pattern
        if (preg_match('/Confidence Level:\s*(\d+)%/i', $response, $matches)) {
            return (int)$matches[1];
        }

        // Try to find just "XX%" at the end
        if (preg_match('/(\d+)%\s*$/i', $response, $matches)) {
            return (int)$matches[1];
        }

        // Default confidence if not found
        return 70;
    }

    /**
     * Store signal in database
     */
    private function storeSignal($signalData)
    {
        Signal::create($signalData);
    }

    /**
     * Clear expired signals
     */
    private function clearExpiredSignals()
    {
        Signal::where('expires_at', '<', now())
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        Log::info('Expired signals cleared');
    }
}
