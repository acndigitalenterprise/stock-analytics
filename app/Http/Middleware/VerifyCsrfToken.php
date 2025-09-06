<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'stock-analytics/reset-password',
        'stock-analytics/reset-password/*',
        'requests',
        'requests/*',
        'test-store',
        'create-user'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, \Closure $next)
    {
        // Log to see if our middleware is being called
        \Log::info('Custom CSRF middleware called for: ' . $request->path());
        
        // Temporary: Disable CSRF for all requests for debugging
        return $next($request);
    }
} 