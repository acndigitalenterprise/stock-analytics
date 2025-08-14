<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

/**
 * Centralized logging service for the application
 * 
 * Provides structured logging for security events, API calls,
 * user actions, and system events with consistent formatting.
 */
class LoggingService
{
    /**
     * Log security events
     * 
     * @param string $event Event type
     * @param array $context Additional context
     * @param string $level Log level (info, warning, error)
     * @return void
     */
    public function logSecurityEvent(string $event, array $context = [], string $level = 'warning'): void
    {
        $logData = [
            'type' => 'security',
            'event' => $event,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'timestamp' => now()->toISOString(),
            'session_id' => session()->getId(),
            'user_id' => auth()->id(),
        ];

        $logData = array_merge($logData, $context);

        Log::channel('security')->{$level}($event, $logData);
    }

    /**
     * Log API calls to external services
     * 
     * @param string $service Service name (yahoo, alphavantage, openai)
     * @param string $endpoint Endpoint called
     * @param array $context Additional context
     * @param string $level Log level
     * @return void
     */
    public function logApiCall(string $service, string $endpoint, array $context = [], string $level = 'info'): void
    {
        if (!config('app.log_api_calls', true)) {
            return;
        }

        $logData = [
            'type' => 'api_call',
            'service' => $service,
            'endpoint' => $endpoint,
            'timestamp' => now()->toISOString(),
            'ip' => request()->ip(),
            'user_id' => auth()->id(),
        ];

        $logData = array_merge($logData, $context);

        Log::channel('api')->{$level}("API call to {$service}", $logData);
    }

    /**
     * Log user actions
     * 
     * @param string $action Action performed
     * @param array $context Additional context
     * @param string $level Log level
     * @return void
     */
    public function logUserAction(string $action, array $context = [], string $level = 'info'): void
    {
        if (!config('app.log_user_actions', true)) {
            return;
        }

        $logData = [
            'type' => 'user_action',
            'action' => $action,
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
            'session_id' => session()->getId(),
        ];

        $logData = array_merge($logData, $context);

        Log::channel('user_actions')->{$level}($action, $logData);
    }

    /**
     * Log performance metrics
     * 
     * @param string $metric Metric name
     * @param array $data Metric data
     * @return void
     */
    public function logPerformance(string $metric, array $data): void
    {
        if (!config('app.log_performance_metrics', true)) {
            return;
        }

        $logData = [
            'type' => 'performance',
            'metric' => $metric,
            'timestamp' => now()->toISOString(),
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
        ];

        $logData = array_merge($logData, $data);

        Log::channel('performance')->info($metric, $logData);
    }

    /**
     * Log system errors with enhanced context
     * 
     * @param \Throwable $exception Exception instance
     * @param array $context Additional context
     * @return void
     */
    public function logSystemError(\Throwable $exception, array $context = []): void
    {
        $logData = [
            'type' => 'system_error',
            'exception_class' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
        ];

        $logData = array_merge($logData, $context);

        Log::channel('errors')->error('System Error: ' . $exception->getMessage(), $logData);
    }

    /**
     * Log database operations
     * 
     * @param string $operation Operation type (insert, update, delete)
     * @param string $table Table name
     * @param array $context Additional context
     * @return void
     */
    public function logDatabaseOperation(string $operation, string $table, array $context = []): void
    {
        if (!config('app.log_database_operations', false)) {
            return;
        }

        $logData = [
            'type' => 'database_operation',
            'operation' => $operation,
            'table' => $table,
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'timestamp' => now()->toISOString(),
        ];

        $logData = array_merge($logData, $context);

        Log::channel('database')->info("DB {$operation} on {$table}", $logData);
    }

    /**
     * Log authentication events
     * 
     * @param string $event Event type (login, logout, failed_login, etc.)
     * @param array $context Additional context
     * @param string $level Log level
     * @return void
     */
    public function logAuthEvent(string $event, array $context = [], string $level = 'info'): void
    {
        $logData = [
            'type' => 'authentication',
            'event' => $event,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
            'session_id' => session()->getId(),
        ];

        $logData = array_merge($logData, $context);

        Log::channel('auth')->{$level}("Auth: {$event}", $logData);
    }

    /**
     * Log stock search events
     * 
     * @param string $query Search query
     * @param string $source Data source used
     * @param int $resultCount Number of results
     * @param bool $cached Whether result was cached
     * @param float $responseTime Response time in seconds
     * @return void
     */
    public function logStockSearch(string $query, string $source, int $resultCount, bool $cached = false, float $responseTime = 0): void
    {
        $logData = [
            'type' => 'stock_search',
            'query' => $query,
            'source' => $source,
            'result_count' => $resultCount,
            'cached' => $cached,
            'response_time' => $responseTime,
            'ip' => request()->ip(),
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString(),
        ];

        Log::channel('stock_analytics')->info('Stock search', $logData);
    }

    /**
     * Log AI advice generation events
     * 
     * @param int $requestId Request ID
     * @param string $stockCode Stock code
     * @param string $status Status (started, completed, failed)
     * @param array $context Additional context
     * @return void
     */
    public function logAiAdvice(int $requestId, string $stockCode, string $status, array $context = []): void
    {
        $logData = [
            'type' => 'ai_advice',
            'request_id' => $requestId,
            'stock_code' => $stockCode,
            'status' => $status,
            'timestamp' => now()->toISOString(),
        ];

        $logData = array_merge($logData, $context);

        $level = $status === 'failed' ? 'error' : 'info';
        Log::channel('ai')->{$level}("AI advice {$status} for {$stockCode}", $logData);
    }

    /**
     * Get structured context from request
     * 
     * @param Request|null $request
     * @return array
     */
    public function getRequestContext(?Request $request = null): array
    {
        $request = $request ?: request();
        
        return [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'referrer' => $request->header('referer'),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Format exception for logging
     * 
     * @param \Throwable $exception
     * @return array
     */
    public function formatException(\Throwable $exception): array
    {
        return [
            'exception_class' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $exception->getCode(),
            'trace' => $exception->getTraceAsString(),
        ];
    }
}