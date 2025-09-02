<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = session('user');
        
        if (!$user) {
            return redirect()->route('auth.signin.page')
                ->withErrors(['error' => 'Please login to access this page.']);
        }
        
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            return redirect()->route('dashboard')
                ->withErrors(['error' => 'You do not have admin access to this page.']);
        }
        
        return $next($request);
    }
}
