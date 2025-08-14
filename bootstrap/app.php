<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register custom middleware aliases
        $middleware->alias([
            'rate_limit' => \App\Http\Middleware\RateLimitMiddleware::class,
            'auth.session' => \App\Http\Middleware\AuthMiddleware::class,
        ]);

        // Use custom CSRF middleware
        $middleware->replace(
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\VerifyCsrfToken::class
        );

        // Add global middleware
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);

        // Web middleware group
        $middleware->web(append: [
            // Additional web middleware if needed
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle CSRF token mismatch for authentication forms
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            // If it's an authentication request, redirect back with a user-friendly error
            if ($request->is('stock-analytics/signin') || $request->is('stock-analytics/signup') || $request->is('stock-analytics/register')) {
                return redirect()->back()->withErrors([
                    'csrf_error' => 'Your session has expired. Please try again.'
                ])->withInput($request->except(['password', '_token']));
            }
        });
    })->create();
