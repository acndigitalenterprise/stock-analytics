<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\YahooFinanceService;

class MarketController extends Controller
{
    protected $yahooFinanceService;

    public function __construct(YahooFinanceService $yahooFinanceService)
    {
        $this->yahooFinanceService = $yahooFinanceService;
    }

    public function index()
    {
        try {
            // Get market insights data
            $marketInsights = $this->yahooFinanceService->getMarketInsights();
            
            return view('Market.market', compact('marketInsights'));
        } catch (\Exception $e) {
            // Handle any errors
            $marketInsights = [
                'success' => false,
                'error' => 'Market data temporarily unavailable: ' . $e->getMessage()
            ];
            
            return view('Market.market', compact('marketInsights'));
        }
    }

    public function refresh()
    {
        try {
            // Force refresh market data
            $marketInsights = $this->yahooFinanceService->getMarketInsights(true);
            
            if ($marketInsights['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Market data refreshed successfully',
                    'data' => $marketInsights
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $marketInsights['error'] ?? 'Failed to refresh market data'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error refreshing market data: ' . $e->getMessage()
            ]);
        }
    }
}