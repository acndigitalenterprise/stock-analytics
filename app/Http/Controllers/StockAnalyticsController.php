<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StockAnalyticsRequest;
use App\Models\User;
use App\Models\Request as StockRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\StockService;
use App\Services\CacheService;
use App\Jobs\GenerateStockAdvice;

class StockAnalyticsController extends Controller
{
    /**
     * Cache service instance
     */
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    public function index()
    {
        // Check if user is already logged in
        $user = session('user');
        if ($user) {
            // Redirect to admin page if user is already logged in
            return redirect()->route('stock-analytics.admin');
        }
        
        return view('stock-analytics');
    }

    /**
     * Search for stocks with caching support
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchStocks(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json([]);
        }

        // Validate query length
        if (strlen($query) > 50) {
            return response()->json(['error' => 'Query too long'], 400);
        }

        try {
            // Check cache first
            $cachedResults = $this->cacheService->getStockSearchResults($query);
            if ($cachedResults !== null) {
                Log::info('Returning cached stock search results', [
                    'query' => $query,
                    'results_count' => count($cachedResults)
                ]);
                return response()->json($cachedResults);
            }

            // Try multiple API sources for better coverage
            $results = [];
            $source = 'local'; // Default fallback
            
            if (config('services.yahoo_finance.enabled', true)) {
                $results = $this->searchFromYahooFinance($query);
                $source = 'yahoo';
            }
            
            // If Yahoo Finance doesn't return results, try Alpha Vantage
            if (empty($results) && config('services.alphavantage.enabled', true)) {
                $results = $this->searchFromAlphaVantage($query);
                $source = 'alphavantage';
            }
            
            // Fallback to local data if API fails
            if (empty($results)) {
                $results = $this->searchFromLocalData($query);
                $source = 'local';
            }

            // Cache the results if we got any
            if (!empty($results)) {
                $this->cacheService->cacheStockSearchResults($query, $results, $source);
            }
            
            Log::info('Stock search completed', [
                'query' => $query,
                'source' => $source,
                'results_count' => count($results)
            ]);
            
            return response()->json(array_values($results));
        } catch (\Exception $e) {
            Log::error('Stock search failed', [
                'query' => $query,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback to local data on error
            $results = $this->searchFromLocalData($query);
            
            // Cache fallback results with shorter TTL
            if (!empty($results)) {
                $this->cacheService->cacheStockSearchResults($query, $results, 'local', 60); // 1 minute cache
            }
            
            return response()->json($results);
        }
    }

    /**
     * Search stocks from Yahoo Finance API
     * 
     * @param string $query Search query
     * @return array Search results
     */
    private function searchFromYahooFinance($query)
    {
        try {
            $timeout = config('services.yahoo_finance.timeout', 5);
            $baseUrl = config('services.yahoo_finance.base_url', 'https://query1.finance.yahoo.com');
            $maxResults = config('services.stock_search.max_results', 20);
            
            $response = Http::timeout($timeout)->get($baseUrl . '/v1/finance/search', [
                'q' => $query,
                'quotesCount' => $maxResults,
                'newsCount' => 0,
                'enableFuzzyQuery' => false,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $results = [];

                if (isset($data['quotes'])) {
                    foreach ($data['quotes'] as $quote) {
                        if (isset($quote['symbol']) && isset($quote['shortname'])) {
                            // Only include Indonesian stocks (IDX exchange or .JK suffix)
                            $symbol = $quote['symbol'];
                            $exchange = $quote['exchange'] ?? '';
                            
                            if ($exchange === 'IDX' || str_ends_with($symbol, '.JK')) {
                                $results[] = [
                                    'code' => $symbol,
                                    'name' => $quote['shortname'],
                                    'exchange' => $exchange,
                                    'type' => $quote['quoteType'] ?? 'EQUITY'
                                ];
                            }
                            // Log results for debugging
                            Log::debug("Yahoo Finance result", [
                                'symbol' => $symbol, 
                                'exchange' => $exchange,
                                'query' => $query
                            ]);
                        }
                    }
                }

                return $results;
            }
        } catch (\Exception $e) {
            Log::error('Yahoo Finance API error', [
                'query' => $query,
                'error' => $e->getMessage()
            ]);
        }

        return [];
    }

    /**
     * Search stocks from Alpha Vantage API
     * 
     * @param string $query Search query
     * @return array Search results
     */
    private function searchFromAlphaVantage($query)
    {
        try {
            $apiKey = config('services.alphavantage.key');
            $timeout = config('services.alphavantage.timeout', 10);
            
            if (empty($apiKey)) {
                Log::warning('Alpha Vantage API key not configured');
                return [];
            }
            
            // Using Alpha Vantage Search API (free tier)
            $response = Http::timeout($timeout)->get('https://www.alphavantage.co/query', [
                'function' => 'SYMBOL_SEARCH',
                'keywords' => $query,
                'apikey' => $apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $results = [];

                if (isset($data['bestMatches'])) {
                    foreach ($data['bestMatches'] as $match) {
                        $region = $match['4. region'] ?? '';
                        $symbol = $match['1. symbol'] ?? '';
                        
                        // Only include Indonesian stocks
                        if ($region === 'Indonesia' || str_ends_with($symbol, '.JK')) {
                            $results[] = [
                                'code' => $symbol,
                                'name' => $match['2. name'],
                                'type' => $match['3. type'],
                                'region' => $region,
                                'currency' => $match['8. currency']
                            ];
                        }
                    }
                }

                return $results;
            }
        } catch (\Exception $e) {
            Log::error('Alpha Vantage API error', [
                'query' => $query,
                'error' => $e->getMessage()
            ]);
        }

        return [];
    }

    /**
     * Search stocks from local data fallback
     * 
     * @param string $query Search query
     * @return array Search results
     */
    private function searchFromLocalData($query)
    {
        $companies = config('companies', []);
        $results = [];
        $query = strtoupper($query);
        $maxResults = min(config('services.stock_search.max_results', 20), 10); // Limit local results
        
        foreach ($companies as $code => $name) {
            if (strpos($code, $query) === 0 || strpos(strtoupper($name), $query) !== false) {
                $results[] = [
                    'code' => $code,
                    'name' => $name,
                    'type' => 'EQUITY',
                    'region' => 'ID'
                ];
                
                // Break early if we have enough results
                if (count($results) >= $maxResults) {
                    break;
                }
            }
        }
        
        return $results;
    }

    /**
     * Submit stock analytics request with enhanced validation
     * 
     * @param StockAnalyticsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submit(StockAnalyticsRequest $request)
    {
        // Check trading hours (09:00-16:00 WIB)
        $currentHour = now()->setTimezone('Asia/Jakarta')->format('H');
        $isTradingHours = $currentHour >= 9 && $currentHour < 16;
        
        if (!$isTradingHours) {
            $currentTime = now()->setTimezone('Asia/Jakarta')->format('H:i');
            return redirect()->back()
                ->withInput()
                ->withErrors(['trading_hours' => "Market is closed. Trading hours: 09:00-16:00 WIB (Current: {$currentTime} WIB)"]);
        }

        // Get validated data from form request
        $validated = $request->validated();

        try {
            // Check if user exists by email
            $user = User::where('email', $validated['email'])->first();
            $isNewUser = false;
            $password = null;

            // If user doesn't exist, check if mobile number is already taken
            if (!$user) {
                // Check if mobile number is already registered
                $existingUserWithMobile = User::where('mobile_number', $validated['mobile_number'])->first();
                if ($existingUserWithMobile) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['mobile_number' => 'This mobile number is already registered. Please use a different mobile number or sign in with your existing account.']);
                }

                $password = Str::random(10); // Generate random password
                $user = User::create([
                    'name' => $validated['full_name'],
                    'mobile_number' => $validated['mobile_number'],
                    'email' => $validated['email'],
                    'password' => Hash::make($password),
                    'role' => 'user',
                ]);
                $isNewUser = true;
            }

            // Create stock request
            $stockRequest = StockRequest::create([
                'full_name' => $validated['full_name'],
                'mobile_number' => $validated['mobile_number'],
                'email' => $validated['email'],
                'stock_code' => StockService::ensureJKFormat($validated['stock_code']),
                'company_name' => StockService::getCompanyName($validated['stock_code']),
                'timeframe' => $validated['timeframe'],
                'user_id' => $user->id,
            ]);

            // Send email
            $this->sendEmail($user, $stockRequest, $isNewUser, $password);

            // Dispatch job to generate AI advice
            try {
                Log::info('Dispatching GenerateStockAdvice job', [
                    'request_id' => $stockRequest->id,
                    'stock_code' => $stockRequest->stock_code,
                    'timeframe' => $stockRequest->timeframe
                ]);
                
                GenerateStockAdvice::dispatch($stockRequest);
                
                Log::info('GenerateStockAdvice job dispatched successfully', [
                    'request_id' => $stockRequest->id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to dispatch GenerateStockAdvice job', [
                    'request_id' => $stockRequest->id,
                    'error' => $e->getMessage()
                ]);
            }

            return redirect('/stock-analytics/confirmation');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred. Please try again.']);
        }
    }

    // ensureJKFormat and getCompanyName moved to StockService

    private function sendEmail($user, $request, $isNewUser, $password = null)
    {
        $data = [
            'user' => $user,
            'request' => $request,
            'isNewUser' => $isNewUser,
            'password' => $password,
        ];

        Mail::send('emails.stock-request', $data, function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Stock Analytics Request Confirmation');
        });
    }
}
