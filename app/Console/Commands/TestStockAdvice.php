<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\YahooFinanceService;
use App\Services\ChatGPTService;
use App\Services\TechnicalAnalysisService;

class TestStockAdvice extends Command
{
    protected $signature = 'test:stock-advice {stock_code} {timeframe=1d}';
    protected $description = 'Test the stock advice generation system';

    public function handle(YahooFinanceService $yahooService, ChatGPTService $chatgptService, TechnicalAnalysisService $technicalService)
    {
        $stockCode = $this->argument('stock_code');
        $timeframe = $this->argument('timeframe');

        $this->info("Testing stock advice generation for {$stockCode} ({$timeframe})...");

        // Test Yahoo Finance API
        $this->info("1. Fetching data from Yahoo Finance...");
        $yahooData = $yahooService->getStockData($stockCode, $timeframe);

        if (!$yahooData) {
            $this->error("Failed to fetch data from Yahoo Finance");
            return 1;
        }

        $this->info("✓ Yahoo Finance data fetched successfully");

        // Extract relevant data
        $this->info("2. Extracting relevant data...");
        $stockData = $yahooService->extractRelevantData($yahooData);
        
        $this->info("✓ Data extracted:");
        $this->table(['Field', 'Value'], [
            ['Symbol', $stockData['symbol']],
            ['Company', $stockData['company_name']],
            ['Current Price', $stockData['currency'] . ' ' . number_format($stockData['current_price'], 2)],
            ['Previous Close', $stockData['currency'] . ' ' . number_format($stockData['previous_close'], 2)],
            ['Volume', number_format($stockData['volume'])],
        ]);

        // Generate technical analysis
        $this->info("3. Performing technical analysis...");
        $ohlcvData = $this->convertYahooToOHLCV($yahooData);
        $technicalAnalysis = $technicalService->getScalpingAnalysis($ohlcvData, $stockCode);
        
        $this->info("✓ Technical analysis completed");
        
        // Test ChatGPT API
        $this->info("4. Generating AI advice...");
        $advice = $chatgptService->generateStockAdvice($stockData, $technicalAnalysis, $timeframe);

        if (!$advice) {
            $this->error("Failed to generate AI advice");
            return 1;
        }

        $this->info("✓ AI advice generated successfully");
        $this->newLine();
        $this->info("AI ADVICE:");
        $this->line($advice);

        $this->info("✓ All tests passed!");
        return 0;
    }

    /**
     * Convert Yahoo Finance data format to OHLCV array for technical analysis
     */
    private function convertYahooToOHLCV(array $yahooData): array
    {
        $result = $yahooData['chart']['result'][0];
        $timestamps = $result['timestamp'];
        $indicators = $result['indicators']['quote'][0];
        $meta = $result['meta'];
        
        $ohlcvData = [];
        
        for ($i = 0; $i < count($timestamps); $i++) {
            $ohlcvData[] = [
                'date' => date('Y-m-d', $timestamps[$i]),
                'open' => $indicators['open'][$i] ?? 0,
                'high' => $indicators['high'][$i] ?? 0,
                'low' => $indicators['low'][$i] ?? 0,
                'close' => $indicators['close'][$i] ?? 0,
                'volume' => $indicators['volume'][$i] ?? 0,
            ];
        }
        
        // Add real-time current data as most recent entry
        $currentData = [
            'date' => date('Y-m-d'),
            'open' => $meta['regularMarketPrice'],
            'high' => $meta['regularMarketDayHigh'] ?? $meta['regularMarketPrice'],
            'low' => $meta['regularMarketDayLow'] ?? $meta['regularMarketPrice'], 
            'close' => $meta['regularMarketPrice'],
            'volume' => $meta['regularMarketVolume'] ?? 0,
        ];
        
        // Add previous close data
        $prevClose = $meta['previousClose'] ?? $meta['regularMarketPrice'] ?? 0;
        $previousData = [
            'date' => date('Y-m-d', strtotime('-1 day')),
            'open' => $prevClose,
            'high' => $prevClose,
            'low' => $prevClose, 
            'close' => $prevClose,
            'volume' => 0,
        ];
        
        array_unshift($ohlcvData, $previousData);
        array_unshift($ohlcvData, $currentData);
        
        // Sort by date (most recent first)
        usort($ohlcvData, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return $ohlcvData;
    }
} 