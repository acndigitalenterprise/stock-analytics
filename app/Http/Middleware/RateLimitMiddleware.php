<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rate limiting middleware for API endpoints
 * 
 * Provides configurable rate limiting based on IP address and endpoint type
 * with different limits for different types of operations.
 */
class RateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $type = 'default'): Response
    {
        $ip = $this->getClientIp($request);
        $rateLimitConfig = $this->getRateLimitConfig($type);
        
        if (!$rateLimitConfig) {
            return $next($request);
        }

        $cacheKey = "rate_limit:{$type}:{$ip}";
        $windowStart = now()->startOfMinute()->timestamp;
        $windowKey = "{$cacheKey}:{$windowStart}";

        // Get current request count for this window
        $requestCount = Cache::get($windowKey, 0);

        // Check if rate limit exceeded
        if ($requestCount >= $rateLimitConfig['limit']) {
            Log::warning('Rate limit exceeded', [
                'ip' => $ip,
                'type' => $type,
                'limit' => $rateLimitConfig['limit'],
                'current_count' => $requestCount,
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl()
            ]);

            return response()->json([
                'error' => 'Rate limit exceeded',
                'message' => "Too many requests. Limit: {$rateLimitConfig['limit']} per minute.",
                'retry_after' => 60 - now()->second
            ], 429);
        }

        // Increment request count
        Cache::put($windowKey, $requestCount + 1, 60); // Cache for 1 minute

        // Add rate limit headers to response
        $response = $next($request);
        
        $this->addRateLimitHeaders($response, $rateLimitConfig, $requestCount + 1);

        // Log request for monitoring
        if (config('app.debug') || config('app.log_rate_limits', false)) {
            Log::info('Rate limit check passed', [
                'ip' => $ip,
                'type' => $type,
                'count' => $requestCount + 1,
                'limit' => $rateLimitConfig['limit']
            ]);
        }

        return $response;
    }

    /**
     * Get client IP address
     * 
     * @param Request $request
     * @return string
     */
    private function getClientIp(Request $request): string
    {
        // Check for shared internet/proxy
        if (!empty($request->server('HTTP_CLIENT_IP'))) {
            $ip = $request->server('HTTP_CLIENT_IP');
        } elseif (!empty($request->server('HTTP_X_FORWARDED_FOR'))) {
            $ip = $request->server('HTTP_X_FORWARDED_FOR');
        } elseif (!empty($request->server('HTTP_X_FORWARDED'))) {
            $ip = $request->server('HTTP_X_FORWARDED');
        } elseif (!empty($request->server('HTTP_FORWARDED_FOR'))) {
            $ip = $request->server('HTTP_FORWARDED_FOR');
        } elseif (!empty($request->server('HTTP_FORWARDED'))) {
            $ip = $request->server('HTTP_FORWARDED');
        } else {
            $ip = $request->server('REMOTE_ADDR');
        }

        // Handle comma separated IPs (from load balancers)
        if (str_contains($ip, ',')) {
            $ip = trim(explode(',', $ip)[0]);
        }

        return $ip ?: '0.0.0.0';
    }

    /**
     * Get rate limit configuration for endpoint type
     * 
     * @param string $type
     * @return array|null
     */
    private function getRateLimitConfig(string $type): ?array
    {
        $configs = [
            'stock_search' => [
                'limit' => env('RATE_LIMIT_STOCK_SEARCH', 60),
                'window' => 60, // seconds
                'name' => 'Stock Search'
            ],
            'stock_submit' => [
                'limit' => env('RATE_LIMIT_STOCK_SUBMIT', 10),
                'window' => 60, // seconds
                'name' => 'Stock Request Submission'
            ],
            'auth' => [
                'limit' => env('RATE_LIMIT_AUTH', 5),
                'window' => 60, // seconds
                'name' => 'Authentication'
            ],
            'default' => [
                'limit' => env('RATE_LIMIT_DEFAULT', 100),
                'window' => 60, // seconds
                'name' => 'Default'
            ]
        ];

        return $configs[$type] ?? $configs['default'];
    }

    /**
     * Add rate limit headers to response
     * 
     * @param Response $response
     * @param array $config
     * @param int $currentCount
     * @return void
     */
    private function addRateLimitHeaders($response, array $config, int $currentCount): void
    {
        $remaining = max(0, $config['limit'] - $currentCount);
        $resetTime = now()->addSeconds($config['window'])->timestamp;

        $response->headers->set('X-RateLimit-Limit', $config['limit']);
        $response->headers->set('X-RateLimit-Remaining', $remaining);
        $response->headers->set('X-RateLimit-Reset', $resetTime);
        $response->headers->set('X-RateLimit-Type', $config['name']);
        
        if ($remaining === 0) {
            $response->headers->set('Retry-After', $config['window']);
        }
    }

    /**
     * Check if IP is whitelisted
     * 
     * @param string $ip
     * @return bool
     */
    private function isWhitelisted(string $ip): bool
    {
        $whitelist = config('app.rate_limit_whitelist', []);
        
        if (empty($whitelist)) {
            return false;
        }

        foreach ($whitelist as $whitelistedIp) {
            if ($ip === $whitelistedIp || $this->ipInRange($ip, $whitelistedIp)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP is in range (CIDR notation support)
     * 
     * @param string $ip
     * @param string $range
     * @return bool
     */
    private function ipInRange(string $ip, string $range): bool
    {
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }

        [$range, $netmask] = explode('/', $range, 2);
        $rangeDecimal = ip2long($range);
        $ipDecimal = ip2long($ip);
        $wildcardDecimal = pow(2, (32 - $netmask)) - 1;
        $netmaskDecimal = ~$wildcardDecimal;

        return ($ipDecimal & $netmaskDecimal) === ($rangeDecimal & $netmaskDecimal);
    }
}