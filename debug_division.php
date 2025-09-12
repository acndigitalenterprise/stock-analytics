<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = new Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Set custom error handler for division by zero
set_error_handler(function ($severity, $message, $file, $line) {
    if ($severity === E_WARNING && strpos($message, 'Division by zero') !== false) {
        echo "DIVISION BY ZERO FOUND!\n";
        echo "Message: $message\n";
        echo "File: $file\n";
        echo "Line: $line\n";
        echo "Stack trace:\n";
        
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        foreach ($trace as $i => $call) {
            $file = isset($call['file']) ? $call['file'] : 'unknown';
            $line = isset($call['line']) ? $call['line'] : 'unknown';
            $function = isset($call['function']) ? $call['function'] : 'unknown';
            $class = isset($call['class']) ? $call['class'] : '';
            echo "  #$i $file:$line $class::$function()\n";
        }
        
        throw new DivisionByZeroError($message);
    }
    return false;
});

try {
    $request = \App\Models\Request::find(70);
    $request->update(['advice' => null]);
    
    echo "Testing advice generation with detailed error handling...\n";
    
    $job = new \App\Jobs\GenerateStockAdvice($request);
    $job->handle(
        app('App\Services\YahooFinanceService'),
        app('App\Services\AlphaVantageService'), 
        app('App\Services\TechnicalAnalysisService'),
        app('App\Services\ChatGPTService'),
        app('App\Services\PriceMonitoringService')
    );
    
    echo "SUCCESS: Job completed without errors!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Location: " . $e->getFile() . ":" . $e->getLine() . "\n";
}