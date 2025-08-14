<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\LoggingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

/**
 * Unit tests for LoggingService
 * 
 * Tests logging functionality for various application events.
 */
class LoggingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LoggingService $loggingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loggingService = new LoggingService();
    }

    /** @test */
    public function it_can_log_security_events()
    {
        Log::shouldReceive('channel')
            ->with('security')
            ->once()
            ->andReturnSelf();
        
        Log::shouldReceive('warning')
            ->once()
            ->with('Failed login attempt', \Mockery::type('array'));

        $this->loggingService->logSecurityEvent('Failed login attempt', [
            'email' => 'test@example.com'
        ]);
    }

    /** @test */
    public function it_can_log_api_calls()
    {
        config(['app.log_api_calls' => true]);

        Log::shouldReceive('channel')
            ->with('api')
            ->once()
            ->andReturnSelf();
        
        Log::shouldReceive('info')
            ->once()
            ->with('API call to yahoo', \Mockery::type('array'));

        $this->loggingService->logApiCall('yahoo', '/v1/finance/search', [
            'query' => 'BBCA',
            'response_time' => 0.5
        ]);
    }

    /** @test */
    public function it_skips_api_logging_when_disabled()
    {
        config(['app.log_api_calls' => false]);

        Log::shouldReceive('channel')->never();
        Log::shouldReceive('info')->never();

        $this->loggingService->logApiCall('yahoo', '/v1/finance/search');
    }

    /** @test */
    public function it_can_log_user_actions()
    {
        config(['app.log_user_actions' => true]);

        Log::shouldReceive('channel')
            ->with('user_actions')
            ->once()
            ->andReturnSelf();
        
        Log::shouldReceive('info')
            ->once()
            ->with('Stock search performed', \Mockery::type('array'));

        $this->loggingService->logUserAction('Stock search performed', [
            'query' => 'BBCA',
            'results_count' => 5
        ]);
    }

    /** @test */
    public function it_can_log_performance_metrics()
    {
        config(['app.log_performance_metrics' => true]);

        Log::shouldReceive('channel')
            ->with('performance')
            ->once()
            ->andReturnSelf();
        
        Log::shouldReceive('info')
            ->once()
            ->with('API response time', \Mockery::type('array'));

        $this->loggingService->logPerformance('API response time', [
            'service' => 'yahoo',
            'response_time' => 1.2,
            'query' => 'BBCA'
        ]);
    }

    /** @test */
    public function it_can_log_system_errors()
    {
        Log::shouldReceive('channel')
            ->with('errors')
            ->once()
            ->andReturnSelf();
        
        Log::shouldReceive('error')
            ->once()
            ->with(\Mockery::type('string'), \Mockery::type('array'));

        $exception = new \Exception('Test error message');
        $this->loggingService->logSystemError($exception, [
            'additional_context' => 'test'
        ]);
    }

    /** @test */
    public function it_can_log_authentication_events()
    {
        Log::shouldReceive('channel')
            ->with('auth')
            ->once()
            ->andReturnSelf();
        
        Log::shouldReceive('info')
            ->once()
            ->with('Auth: user_login', \Mockery::type('array'));

        $this->loggingService->logAuthEvent('user_login', [
            'user_id' => 1,
            'email' => 'test@example.com'
        ]);
    }

    /** @test */
    public function it_can_log_stock_search_events()
    {
        Log::shouldReceive('channel')
            ->with('stock_analytics')
            ->once()
            ->andReturnSelf();
        
        Log::shouldReceive('info')
            ->once()
            ->with('Stock search', \Mockery::type('array'));

        $this->loggingService->logStockSearch('BBCA', 'yahoo', 5, false, 0.8);
    }

    /** @test */
    public function it_can_log_ai_advice_events()
    {
        Log::shouldReceive('channel')
            ->with('ai')
            ->once()
            ->andReturnSelf();
        
        Log::shouldReceive('info')
            ->once()
            ->with('AI advice completed for BBCA.JK', \Mockery::type('array'));

        $this->loggingService->logAiAdvice(1, 'BBCA.JK', 'completed', [
            'processing_time' => 5.2,
            'model' => 'gpt-4'
        ]);
    }

    /** @test */
    public function it_logs_ai_failures_as_errors()
    {
        Log::shouldReceive('channel')
            ->with('ai')
            ->once()
            ->andReturnSelf();
        
        Log::shouldReceive('error')
            ->once()
            ->with('AI advice failed for BBCA.JK', \Mockery::type('array'));

        $this->loggingService->logAiAdvice(1, 'BBCA.JK', 'failed', [
            'error' => 'API timeout'
        ]);
    }

    /** @test */
    public function it_formats_exceptions_properly()
    {
        $exception = new \InvalidArgumentException('Test error', 404);
        $formatted = $this->loggingService->formatException($exception);

        $this->assertIsArray($formatted);
        $this->assertArrayHasKey('exception_class', $formatted);
        $this->assertArrayHasKey('message', $formatted);
        $this->assertArrayHasKey('file', $formatted);
        $this->assertArrayHasKey('line', $formatted);
        $this->assertArrayHasKey('code', $formatted);
        $this->assertArrayHasKey('trace', $formatted);
        
        $this->assertEquals('InvalidArgumentException', $formatted['exception_class']);
        $this->assertEquals('Test error', $formatted['message']);
        $this->assertEquals(404, $formatted['code']);
    }

    /** @test */
    public function it_gets_request_context()
    {
        $context = $this->loggingService->getRequestContext();

        $this->assertIsArray($context);
        $this->assertArrayHasKey('ip', $context);
        $this->assertArrayHasKey('user_agent', $context);
        $this->assertArrayHasKey('url', $context);
        $this->assertArrayHasKey('method', $context);
        $this->assertArrayHasKey('timestamp', $context);
    }
}