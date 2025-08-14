<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YahooFinanceService
{
    public function getStockData(string $stockCode, string $timeframe): ?array
    {
        try {
            $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$stockCode}?interval={$timeframe}&range=1d";
            
            $response = Http::timeout(10)->get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Validate response structure
                if (isset($data['chart']['result'][0])) {
                    return $data;
                }
            }
            
            Log::error('Yahoo Finance API failed', [
                'stock_code' => $stockCode,
                'timeframe' => $timeframe,
                'response' => $response->body()
            ]);
            
            return null;
        } catch (\Exception $e) {
            Log::error('Yahoo Finance API exception', [
                'stock_code' => $stockCode,
                'timeframe' => $timeframe,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
    
    public function extractRelevantData(array $yahooData): array
    {
        $result = $yahooData['chart']['result'][0];
        $meta = $result['meta'];
        $indicators = $result['indicators']['quote'][0];
        
        // Get latest price data
        $latestIndex = count($indicators['close']) - 1;
        
        return [
            'symbol' => $meta['symbol'],
            'currency' => $meta['currency'],
            'exchange' => $meta['exchangeName'],
            'company_name' => $meta['longName'],
            'current_price' => $meta['regularMarketPrice'],
            'previous_close' => $meta['previousClose'] ?? $meta['regularMarketPrice'],
            'day_high' => $meta['regularMarketDayHigh'] ?? $meta['regularMarketPrice'],
            'day_low' => $meta['regularMarketDayLow'] ?? $meta['regularMarketPrice'],
            'volume' => $meta['regularMarketVolume'] ?? 0,
            'fifty_two_week_high' => $meta['fiftyTwoWeekHigh'] ?? $meta['regularMarketPrice'],
            'fifty_two_week_low' => $meta['fiftyTwoWeekLow'] ?? $meta['regularMarketPrice'],
            'latest_close' => $indicators['close'][$latestIndex] ?? $meta['regularMarketPrice'],
            'latest_high' => $indicators['high'][$latestIndex] ?? $meta['regularMarketPrice'],
            'latest_low' => $indicators['low'][$latestIndex] ?? $meta['regularMarketPrice'],
            'latest_open' => $indicators['open'][$latestIndex] ?? $meta['regularMarketPrice'],
            'latest_volume' => $indicators['volume'][$latestIndex] ?? 0,
            'timestamp' => $result['timestamp'][$latestIndex] ?? null,
            'timezone' => $meta['timezone'] ?? 'WIB',
        ];
    }

    /**
     * Get market insights - top active and promising stocks
     */
    public function getMarketInsights(): array
    {
        try {
            // Popular Indonesian stocks for analysis
            $stocks = [
                'BBCA.JK', 'BBRI.JK', 'BMRI.JK', 'TLKM.JK', 'ASII.JK',
                'UNVR.JK', 'ICBP.JK', 'KLBF.JK', 'GOTO.JK', 'AMMN.JK',
                'ACES.JK', 'ANTM.JK', 'BRIS.JK', 'CPIN.JK', 'EMTK.JK',
                'INCO.JK', 'ITMG.JK', 'JPFA.JK', 'MAPI.JK', 'MDKA.JK'
            ];

            $insights = [];
            $failed = 0;

            foreach ($stocks as $stock) {
                $data = $this->getStockData($stock, '1d');
                if ($data) {
                    $extracted = $this->extractRelevantData($data);
                    
                    // Calculate change percentage
                    $changePercent = 0;
                    if ($extracted['previous_close'] > 0) {
                        $changePercent = (($extracted['current_price'] - $extracted['previous_close']) / $extracted['previous_close']) * 100;
                    }
                    
                    $insights[] = [
                        'symbol' => str_replace('.JK', '', $extracted['symbol']),
                        'name' => $this->shortenCompanyName($extracted['company_name'] ?? $stock),
                        'price' => $extracted['current_price'],
                        'change_percent' => $changePercent,
                        'volume' => $extracted['volume'],
                        'is_gaining' => $changePercent > 0,
                        'volume_score' => $this->calculateVolumeScore($extracted),
                        'promising_score' => $this->calculatePromisingScore($extracted, $changePercent)
                    ];
                } else {
                    $failed++;
                    if ($failed > 5) break; // Stop if too many failures
                }
                
                // Small delay to avoid rate limiting
                usleep(100000); // 0.1 second
            }

            // Sort and get top 10
            $topActive = collect($insights)->sortByDesc('volume')->take(10)->values()->all();
            $topPromising = collect($insights)->sortByDesc('promising_score')->take(10)->values()->all();

            return [
                'success' => true,
                'top_active' => $topActive,
                'top_promising' => $topPromising,
                'total_analyzed' => count($insights),
                'last_update' => now()->setTimezone('Asia/Jakarta')->format('d M Y H:i T')
            ];

        } catch (\Exception $e) {
            Log::error('Market insights failed', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'Unable to fetch market data',
                'top_active' => [],
                'top_promising' => []
            ];
        }
    }

    private function shortenCompanyName(?string $name): string
    {
        if (!$name) return 'N/A';
        
        // Common replacements for Indonesian companies
        $replacements = [
            'PT ' => '',
            ' Tbk' => '',
            ' Indonesia' => '',
            'Bank ' => '',
            'Astra ' => '',
            'Telekomunikasi ' => 'Telkom ',
        ];
        
        $shortened = str_replace(array_keys($replacements), array_values($replacements), $name);
        return strlen($shortened) > 25 ? substr($shortened, 0, 22) . '...' : $shortened;
    }

    private function calculateVolumeScore(array $extracted): float
    {
        // Simple volume scoring - higher volume gets higher score
        $volume = $extracted['volume'] ?? 0;
        if ($volume > 100000000) return 10.0; // > 100M
        if ($volume > 50000000) return 8.0;   // > 50M
        if ($volume > 20000000) return 6.0;   // > 20M
        if ($volume > 10000000) return 4.0;   // > 10M
        if ($volume > 5000000) return 2.0;    // > 5M
        return 1.0;
    }

    private function calculatePromisingScore(array $extracted, float $changePercent): float
    {
        $score = 0;
        
        // Price momentum (30% weight)
        if ($changePercent > 5) $score += 3.0;
        elseif ($changePercent > 2) $score += 2.0;
        elseif ($changePercent > 0) $score += 1.0;
        
        // Volume factor (25% weight)
        $volumeScore = $this->calculateVolumeScore($extracted);
        $score += ($volumeScore / 10) * 2.5;
        
        // Price position relative to 52-week range (25% weight)
        $currentPrice = $extracted['current_price'];
        $yearHigh = $extracted['fifty_two_week_high'];
        $yearLow = $extracted['fifty_two_week_low'];
        
        if ($yearHigh > $yearLow) {
            $position = ($currentPrice - $yearLow) / ($yearHigh - $yearLow);
            if ($position > 0.8) $score += 2.5; // Near yearly high
            elseif ($position < 0.3) $score += 1.5; // Near yearly low (potential bounce)
            else $score += 1.0;
        }
        
        // Daily range position (20% weight)
        $dayHigh = $extracted['day_high'];
        $dayLow = $extracted['day_low'];
        
        if ($dayHigh > $dayLow) {
            $dailyPosition = ($currentPrice - $dayLow) / ($dayHigh - $dayLow);
            if ($dailyPosition > 0.7) $score += 2.0; // Strong intraday performance
            elseif ($dailyPosition > 0.5) $score += 1.0;
        }
        
        return min($score, 10.0); // Cap at 10
    }
} 