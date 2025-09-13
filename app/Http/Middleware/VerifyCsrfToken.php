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
        'create-user',
        'signin',
        'signup',
        'settings'
    ];

} 