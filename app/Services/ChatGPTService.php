<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatGPTService
{
    private ?string $apiKey;
    private ?string $organizationId;
    private string $baseUrl = 'https://api.openai.com/v1/chat/completions';
    
    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->organizationId = config('services.openai.organization_id');
    }
    
    public function generateStockAdvice(array $stockData, array $technicalAnalysis, string $timeframe): array
    {
        // Generate both Claude (deterministic) and ChatGPT advice
        $claudeAdvice = $this->buildDeterministicAdvice($stockData, $technicalAnalysis, $timeframe);
        $chatgptAdvice = $this->generateChatGPTAdvice($stockData, $technicalAnalysis, $timeframe);
        
        return [
            'claude' => $claudeAdvice,
            'chatgpt' => $chatgptAdvice
        ];
        
        /* Commented out AI integration - can be enabled later with proper format parsing
        if (!$this->apiKey) {
            Log::warning('OpenAI API key not configured, using fallback deterministic analysis');
            return $this->buildDeterministicAdvice($stockData, $technicalAnalysis, $timeframe);
        }

        // Build context prompt for AI
        $prompt = $this->buildAIPrompt($stockData, $technicalAnalysis, $timeframe);
        
        try {
            $response = Http::timeout(config('services.openai.timeout'))
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl, [
                    'model' => config('services.openai.model'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an expert stock analyst specializing in technical analysis and scalping strategies for Indonesian (IDX) and global stocks. Provide concise, actionable trading advice based on real-time data and technical indicators.'
                        ],
                        [
                            'role' => 'user', 
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => config('services.openai.max_tokens'),
                    'temperature' => 0.3, // Lower temperature for more consistent analysis
                ]);

            if ($response->successful()) {
                $result = $response->json();
                $advice = $result['choices'][0]['message']['content'] ?? null;
                
                if ($advice) {
                    Log::info('Successfully generated AI stock advice', [
                        'model' => config('services.openai.model'),
                        'tokens_used' => $result['usage']['total_tokens'] ?? 'unknown'
                    ]);
                    
                    // Parse AI response and format it consistently
                    return $this->formatAIResponse($advice, $stockData, $technicalAnalysis);
                }
            }

            Log::error('OpenAI API failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error calling OpenAI API', [
                'error' => $e->getMessage()
            ]);
        }

        // Fallback to deterministic analysis if API fails
        Log::info('Using fallback deterministic analysis due to API failure');
        return $this->buildDeterministicAdvice($stockData, $technicalAnalysis, $timeframe);
        */
    }
    
    /**
     * Build AI prompt with comprehensive technical analysis context
     */
    private function buildAIPrompt(array $stockData, array $technicalAnalysis, string $timeframe): string
    {
        $timeframeText = $timeframe === '1h' ? '1 hour' : '1 day';
        $scalpingScore = $technicalAnalysis['scalping_score'] ?? 0;
        
        // Calculate price changes
        $priceChange = $stockData['current_price'] - $stockData['previous_close'];
        $priceChangePercent = $stockData['previous_close'] > 0 ? (($priceChange / $stockData['previous_close']) * 100) : 0;
        
        $prompt = "Analyze this stock for {$timeframeText} scalping/trading:\n\n";
        
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
        
        // Calculate one hour ago price for context
        $oneHourAgoPrice = $stockData['previous_close']; // simplified
        $priceChangeFromOneHour = $stockData['current_price'] - $oneHourAgoPrice;
        $priceChangePercentFromOneHour = $oneHourAgoPrice > 0 ? (($priceChangeFromOneHour / $oneHourAgoPrice) * 100) : 0;
        $changeSignOneHour = $priceChangeFromOneHour >= 0 ? '+' : '';
        
        $useHoldFormat = $scalpingScore < 4;
        
        if ($useHoldFormat) {
            $prompt .= "\n**Please provide your analysis with confidence level:**\n";
            $prompt .= "Start with basic info, then give your reasoning and confidence.\n";
            $prompt .= "Include a confidence level (0-100%) for your analysis.\n";
            $prompt .= "Format is free - provide natural language analysis.\n";
            $prompt .= "End with: Confidence Level: X%\n";
        } else {
            $prompt .= "\n**Please provide your analysis with confidence level:**\n";
            $prompt .= "Start with basic info, then provide your BUY recommendation with entry/target prices.\n";
            $prompt .= "Include technical reasoning and risk assessment.\n";
            $prompt .= "Include a confidence level (0-100%) for your analysis.\n";
            $prompt .= "Format is free - provide natural language analysis with specific price levels.\n";
            $prompt .= "End with: Confidence Level: X%\n";
        }
        
        return $prompt;
    }

    /**
     * Generate ChatGPT advice with confidence level
     */
    private function generateChatGPTAdvice(array $stockData, array $technicalAnalysis, string $timeframe): ?string
    {
        if (!$this->apiKey) {
            Log::warning('OpenAI API key not configured');
            return "ChatGPT analysis unavailable - API key not configured.";
        }

        // Build context prompt for AI
        $prompt = $this->buildAIPrompt($stockData, $technicalAnalysis, $timeframe);
        
        try {
            $response = Http::timeout(config('services.openai.timeout', 30))
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl, [
                    'model' => config('services.openai.model', 'gpt-4'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an expert stock analyst specializing in technical analysis and scalping strategies for Indonesian (IDX) and global stocks. Provide concise, actionable trading advice based on real-time data and technical indicators. Always include a confidence level for your analysis.'
                        ],
                        [
                            'role' => 'user', 
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => config('services.openai.max_tokens', 1000),
                    'temperature' => 0.3, // Lower temperature for more consistent analysis
                ]);

            if ($response->successful()) {
                $result = $response->json();
                $advice = $result['choices'][0]['message']['content'] ?? null;
                
                if ($advice) {
                    Log::info('Successfully generated ChatGPT stock advice', [
                        'model' => config('services.openai.model'),
                        'tokens_used' => $result['usage']['total_tokens'] ?? 'unknown'
                    ]);
                    
                    return $advice;
                }
            }

            Log::error('OpenAI API failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            
            return "ChatGPT analysis failed - API error. Status: " . $response->status();
            
        } catch (\Exception $e) {
            Log::error('Error calling OpenAI API', [
                'error' => $e->getMessage()
            ]);
            
            return "ChatGPT analysis failed - " . $e->getMessage();
        }
    }

    /**
     * Build deterministic advice (fallback when AI is not available)
     */
    private function buildDeterministicAdvice(array $stockData, array $technicalAnalysis, string $timeframe): string
    {
        // Calculate price changes
        $oneHourAgoPrice = $this->calculateOneHourAgoPrice($stockData);
        $thirtyMinAgoPrice = $this->calculateThirtyMinAgoPrice($stockData);
        
        $priceChangeFromOneHour = $stockData['current_price'] - $oneHourAgoPrice;
        $priceChangePercentFromOneHour = $oneHourAgoPrice > 0 ? (($priceChangeFromOneHour / $oneHourAgoPrice) * 100) : 0;
        $changeSignOneHour = $priceChangeFromOneHour >= 0 ? '+' : '';
        
        $priceChangeFromThirtyMin = $stockData['current_price'] - $thirtyMinAgoPrice;
        $priceChangePercentFromThirtyMin = $thirtyMinAgoPrice > 0 ? (($priceChangeFromThirtyMin / $thirtyMinAgoPrice) * 100) : 0;
        $changeSignThirtyMin = $priceChangeFromThirtyMin >= 0 ? '+' : '';

        // Determine action based on score
        $isHold = $technicalAnalysis['scalping_score'] < 4;
        $action = $isHold ? 'Hold' : 'Buy';
        
        // Build advice string
        $advice = "Current Price: {$stockData['currency']} " . number_format($stockData['current_price'], 2) . "\n";
        $advice .= "Price 1 hour ago: {$stockData['currency']} " . number_format($oneHourAgoPrice, 2) . " ({$changeSignOneHour}" . number_format($priceChangePercentFromOneHour, 2) . "%)\n";
        $advice .= "Price 30 minutes: {$stockData['currency']} " . number_format($thirtyMinAgoPrice, 2) . " ({$changeSignThirtyMin}" . number_format($priceChangePercentFromThirtyMin, 2) . "%)\n";
        $advice .= "Traded Volume: " . number_format($stockData['volume']) . " shares\n";
        if (!$isHold) {
            // BUY format with rounded numbers
            $advice .= "Action: Buy at {$stockData['currency']} " . number_format($technicalAnalysis['entry_price'], 0) . "\n";
            
            $entryPrice = $technicalAnalysis['entry_price'];
            $target1Profit = ($entryPrice > 0) ? ((($technicalAnalysis['target_1'] - $entryPrice) / $entryPrice) * 100) : 0;
            $target2Profit = ($entryPrice > 0) ? ((($technicalAnalysis['target_2'] - $entryPrice) / $entryPrice) * 100) : 0;
            $stopLossPercent = ($entryPrice > 0) ? ((($entryPrice - $technicalAnalysis['stop_loss']) / $entryPrice) * 100) : 0;
            
            $advice .= "Target 1: {$stockData['currency']} " . number_format($technicalAnalysis['target_1'], 0) . " (~" . number_format($target1Profit, 1) . "%)\n";
            $advice .= "Target 2: {$stockData['currency']} " . number_format($technicalAnalysis['target_2'], 0) . " (~" . number_format($target2Profit, 1) . "%)\n";
            $advice .= "Stop Loss: {$stockData['currency']} " . number_format($technicalAnalysis['stop_loss'], 0) . " (~" . number_format($stopLossPercent, 1) . "%)\n";
            
            // Calculate confidence level based on technical indicators
            $confidenceLevel = $this->calculateConfidenceLevel($technicalAnalysis);
            $advice .= "Confidence Level: {$confidenceLevel}%\n";
            
            // Build technical reason without overconfident language
            $advice .= "Reason:\n";
            $advice .= $this->buildTechnicalReason($stockData, $technicalAnalysis, $timeframe);
        } else {
            // HOLD format - no entry/targets/reason needed, just Action: Hold
            // User specification: HOLD should only show price info and Action: Hold
            $advice .= "Action: Hold\n";
        }
        
        return $advice;
    }
    
    /**
     * Calculate confidence level based on technical indicators
     */
    private function calculateConfidenceLevel(array $technicalAnalysis): int
    {
        $confidence = 0;
        $maxConfidence = 100;
        
        // Base confidence from scalping score (0-50%)
        $scalpingScore = $technicalAnalysis['scalping_score'] ?? 0;
        $confidence += ($scalpingScore / 10) * 50;
        
        // RSI contribution (0-15%)
        if (isset($technicalAnalysis['rsi_7'])) {
            $rsi = $technicalAnalysis['rsi_7'];
            if ($rsi >= 30 && $rsi <= 70) {
                $confidence += 15; // Neutral zone is good
            } elseif ($rsi < 30 || $rsi > 70) {
                $confidence += 10; // Extreme zones have some merit
            }
        }
        
        // Stochastic contribution (0-15%)
        if (isset($technicalAnalysis['stochastic_scalping']['%K'])) {
            $stochK = $technicalAnalysis['stochastic_scalping']['%K'];
            if ($stochK >= 20 && $stochK <= 80) {
                $confidence += 15; // Balanced momentum
            } elseif ($stochK < 20 || $stochK > 80) {
                $confidence += 10; // Extreme momentum
            }
        }
        
        // Volume contribution (0-10%)
        if (isset($technicalAnalysis['volume']) && $technicalAnalysis['volume'] > 1000000) {
            $confidence += 10; // High volume adds confidence
        } elseif (isset($technicalAnalysis['volume']) && $technicalAnalysis['volume'] > 500000) {
            $confidence += 5; // Medium volume
        }
        
        // VWAP/EMA alignment (0-10%)
        if (isset($technicalAnalysis['vwap']) && isset($technicalAnalysis['ema_9'])) {
            $confidence += 10; // Multiple indicators available
        } elseif (isset($technicalAnalysis['vwap']) || isset($technicalAnalysis['ema_9'])) {
            $confidence += 5; // Single trend indicator
        }
        
        // Cap at maximum and ensure minimum
        $confidence = min($maxConfidence, max(20, round($confidence)));
        
        return $confidence;
    }
    
    /**
     * Build detailed technical reason for BUY recommendations
     */
    private function buildTechnicalReason(array $stockData, array $technicalAnalysis, string $timeframe): string
    {
        $reason = "The stock has a scalping score of {$technicalAnalysis['scalping_score']}/10. ";
        
        // VWAP analysis
        if (isset($technicalAnalysis['vwap'])) {
            $vwapPosition = $stockData['current_price'] > $technicalAnalysis['vwap'] ? 'above' : 'below';
            $vwapSignal = $stockData['current_price'] > $technicalAnalysis['vwap'] ? 'bullish' : 'bearish';
            $reason .= "The price is currently {$vwapPosition} the VWAP";
        }
        
        // EMA analysis
        if (isset($technicalAnalysis['ema_9'])) {
            $emaPosition = $stockData['current_price'] > $technicalAnalysis['ema_9'] ? 'above' : 'below';
            $reason .= " and the EMA (9). ";
        } else {
            $reason .= ". ";
        }
        
        // RSI analysis
        if (isset($technicalAnalysis['rsi_7'])) {
            $rsi = $technicalAnalysis['rsi_7'];
            if ($rsi < 30) {
                $reason .= "The RSI is in the oversold zone at {$rsi}. ";
            } elseif ($rsi > 70) {
                $reason .= "The RSI is in the overbought zone at {$rsi}. ";
            } else {
                $reason .= "The RSI is in the neutral zone at {$rsi}. ";
            }
        }
        
        // Stochastic analysis
        if (isset($technicalAnalysis['stochastic_scalping'])) {
            $stoch = $technicalAnalysis['stochastic_scalping'];
            if ($stoch['%K'] > 80) {
                $reason .= "The Stochastic indicators are in the overbought zone. ";
            } elseif ($stoch['%K'] < 20) {
                $reason .= "The Stochastic indicators are in the oversold zone. ";
            } else {
                $reason .= "The Stochastic indicators are in the neutral zone. ";
            }
        }
        
        // Bollinger Bands analysis
        if (isset($technicalAnalysis['bollinger_bands_scalping'])) {
            $bb = $technicalAnalysis['bollinger_bands_scalping'];
            $bbPosition = $stockData['current_price'] > $bb['middle'] ? 'above' : 'below';
            $reason .= "The Bollinger Bands show the price is {$bbPosition} the middle band. ";
        }
        
        // Risk management note
        $reason .= "However, the stop loss is set to manage risk and protect against potential market reversals.";
        
        return $reason;
    }
    
    /**
     * Build basic technical reason for HOLD recommendations
     */
    private function buildBasicTechnicalReason(array $stockData, array $technicalAnalysis): string
    {
        $reason = "Technical indicators suggest mixed signals with limited scalping opportunities. ";
        
        if (isset($technicalAnalysis['rsi_7'])) {
            $rsi = $technicalAnalysis['rsi_7'];
            if ($rsi < 30) {
                $reason .= "RSI shows oversold conditions, but momentum is not strong enough for immediate entry. ";
            } elseif ($rsi > 70) {
                $reason .= "RSI indicates overbought conditions, suggesting caution. ";
            }
        }
        
        $reason .= "Consider waiting for clearer technical signals before entering a position.";
        
        return $reason;
    }

    /**
     * Build comprehensive technical indicators summary
     */
    private function buildTechnicalSummary(array $technicalAnalysis): string
    {
        $summary = [];

        // RSI Analysis
        if (isset($technicalAnalysis['rsi'])) {
            $summary[] = "• **RSI (14)**: " . $technicalAnalysis['rsi'] . " - " . $technicalAnalysis['rsi_signal'];
        }

        // MACD Analysis
        if (isset($technicalAnalysis['macd'])) {
            $macd = $technicalAnalysis['macd'];
            $summary[] = "• **MACD**: " . $macd['macd'] . " (Signal: " . $macd['signal'] . ", Histogram: " . $macd['histogram'] . ")";
        }

        // Bollinger Bands
        if (isset($technicalAnalysis['bollinger_bands'])) {
            $bb = $technicalAnalysis['bollinger_bands'];
            $summary[] = "• **Bollinger Bands**: Upper " . $bb['upper'] . ", Middle " . $bb['middle'] . ", Lower " . $bb['lower'] . " (Bandwidth: " . $bb['bandwidth'] . "%)";
        }

        // Moving Averages
        if (isset($technicalAnalysis['sma_20'])) {
            $summary[] = "• **SMA 20**: " . number_format($technicalAnalysis['sma_20'], 2);
        }
        if (isset($technicalAnalysis['sma_50'])) {
            $summary[] = "• **SMA 50**: " . number_format($technicalAnalysis['sma_50'], 2);
        }

        // EMAs
        if (isset($technicalAnalysis['ema_12'])) {
            $summary[] = "• **EMA 12**: " . number_format($technicalAnalysis['ema_12'], 2);
        }
        if (isset($technicalAnalysis['ema_26'])) {
            $summary[] = "• **EMA 26**: " . number_format($technicalAnalysis['ema_26'], 2);
        }

        // Stochastic
        if (isset($technicalAnalysis['stochastic'])) {
            $stoch = $technicalAnalysis['stochastic'];
            $summary[] = "• **Stochastic**: %K " . $stoch['%K'] . ", %D " . $stoch['%D'];
        }

        // Williams %R
        if (isset($technicalAnalysis['williams_r'])) {
            $summary[] = "• **Williams %R**: " . $technicalAnalysis['williams_r'];
        }

        // ADX
        if (isset($technicalAnalysis['adx'])) {
            $summary[] = "• **ADX**: " . $technicalAnalysis['adx'] . " (Trend Strength)";
        }

        // Support & Resistance
        if (isset($technicalAnalysis['support_resistance'])) {
            $sr = $technicalAnalysis['support_resistance'];
            $summary[] = "• **Support**: " . $sr['support'] . " (" . $sr['support_strength'] . " strength)";
            $summary[] = "• **Resistance**: " . $sr['resistance'] . " (" . $sr['resistance_strength'] . " strength)";
        }

        // Volume Analysis
        if (isset($technicalAnalysis['volume_sma'])) {
            $summary[] = "• **Volume SMA**: " . number_format($technicalAnalysis['volume_sma']);
        }

        // Price vs Moving Averages
        if (isset($technicalAnalysis['price_vs_sma20'])) {
            $vs20 = $technicalAnalysis['price_vs_sma20'];
            $summary[] = "• **Price vs SMA20**: " . $vs20['position'] . " by " . abs($vs20['percentage']) . "%";
        }

        if (isset($technicalAnalysis['price_vs_sma50'])) {
            $vs50 = $technicalAnalysis['price_vs_sma50'];
            $summary[] = "• **Price vs SMA50**: " . $vs50['position'] . " by " . abs($vs50['percentage']) . "%";
        }

        // Overall Trend
        if (isset($technicalAnalysis['trend_direction'])) {
            $summary[] = "• **Overall Trend**: " . $technicalAnalysis['trend_direction'];
        }

        // Volatility
        if (isset($technicalAnalysis['volatility'])) {
            $summary[] = "• **Volatility (Annualized)**: " . $technicalAnalysis['volatility'] . "%";
        }

        return implode("\n", $summary);
    }

    /**
     * Build scalping-specific indicators summary
     */
    private function buildScalpingSummary(array $technicalAnalysis): string
    {
        $summary = [];

        // EMA 9
        if (isset($technicalAnalysis['ema_9'])) {
            $summary[] = "• **EMA 9**: " . number_format($technicalAnalysis['ema_9'], 2) . " - " . ($technicalAnalysis['ema_9_signal'] ?? 'N/A');
        }

        // VWAP
        if (isset($technicalAnalysis['vwap'])) {
            $summary[] = "• **VWAP**: " . number_format($technicalAnalysis['vwap'], 2) . " - " . ($technicalAnalysis['vwap_signal'] ?? 'N/A');
        }

        // RSI 7
        if (isset($technicalAnalysis['rsi_7'])) {
            $summary[] = "• **RSI (7)**: " . $technicalAnalysis['rsi_7'] . " - " . ($technicalAnalysis['rsi_7_signal'] ?? 'N/A');
        }

        // Bollinger Bands Scalping
        if (isset($technicalAnalysis['bollinger_bands_scalping'])) {
            $bb = $technicalAnalysis['bollinger_bands_scalping'];
            $summary[] = "• **Bollinger Bands (7,1.5)**: Upper " . $bb['upper'] . ", Middle " . $bb['middle'] . ", Lower " . $bb['lower'] . " - " . ($technicalAnalysis['bb_scalping_signal'] ?? 'N/A');
        }

        // Stochastic Scalping
        if (isset($technicalAnalysis['stochastic_scalping'])) {
            $stoch = $technicalAnalysis['stochastic_scalping'];
            $summary[] = "• **Stochastic (5,3,3)**: %K " . $stoch['%K'] . ", %D " . $stoch['%D'] . " - " . ($technicalAnalysis['stoch_scalping_signal'] ?? 'N/A');
        }

        // MACD Scalping
        if (isset($technicalAnalysis['macd_scalping'])) {
            $macd = $technicalAnalysis['macd_scalping'];
            $summary[] = "• **MACD (5,13,1)**: Line " . $macd['macd'] . ", Signal " . $macd['signal'] . " - " . ($technicalAnalysis['macd_scalping_signal'] ?? 'N/A');
        }

        // Parabolic SAR
        if (isset($technicalAnalysis['parabolic_sar'])) {
            $summary[] = "• **Parabolic SAR**: " . $technicalAnalysis['parabolic_sar'] . " - " . ($technicalAnalysis['sar_signal'] ?? 'N/A');
        }

        // CPR
        if (isset($technicalAnalysis['cpr'])) {
            $cpr = $technicalAnalysis['cpr'];
            $summary[] = "• **CPR**: Pivot " . $cpr['pivot'] . ", BC " . $cpr['bc'] . ", TC " . $cpr['tc'] . " - " . ($technicalAnalysis['cpr_signal'] ?? 'N/A');
        }

        return implode("\n", $summary);
    }

    /**
     * Calculate 1-hour ago price (simplified estimation)
     */
    private function calculateOneHourAgoPrice(array $stockData): float
    {
        // For demonstration, use previous close as approximation
        // In production, you'd fetch actual 1-hour ago data
        return $stockData['previous_close'];
    }
    
    /**
     * Calculate 30-minutes ago price (simplified estimation)
     */
    private function calculateThirtyMinAgoPrice(array $stockData): float
    {
        // Estimate 30-min ago price as midpoint between current and 1-hour ago
        $oneHourAgo = $this->calculateOneHourAgoPrice($stockData);
        return ($stockData['current_price'] + $oneHourAgo) / 2;
    }
} 