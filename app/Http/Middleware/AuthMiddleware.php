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
        \Log::info('AuthMiddleware: Checking user session', [
            'url' => $request->url(),
            'method' => $request->method(),
            'has_user' => session()->has('user'),
            'user' => session('user') ? 'exists' : 'null'
        ]);
        
        if (!session('user')) {
            \Log::warning('AuthMiddleware: No user in session, redirecting', [
                'url' => $request->url(),
                'expects_json' => $request->expectsJson()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect()->route('auth.signin.page')
                ->withErrors(['error' => 'Please login to access this page.']);
        }

        \Log::info('AuthMiddleware: User authenticated, proceeding');
        return $next($request);
    }
}
