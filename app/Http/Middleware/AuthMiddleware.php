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

        // CRITICAL FIX: Refresh user data from database to prevent stale session data
        $sessionUser = session('user');
        $freshUser = \App\Models\User::find($sessionUser->id);
        
        if (!$freshUser) {
            // User was deleted from database
            session()->flush();
            return redirect()->route('auth.signin.page')
                ->withErrors(['error' => 'Your account is no longer valid. Please login again.']);
        }
        
        // Update session with fresh user data
        session(['user' => $freshUser]);
        
        \Log::info('AuthMiddleware: User data refreshed', [
            'user_id' => $freshUser->id,
            'user_email' => $freshUser->email,
            'user_role' => $freshUser->role
        ]);

        return $next($request);
    }
}
