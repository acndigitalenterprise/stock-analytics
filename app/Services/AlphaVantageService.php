<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Alpha Vantage API Service
 * 
 * Provides access to comprehensive stock data including
 * technical indicators and historical OHLCV data
 */
class AlphaVantageService
{
    private ?string $apiKey;
    private string $baseUrl = 'https://www.alphavantage.co/query';
    
    public function __construct()
    {
        $this->apiKey = config('services.alphavantage.api_key');
    }

    /**
     * Get comprehensive stock data with technical indicators
     */
    public function getComprehensiveStockData(string $symbol): ?array
    {
        if (!$this->apiKey) {
            Log::warning('Alpha Vantage API key not configured');
            return null;
        }

        $cacheKey = "alphavantage_comprehensive_{$symbol}";
        
        // Cache for 5 minutes to avoid API rate limits
        return Cache::remember($cacheKey, 300, function() use ($symbol) {
            try {
                // Get daily adjusted data (last 100 days)
                $dailyData = $this->getDailyAdjusted($symbol);
                if (!$dailyData) return null;

                // Get technical indicators
                $technicalData = $this->getTechnicalIndicators($symbol);

                return [
                    'daily_data' => $dailyData,
                    'technical_indicators' => $technicalData,
                    'fetched_at' => now()->toISOString()
                ];

            } catch (\Exception $e) {
                Log::error('Alpha Vantage comprehensive data error', [
                    'symbol' => $symbol,
                    'error' => $e->getMessage()
                ]);
                return null;
            }
        });
    }

