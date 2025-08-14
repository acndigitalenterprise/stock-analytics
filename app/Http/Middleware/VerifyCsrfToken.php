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
        'stock-analytics/reset-password',
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
        
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $exception) {
            \Log::info('CSRF token mismatch caught for: ' . $request->path());
            
            // Handle CSRF token mismatch for authentication forms
            if ($request->is('stock-analytics/signin') || $request->is('stock-analytics/signup') || $request->is('stock-analytics/register')) {
                \Log::info('Redirecting back with CSRF error for: ' . $request->path());
                return redirect()->back()->withErrors([
                    'csrf_error' => 'Your session has expired. Please try again.'
                ])->withInput($request->except(['password', '_token']));
            }
            
            // Re-throw the exception for other routes
            throw $exception;
        }
    }
} 