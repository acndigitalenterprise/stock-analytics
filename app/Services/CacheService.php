<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Service for managing API response caching
 * 
 * This service provides centralized caching functionality for external API responses,
 * particularly for stock search results to improve performance and reduce API calls.
 */
class CacheService
{
    /**
     * Cache TTL for stock search results (in seconds)
     */
    private int $stockSearchTtl;

    /**
     * Cache key prefix for stock search
     */
    private const STOCK_SEARCH_PREFIX = 'stock_search:';

    /**
     * Cache key prefix for API responses
     */
    private const API_RESPONSE_PREFIX = 'api_response:';

    public function __construct()
    {
        $this->stockSearchTtl = config('services.stock_search.cache_ttl', 300);
    }

    /**
     * Get cached stock search results
     * 
     * @param string $query Search query
     * @param string $source API source (yahoo, alphavantage, local)
     * @return array|null Cached results or null if not found
     */
    public function getStockSearchResults(string $query, string $source = 'all'): ?array
    {
        $cacheKey = $this->getStockSearchCacheKey($query, $source);
        
        try {
            $cached = Cache::get($cacheKey);
            
            // Ensure we return array or null, not other types
            if ($cached !== null && is_array($cached)) {
                Log::info('Cache hit for stock search', [
                    'query' => $query,
                    'source' => $source,
                    'cache_key' => $cacheKey
                ]);
                return $cached;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Failed to retrieve from cache', [
                'cache_key' => $cacheKey,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Cache stock search results
     * 
     * @param string $query Search query
     * @param array $results Search results
     * @param string $source API source
     * @param int|null $ttl Custom TTL in seconds
     * @return bool Success status
     */
    public function cacheStockSearchResults(string $query, array $results, string $source = 'all', ?int $ttl = null): bool
    {
        $cacheKey = $this->getStockSearchCacheKey($query, $source);
        $cacheTtl = $ttl ?? $this->stockSearchTtl;
        
        try {
            $cached = Cache::put($cacheKey, $results, $cacheTtl);
            
            Log::info('Cached stock search results', [
                'query' => $query,
                'source' => $source,
                'cache_key' => $cacheKey,
                'ttl' => $cacheTtl,
                'results_count' => count($results)
            ]);
            
            return $cached;
        } catch (\Exception $e) {
            Log::error('Failed to cache stock search results', [
                'cache_key' => $cacheKey,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Get cached API response
     * 
     * @param string $key Cache key
     * @return mixed Cached response or null
     */
    public function getApiResponse(string $key)
    {
        $cacheKey = self::API_RESPONSE_PREFIX . $key;
        
        try {
            return Cache::get($cacheKey);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve API response from cache', [
                'cache_key' => $cacheKey,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Cache API response
     * 
     * @param string $key Cache key
     * @param mixed $response Response data
     * @param int $ttl TTL in seconds
     * @return bool Success status
     */
    public function cacheApiResponse(string $key, $response, int $ttl = 300): bool
    {
        $cacheKey = self::API_RESPONSE_PREFIX . $key;
        
        try {
            return Cache::put($cacheKey, $response, $ttl);
        } catch (\Exception $e) {
            Log::error('Failed to cache API response', [
                'cache_key' => $cacheKey,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Clear stock search cache for a specific query
     * 
     * @param string $query Search query
     * @param string $source API source
     * @return bool Success status
     */
    public function clearStockSearchCache(string $query, string $source = 'all'): bool
    {
        $cacheKey = $this->getStockSearchCacheKey($query, $source);
        
        try {
            return Cache::forget($cacheKey);
        } catch (\Exception $e) {
            Log::error('Failed to clear stock search cache', [
                'cache_key' => $cacheKey,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Clear all stock search cache
     * 
     * @return bool Success status
     */
    public function clearAllStockSearchCache(): bool
    {
        try {
            // This is a simple implementation - in production, you might want to use cache tags
            $pattern = self::STOCK_SEARCH_PREFIX . '*';
            
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                // For Redis, we can use pattern matching
                $redis = Cache::getStore()->getRedis();
                $keys = $redis->keys($pattern);
                
                if (!empty($keys)) {
                    $redis->del($keys);
                }
            } else {
                // For other cache drivers, we need to track keys manually
                // This is a limitation - consider using cache tags in production
                Log::warning('Cannot clear all cache keys for non-Redis cache driver');
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to clear all stock search cache', [
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Generate cache key for stock search
     * 
     * @param string $query Search query
     * @param string $source API source
     * @return string Cache key
     */
    private function getStockSearchCacheKey(string $query, string $source): string
    {
        $normalizedQuery = strtolower(trim($query));
        return self::STOCK_SEARCH_PREFIX . $source . ':' . md5($normalizedQuery);
    }

    /**
     * Get cache statistics
     * 
     * @return array Cache statistics
     */
    public function getCacheStats(): array
    {
        try {
            $stats = [
                'cache_driver' => config('cache.default'),
                'stock_search_ttl' => $this->stockSearchTtl,
                'cache_prefix' => config('cache.prefix'),
            ];

            // Add driver-specific stats if available
            $store = Cache::getStore();
            if (method_exists($store, 'getMemcached')) {
                $memcached = $store->getMemcached();
                $stats['memcached_stats'] = $memcached->getStats();
            } elseif (method_exists($store, 'getRedis')) {
                $redis = $store->getRedis();
                $stats['redis_info'] = $redis->info();
            }

            return $stats;
        } catch (\Exception $e) {
            Log::error('Failed to get cache stats', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'error' => 'Failed to retrieve cache statistics'
            ];
        }
    }

    /**
     * Get or set market insights with caching
     */
    public function getMarketInsights(): array
    {
        $cacheKey = 'market_insights';
        
        return Cache::remember($cacheKey, 30 * 60, function () {
            $yahooService = app(\App\Services\YahooFinanceService::class);
            return $yahooService->getMarketInsights();
        });
    }

    /**
     * Force refresh market insights
     */
    public function refreshMarketInsights(): array
    {
        Cache::forget('market_insights');
        return $this->getMarketInsights();
    }
}