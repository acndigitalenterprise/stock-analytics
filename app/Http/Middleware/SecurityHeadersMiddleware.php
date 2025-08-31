<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security headers middleware
 * 
 * Adds security headers to HTTP responses to protect against
 * common web vulnerabilities like XSS, clickjacking, etc.
 */
class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Content Security Policy
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'",
            // Allow Google Fonts
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "img-src 'self' data: https:",
            "font-src 'self' data: https://fonts.gstatic.com",
            "connect-src 'self' https://query1.finance.yahoo.com https://www.alphavantage.co",
            // No external frames allowed
            "frame-src 'self'",
            "worker-src 'self' blob:", // Allow web workers
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'"
        ]);

        // Security headers
        $headers = [
            // Prevent XSS attacks
            'X-XSS-Protection' => '1; mode=block',
            
            // Prevent content type sniffing
            'X-Content-Type-Options' => 'nosniff',
            
            // Prevent clickjacking
            'X-Frame-Options' => 'DENY',
            
            // Content Security Policy
            'Content-Security-Policy' => $csp,
            
            // Referrer Policy
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            
            // Permissions Policy (formerly Feature Policy)
            'Permissions-Policy' => 'microphone=(), camera=(), geolocation=(), payment=()',
        ];

        // Add HSTS header for HTTPS connections
        if ($request->secure()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        // Add headers to response
        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        // Remove server information
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}