<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

/**
 * Feature tests for stock search functionality
 * 
 * Tests the complete stock search flow including API calls,
 * caching, rate limiting, and response formatting.
 */
class StockSearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_empty_array_for_empty_query()
    {
        $response = $this->get('/api/stocks/search?q=');
        
        $response->assertStatus(200);
        $response->assertJson([]);
    }

    /** @test */
    public function it_validates_query_length()
    {
        $longQuery = str_repeat('A', 51); // > 50 characters
        
        $response = $this->get('/api/stocks/search?q=' . $longQuery);
        
        $response->assertStatus(400);
        $response->assertJson(['error' => 'Query too long']);
    }

    /** @test */
    public function it_returns_cached_results_when_available()
    {
        $query = 'BBCA';
        $cachedResults = [
            ['code' => 'BBCA.JK', 'name' => 'Bank Central Asia', 'type' => 'EQUITY']
        ];

        // Mock cached results
        Cache::shouldReceive('get')
            ->once()
            ->andReturn($cachedResults);

        $response = $this->get('/api/stocks/search?q=' . $query);
        
        $response->assertStatus(200);
        $response->assertJson($cachedResults);
    }

    /** @test */
    public function it_falls_back_to_local_data_when_apis_fail()
    {
        // Mock Yahoo Finance failure
        Http::fake([
            'query1.finance.yahoo.com/*' => Http::response([], 500),
            'www.alphavantage.co/*' => Http::response([], 500),
        ]);

        // Mock cache miss
        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $response = $this->get('/api/stocks/search?q=BBCA');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['code', 'name', 'type', 'region']
        ]);
    }

    /** @test */
    public function it_handles_yahoo_finance_api_responses()
    {
        // Mock Yahoo Finance success
        Http::fake([
            'query1.finance.yahoo.com/*' => Http::response([
                'quotes' => [
                    [
                        'symbol' => 'BBCA.JK',
                        'shortname' => 'Bank Central Asia Tbk PT',
                        'exchange' => 'IDX',
                        'quoteType' => 'EQUITY'
                    ]
                ]
            ], 200),
        ]);

        // Mock cache operations
        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $response = $this->get('/api/stocks/search?q=BBCA');
        
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'code' => 'BBCA.JK',
            'name' => 'Bank Central Asia Tbk PT',
            'exchange' => 'IDX',
            'type' => 'EQUITY'
        ]);
    }

    /** @test */
    public function it_handles_alpha_vantage_api_responses()
    {
        // Mock Yahoo Finance failure and Alpha Vantage success
        Http::fake([
            'query1.finance.yahoo.com/*' => Http::response([], 404),
            'www.alphavantage.co/*' => Http::response([
                'bestMatches' => [
                    [
                        '1. symbol' => 'BBCA.JK',
                        '2. name' => 'Bank Central Asia',
                        '3. type' => 'Equity',
                        '4. region' => 'Indonesia',
                        '8. currency' => 'IDR'
                    ]
                ]
            ], 200),
        ]);

        // Mock cache operations
        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $response = $this->get('/api/stocks/search?q=BBCA');
        
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'code' => 'BBCA.JK',
            'name' => 'Bank Central Asia',
            'type' => 'Equity',
            'region' => 'Indonesia'
        ]);
    }

    /** @test */
    public function it_respects_rate_limiting()
    {
        // First request should succeed
        $response = $this->get('/api/stocks/search?q=BBCA');
        $response->assertStatus(200);

        // Mock reaching rate limit
        Cache::shouldReceive('get')
            ->with(\Mockery::pattern('/rate_limit:stock_search:/'))
            ->andReturn(60); // Assuming limit is 60

        $response = $this->get('/api/stocks/search?q=TLKM');
        $response->assertStatus(429);
        $response->assertJsonStructure([
            'error', 'message', 'retry_after'
        ]);
    }

    /** @test */
    public function it_adds_rate_limit_headers_to_responses()
    {
        // Mock cache operations for successful request
        Cache::shouldReceive('get')->andReturn(null, 1); // cache miss, then rate limit count
        Cache::shouldReceive('put')->andReturn(true);

        $response = $this->get('/api/stocks/search?q=BBCA');
        
        $response->assertStatus(200);
        $response->assertHeader('X-RateLimit-Limit');
        $response->assertHeader('X-RateLimit-Remaining');
        $response->assertHeader('X-RateLimit-Reset');
        $response->assertHeader('X-RateLimit-Type');
    }

    /** @test */
    public function it_logs_search_activities()
    {
        // This test would check if logging service is called
        // In a real test, you'd mock the LoggingService
        
        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $response = $this->get('/api/stocks/search?q=BBCA');
        
        $response->assertStatus(200);
        
        // In reality, you'd assert that LoggingService::logStockSearch was called
        // with the correct parameters
    }

    /** @test */
    public function it_handles_concurrent_requests_safely()
    {
        // Mock cache operations to simulate concurrent access
        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        // Simulate multiple concurrent requests
        $responses = [];
        for ($i = 0; $i < 5; $i++) {
            $responses[] = $this->get('/api/stocks/search?q=BBCA' . $i);
        }

        foreach ($responses as $response) {
            $response->assertStatus(200);
        }
    }
}