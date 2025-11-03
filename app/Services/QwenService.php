<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Qwen Service - Alibaba Cloud Qwen API Integration
 *
 * Provides stock analysis using Alibaba's Qwen AI model
 */
class QwenService
{
    private ?string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.qwen.api_key');
        $this->baseUrl = config('services.qwen.base_url', 'https://dashscope.aliyuncs.com/api/v1/services/aigc/text-generation/generation');
    }

    /**
     * Generate Qwen AI advice for stock analysis
     */
    public function generateStockAdvice(array $stockData, array $technicalAnalysis, string $timeframe): ?string
    {
        if (!$this->apiKey) {
            Log::warning('Qwen API key not configured');
            return "Qwen analysis unavailable - API key not configured.";
        }

        // Build context prompt for AI
        $prompt = $this->buildAIPrompt($stockData, $technicalAnalysis, $timeframe);

        try {
            $response = Http::timeout(config('services.qwen.timeout', 30))
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl, [
                    'model' => config('services.qwen.model', 'qwen-plus'),
                    'input' => [
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => 'You are an expert stock analyst specializing in technical analysis and trading strategies (scalping, day trading, swing trading, position trading) for Indonesian (IDX) and global stocks. Provide concise, actionable trading advice based on real-time data and technical indicators. Always include a confidence level for your analysis.'
                            ],
                            [
                                'role' => 'user',
                                'content' => $prompt
                            ]
                        ]
                    ],
                    'parameters' => [
                        'max_tokens' => config('services.qwen.max_tokens', 1000),
                        'temperature' => 0.3, // Lower temperature for more consistent analysis
                    ]
                ]);

            if ($response->successful()) {
                $result = $response->json();

                // Qwen API response structure: output.text
                $advice = $result['output']['text'] ?? null;

                if ($advice) {
                    Log::info('Successfully generated Qwen stock advice', [
                        'model' => config('services.qwen.model'),
                        'tokens_used' => $result['usage']['total_tokens'] ?? 'unknown'
                    ]);

                    return $advice;
                }
            }

            Log::error('Qwen API failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return "Qwen analysis failed - API error. Status: " . $response->status();

        } catch (\Exception $e) {
            Log::error('Error calling Qwen API', [
                'error' => $e->getMessage()
            ]);

            return "Qwen analysis failed - " . $e->getMessage();
        }
    }

    /**
     * Build AI prompt with comprehensive technical analysis context
     */
    private function buildAIPrompt(array $stockData, array $technicalAnalysis, string $timeframe): string
    {
        // Map timeframe to descriptive text and trading strategy
        $timeframeMapping = match($timeframe) {
            '1h' => ['text' => '1 hour', 'strategy' => 'scalping'],
            '1d' => ['text' => '1 day', 'strategy' => 'day trading'],
            '1w' => ['text' => '1 week', 'strategy' => 'swing trading'],
            '1m' => ['text' => '1 month', 'strategy' => 'position trading'],
            default => ['text' => '1 hour', 'strategy' => 'scalping']
        };

        $timeframeText = $timeframeMapping['text'];
        $strategyType = $timeframeMapping['strategy'];
        $scalpingScore = $technicalAnalysis['scalping_score'] ?? 0;

        // Calculate price changes
        $priceChange = $stockData['current_price'] - $stockData['previous_close'];
        $priceChangePercent = $stockData['previous_close'] > 0 ? (($priceChange / $stockData['previous_close']) * 100) : 0;

        $prompt = "Analyze this stock for {$timeframeText} {$strategyType}:\n\n";

        // Stock basic info
        $prompt .= "**Stock:** " . (isset($stockData['symbol']) ? $stockData['symbol'] : 'N/A') . "\n";
        $prompt .= "**Current Price:** {$stockData['currency']} " . number_format($stockData['current_price'], 2) . "\n";
        $prompt .= "**Previous Close:** {$stockData['currency']} " . number_format($stockData['previous_close'], 2) . "\n";
        $prompt .= "**Price Change:** " . ($priceChange >= 0 ? '+' : '') . number_format($priceChangePercent, 2) . "%\n";
        $prompt .= "**Volume:** " . number_format($stockData['volume']) . " shares\n";
        $marketType = (strpos((isset($stockData['symbol']) ? $stockData['symbol'] : ''), '.JK') !== false) ? 'IDX (Indonesian)' : 'Global';
        $prompt .= "**Market:** " . $marketType . "\n\n";

        // Technical indicators
        $prompt .= "**Technical Analysis Summary:**\n";
        $prompt .= "• Scalping Score: {$scalpingScore}/10\n";

        if (isset($technicalAnalysis['rsi_7'])) {
            $prompt .= "• RSI (7): {$technicalAnalysis['rsi_7']} - " . ($technicalAnalysis['rsi_7_signal'] ?? 'N/A') . "\n";
        }

        if (isset($technicalAnalysis['ema_9'])) {
            $prompt .= "• EMA (9): " . number_format($technicalAnalysis['ema_9'], 2) . " - " . ($technicalAnalysis['ema_9_signal'] ?? 'N/A') . "\n";
        }

        if (isset($technicalAnalysis['vwap'])) {
            $prompt .= "• VWAP: " . number_format($technicalAnalysis['vwap'], 2) . " - " . ($technicalAnalysis['vwap_signal'] ?? 'N/A') . "\n";
        }

        if (isset($technicalAnalysis['bollinger_bands_scalping'])) {
            $bb = $technicalAnalysis['bollinger_bands_scalping'];
            $prompt .= "• Bollinger Bands: Upper {$bb['upper']}, Lower {$bb['lower']} - " . ($technicalAnalysis['bb_scalping_signal'] ?? 'N/A') . "\n";
        }

        if (isset($technicalAnalysis['stochastic_scalping'])) {
            $stoch = $technicalAnalysis['stochastic_scalping'];
            $prompt .= "• Stochastic: %K {$stoch['%K']}, %D {$stoch['%D']} - " . ($technicalAnalysis['stoch_scalping_signal'] ?? 'N/A') . "\n";
        }

        // Timeframe-aware threshold for HOLD vs BUY format (same as ChatGPTService)
        $buyThreshold = match($timeframe) {
            '1h', '1d' => 4,  // Scalping: score >= 4 for BUY
            '1w' => 3,         // Swing: score >= 3 for BUY
            '1m' => 2,         // Position: score >= 2 for BUY
            default => 4
        };

        // Determine action based on score
        // BUY: score >= threshold
        // HOLD: 1 <= score < threshold
        // SELL: score < 1
        if ($scalpingScore >= $buyThreshold) {
            // BUY format
            $prompt .= "\n**Please provide your BUY analysis with confidence level:**\n";
            $prompt .= "Start with basic info, then provide your BUY recommendation with entry/target prices.\n";
            $prompt .= "Include technical reasoning and risk assessment.\n";
            $prompt .= "Include a confidence level (0-100%) for your analysis.\n";
            $prompt .= "Format is free - provide natural language analysis with specific price levels.\n";
            $prompt .= "End with: Confidence Level: X%\n";
        } elseif ($scalpingScore >= 1) {
            // HOLD format
            $prompt .= "\n**Please provide your HOLD analysis with confidence level:**\n";
            $prompt .= "Start with basic info, then give your reasoning and confidence.\n";
            $prompt .= "Explain why the stock should be held (neutral signals, waiting for better setup).\n";
            $prompt .= "Include a confidence level (0-100%) for your analysis.\n";
            $prompt .= "Format is free - provide natural language analysis.\n";
            $prompt .= "End with: Confidence Level: X%\n";
        } else {
            // SELL format (bearish)
            $prompt .= "\n**Please provide your SELL analysis with confidence level:**\n";
            $prompt .= "Start with basic info, then explain why this is a SELL signal (exit recommendation).\n";
            $prompt .= "Include bearish technical reasoning and risk assessment.\n";
            $prompt .= "This is an exit signal for existing positions, not a short selling recommendation.\n";
            $prompt .= "Include a confidence level (0-100%) for your analysis.\n";
            $prompt .= "Format is free - provide natural language analysis.\n";
            $prompt .= "End with: Confidence Level: X%\n";
        }

        return $prompt;
    }
}
