<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

/**
 * Unit tests for CacheService
 * 
 * Tests caching functionality for stock search results
 * and API responses.
 */
class CacheServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CacheService $cacheService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheService = new CacheService();
    }

    /** @test */
    public function it_can_cache_and_retrieve_stock_search_results()
    {
        $query = 'BBCA';
        $source = 'yahoo';
        $results = [
            ['code' => 'BBCA.JK', 'name' => 'Bank Central Asia', 'type' => 'EQUITY']
        ];

        // Cache the results
        $cached = $this->cacheService->cacheStockSearchResults($query, $results, $source);
        $this->assertTrue($cached);

        // Retrieve the results
        $retrieved = $this->cacheService->getStockSearchResults($query, $source);
        $this->assertEquals($results, $retrieved);
    }

    /** @test */
    public function it_returns_null_for_missing_cache_keys()
    {
        $result = $this->cacheService->getStockSearchResults('NONEXISTENT', 'yahoo');
        $this->assertNull($result);
    }

    /** @test */
    public function it_can_cache_api_responses()
    {
        $key = 'test_api_response';
        $response = ['status' => 'success', 'data' => ['test' => 'value']];
        $ttl = 300;

        // Cache the response
        $cached = $this->cacheService->cacheApiResponse($key, $response, $ttl);
        $this->assertTrue($cached);

        // Retrieve the response
        $retrieved = $this->cacheService->getApiResponse($key);
        $this->assertEquals($response, $retrieved);
    }

    /** @test */
    public function it_can_clear_stock_search_cache()
    {
        $query = 'TLKM';
        $source = 'local';
        $results = [
            ['code' => 'TLKM.JK', 'name' => 'Telkom Indonesia', 'type' => 'EQUITY']
        ];

        // Cache the results
        $this->cacheService->cacheStockSearchResults($query, $results, $source);
        
        // Verify it's cached
        $retrieved = $this->cacheService->getStockSearchResults($query, $source);
        $this->assertEquals($results, $retrieved);

        // Clear the cache
        $cleared = $this->cacheService->clearStockSearchCache($query, $source);
        $this->assertTrue($cleared);

        // Verify it's cleared
        $retrieved = $this->cacheService->getStockSearchResults($query, $source);
        $this->assertNull($retrieved);
    }

    /** @test */
    public function it_generates_consistent_cache_keys()
    {
        $query1 = 'BBCA';
        $query2 = 'bbca'; // Different case
        $source = 'yahoo';
        $results = [['code' => 'BBCA.JK', 'name' => 'Bank Central Asia']];

        // Cache with uppercase
        $this->cacheService->cacheStockSearchResults($query1, $results, $source);
        
        // Retrieve with lowercase (should work due to normalization)
        $retrieved = $this->cacheService->getStockSearchResults($query2, $source);
        $this->assertEquals($results, $retrieved);
    }

    /** @test */
    public function it_handles_cache_errors_gracefully()
    {
        // Mock cache failure
        Cache::shouldReceive('get')->andThrow(new \Exception('Cache error'));
        
        $result = $this->cacheService->getStockSearchResults('TEST', 'yahoo');
        $this->assertNull($result);
    }

    /** @test */
    public function it_provides_cache_statistics()
    {
        $stats = $this->cacheService->getCacheStats();
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('cache_driver', $stats);
        $this->assertArrayHasKey('stock_search_ttl', $stats);
    }
}