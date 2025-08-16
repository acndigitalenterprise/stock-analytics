<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Log::info('AuthMiddleware: Checking session', [
            'url' => $request->url(), 
            'has_session' => session()->has('user'),
            'session_id' => session()->getId()
        ]);
        
        if (!session('user')) {
            \Log::error('AuthMiddleware: No user in session, redirecting');
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect()->route('stock-analytics.index')
                ->withErrors(['error' => 'Please login to access this page.']);
        }

        \Log::info('AuthMiddleware: User found, proceeding');
        return $next($request);
    }
}