    /**
     * Get daily adjusted stock data
     */
    public function getDailyAdjusted(string $symbol): ?array
    {
        try {
            $response = Http::timeout(15)->get($this->baseUrl, [
                'function' => 'TIME_SERIES_DAILY_ADJUSTED',
                'symbol' => $symbol,
                'outputsize' => 'compact', // Last 100 data points
                'apikey' => $this->apiKey
            ]);

            if (!$response->successful()) {
                Log::error('Alpha Vantage daily data API failed', [
                    'symbol' => $symbol,
                    'status' => $response->status()
                ]);
                return null;
            }

            $data = $response->json();

            // Check for API errors
            if (isset($data['Error Message'])) {
                Log::error('Alpha Vantage API error', [
                    'symbol' => $symbol,
                    'error' => $data['Error Message']
                ]);
                return null;
            }

            if (isset($data['Note'])) {
                Log::warning('Alpha Vantage API rate limit', [
                    'symbol' => $symbol,
                    'note' => $data['Note']
                ]);
                return null;
            }

            if (!isset($data['Time Series (Daily)'])) {
                Log::warning('No daily data found for symbol', ['symbol' => $symbol]);
                return null;
            }

            // Convert to standardized format
            $timeSeries = $data['Time Series (Daily)'];
            $ohlcvData = [];

            foreach ($timeSeries as $date => $dayData) {
                $ohlcvData[] = [
                    'date' => $date,
                    'open' => (float) $dayData['1. open'],
                    'high' => (float) $dayData['2. high'],
                    'low' => (float) $dayData['3. low'],
                    'close' => (float) $dayData['4. close'],
                    'adjusted_close' => (float) $dayData['5. adjusted close'],
                    'volume' => (int) $dayData['6. volume'],
                    'dividend_amount' => (float) $dayData['7. dividend amount'],
                    'split_coefficient' => (float) $dayData['8. split coefficient']
                ];
            }

            // Sort by date (most recent first)
            usort($ohlcvData, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });

            return $ohlcvData;

        } catch (\Exception $e) {
            Log::error('Alpha Vantage daily data exception', [
                'symbol' => $symbol,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get multiple technical indicators
     */
    public function getTechnicalIndicators(string $symbol): array
    {
        $indicators = [
            'rsi' => $this->getRSI($symbol),
            'macd' => $this->getMACD($symbol),
            'bbands' => $this->getBollingerBands($symbol),
            'sma20' => $this->getSMA($symbol, 20),
            'sma50' => $this->getSMA($symbol, 50),
            'ema12' => $this->getEMA($symbol, 12),
            'ema26' => $this->getEMA($symbol, 26),
            'stoch' => $this->getStochastic($symbol),
            'willr' => $this->getWilliamsR($symbol),
            'adx' => $this->getADX($symbol)
        ];

        // Filter out null values
        return array_filter($indicators, function($value) {
            return $value !== null;
        });
    }

    /**
     * Get RSI from Alpha Vantage
     */
    public function getRSI(string $symbol, int $period = 14): ?array
    {
        return $this->getTechnicalIndicator($symbol, 'RSI', [
            'time_period' => $period,
            'series_type' => 'close'
        ]);
    }

    /**
     * Get MACD from Alpha Vantage
     */
    public function getMACD(string $symbol): ?array
    {
        return $this->getTechnicalIndicator($symbol, 'MACD', [
            'series_type' => 'close',
            'fastperiod' => 12,
            'slowperiod' => 26,
            'signalperiod' => 9
        ]);
    }

    /**
     * Get Bollinger Bands from Alpha Vantage
     */
    public function getBollingerBands(string $symbol, int $period = 20): ?array
    {
        return $this->getTechnicalIndicator($symbol, 'BBANDS', [
            'time_period' => $period,
            'series_type' => 'close',
            'nbdevup' => 2,
            'nbdevdn' => 2
        ]);
    }

    /**
     * Get Simple Moving Average
     */
    public function getSMA(string $symbol, int $period): ?array
    {
        return $this->getTechnicalIndicator($symbol, 'SMA', [
            'time_period' => $period,
            'series_type' => 'close'
        ]);
    }

    /**
     * Get Exponential Moving Average
     */
    public function getEMA(string $symbol, int $period): ?array
    {
        return $this->getTechnicalIndicator($symbol, 'EMA', [
            'time_period' => $period,
            'series_type' => 'close'
        ]);
    }

    /**
     * Get Stochastic Oscillator
     */
    public function getStochastic(string $symbol): ?array
    {
        return $this->getTechnicalIndicator($symbol, 'STOCH', [
            'fastkperiod' => 14,
            'slowkperiod' => 3,
            'slowdperiod' => 3,
            'slowkmatype' => 0,
            'slowdmatype' => 0
        ]);
    }

    /**
     * Get Williams %R
     */
    public function getWilliamsR(string $symbol, int $period = 14): ?array
    {
        return $this->getTechnicalIndicator($symbol, 'WILLR', [
            'time_period' => $period
        ]);
    }

    /**
     * Get ADX (Average Directional Index)
     */
    public function getADX(string $symbol, int $period = 14): ?array
    {
        return $this->getTechnicalIndicator($symbol, 'ADX', [
            'time_period' => $period
        ]);
    }

    /**
     * Generic method to get technical indicators
     */
    private function getTechnicalIndicator(string $symbol, string $function, array $params): ?array
    {
        $cacheKey = "alphavantage_{$function}_{$symbol}_" . md5(serialize($params));
        
        return Cache::remember($cacheKey, 300, function() use ($symbol, $function, $params) {
            try {
                $queryParams = array_merge([
                    'function' => $function,
                    'symbol' => $symbol,
                    'interval' => 'daily',
                    'apikey' => $this->apiKey
                ], $params);

                $response = Http::timeout(15)->get($this->baseUrl, $queryParams);

                if (!$response->successful()) {
                    Log::error('Alpha Vantage technical indicator API failed', [
                        'symbol' => $symbol,
                        'function' => $function,
                        'status' => $response->status()
                    ]);
                    return null;
                }

                $data = $response->json();

                // Check for API errors
                if (isset($data['Error Message'])) {
                    Log::error('Alpha Vantage technical indicator error', [
                        'symbol' => $symbol,
                        'function' => $function,
                        'error' => $data['Error Message']
                    ]);
                    return null;
                }

                if (isset($data['Note'])) {
                    Log::warning('Alpha Vantage technical indicator rate limit', [
                        'symbol' => $symbol,
                        'function' => $function
                    ]);
                    return null;
                }

                // Extract technical indicator data
                $technicalKey = "Technical Analysis: {$function}";
                if (!isset($data[$technicalKey])) {
                    Log::warning('No technical indicator data found', [
                        'symbol' => $symbol,
                        'function' => $function
                    ]);
                    return null;
                }

                // Get the latest value(s)
                $technicalData = $data[$technicalKey];
                $latestDate = array_key_first($technicalData);
                $latestValues = $technicalData[$latestDate];

                return [
                    'date' => $latestDate,
                    'values' => $latestValues,
                    'function' => $function
                ];

            } catch (\Exception $e) {
                Log::error('Alpha Vantage technical indicator exception', [
                    'symbol' => $symbol,
                    'function' => $function,
                    'error' => $e->getMessage()
                ]);
                return null;
            }
        });
    }

    /**
     * Parse and format technical indicators for ChatGPT
     */
    public function formatTechnicalIndicators(array $technicalData): array
    {
        $formatted = [];

        foreach ($technicalData as $indicator => $data) {
            if (!$data || !isset($data['values'])) continue;

            switch ($indicator) {
                case 'rsi':
                    $rsi = (float) $data['values']['RSI'];
                    $formatted['rsi'] = [
                        'value' => round($rsi, 2),
                        'signal' => $this->getRSISignal($rsi),
                        'interpretation' => $this->getRSIInterpretation($rsi)
                    ];
                    break;

                case 'macd':
                    $formatted['macd'] = [
                        'macd' => round((float) $data['values']['MACD'], 4),
                        'signal' => round((float) $data['values']['MACD_Signal'], 4),
                        'histogram' => round((float) $data['values']['MACD_Hist'], 4),
                        'trend' => $this->getMACDTrend($data['values'])
                    ];
                    break;

                case 'bbands':
                    $upper = (float) $data['values']['Real Upper Band'];
                    $middle = (float) $data['values']['Real Middle Band'];
                    $lower = (float) $data['values']['Real Lower Band'];
                    
                    $formatted['bollinger_bands'] = [
                        'upper' => round($upper, 2),
                        'middle' => round($middle, 2),
                        'lower' => round($lower, 2),
                        'bandwidth' => $middle != 0 ? round((($upper - $lower) / $middle) * 100, 2) : 0
                    ];
                    break;

                case 'stoch':
                    $formatted['stochastic'] = [
                        'k_percent' => round((float) $data['values']['SlowK'], 2),
                        'd_percent' => round((float) $data['values']['SlowD'], 2),
                        'signal' => $this->getStochasticSignal($data['values'])
                    ];
                    break;

                case 'willr':
                    $willr = (float) $data['values']['WILLR'];
                    $formatted['williams_r'] = [
                        'value' => round($willr, 2),
                        'signal' => $this->getWilliamsRSignal($willr)
                    ];
                    break;

                case 'adx':
                    $formatted['adx'] = [
                        'value' => round((float) $data['values']['ADX'], 2),
                        'strength' => $this->getADXStrength((float) $data['values']['ADX'])
                    ];
                    break;

                default:
                    if (isset($data['values']) && is_array($data['values'])) {
                        $key = array_key_first($data['values']);
                        $formatted[$indicator] = [
                            'value' => round((float) $data['values'][$key], 2)
                        ];
                    }
            }
        }

        return $formatted;
    }

    private function getRSISignal(float $rsi): string
    {
        if ($rsi > 70) return 'Overbought';
        if ($rsi < 30) return 'Oversold';
        if ($rsi > 50) return 'Bullish';
        return 'Bearish';
    }

    private function getRSIInterpretation(float $rsi): string
    {
        if ($rsi > 80) return 'Extremely overbought - strong sell signal';
        if ($rsi > 70) return 'Overbought - consider selling';
        if ($rsi > 60) return 'Bullish momentum';
        if ($rsi > 40) return 'Neutral territory';
        if ($rsi > 30) return 'Bearish momentum';
        if ($rsi > 20) return 'Oversold - consider buying';
        return 'Extremely oversold - strong buy signal';
    }

    private function getMACDTrend(array $values): string
    {
        $macd = (float) $values['MACD'];
        $signal = (float) $values['MACD_Signal'];
        $histogram = (float) $values['MACD_Hist'];

        if ($macd > $signal && $histogram > 0) return 'Strong Bullish';
        if ($macd > $signal) return 'Bullish';
        if ($macd < $signal && $histogram < 0) return 'Strong Bearish';
        return 'Bearish';
    }

    private function getStochasticSignal(array $values): string
    {
        $k = (float) $values['SlowK'];
        $d = (float) $values['SlowD'];

        if ($k > 80 && $d > 80) return 'Overbought';
        if ($k < 20 && $d < 20) return 'Oversold';
        if ($k > $d) return 'Bullish';
        return 'Bearish';
    }

    private function getWilliamsRSignal(float $willr): string
    {
        if ($willr > -20) return 'Overbought';
        if ($willr < -80) return 'Oversold';
        if ($willr > -50) return 'Bullish';
        return 'Bearish';
    }

    private function getADXStrength(float $adx): string
    {
        if ($adx > 50) return 'Very Strong Trend';
        if ($adx > 25) return 'Strong Trend';
        if ($adx > 20) return 'Moderate Trend';
        return 'Weak Trend';
    }
}