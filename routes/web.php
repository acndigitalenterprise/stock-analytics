<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockAnalyticsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\SignalsController;

/*
|--------------------------------------------------------------------------
| Ticker AI - Web Routes
|--------------------------------------------------------------------------
| Clean, flexible route structure for production deployment
| Brand-agnostic and domain-flexible design
*/

// =================================
// PUBLIC ROUTES
// =================================

// Homepage
Route::get('/', function () {
    if (session()->has('user')) {
        return redirect()->route('dashboard');
    }
    return view('home.home');
})->name('home');

// Authentication Pages
Route::get('/signin', function () {
    if (session()->has('user')) {
        return redirect()->route('dashboard');
    }
    return view('home.signin');
})->name('auth.signin.page');

Route::get('/signup', function () {
    if (session()->has('user')) {
        return redirect()->route('dashboard');
    }
    return view('home.signup');
})->name('auth.signup.page');

Route::get('/forgot-password', function () {
    if (session()->has('user')) {
        return redirect()->route('dashboard');
    }
    return view('home.forgotpassword');
})->name('auth.forgot.page');

Route::get('/email-verification', function () {
    return view('home.emailverification');
})->name('auth.verified');

Route::get('/privacy-policy', function () {
    return view('home.privacypolicy');
})->name('privacy');

Route::get('/disclaimer', function () {
    return view('home.disclaimer');
})->name('disclaimer');

Route::get('/about', function () {
    return view('home.about');
})->name('about');

Route::get('/contacts', function () {
    return view('home.contacts');
})->name('contacts');

Route::post('/contacts/send', [App\Http\Controllers\ContactController::class, 'sendMessage'])
    ->name('contacts.send');

// Password Reset with Token
Route::get('/reset-password/{token}', function ($token) {
    if (session()->has('user')) {
        return redirect()->route('dashboard');
    }
    return view('home.resetpassword', compact('token'));
})->name('auth.reset.page');


// =================================
// API ROUTES
// =================================

// Stock search API with rate limiting
Route::get('/api/stocks/search', [StockAnalyticsController::class, 'searchStocks'])
    ->middleware('rate_limit:stock_search')
    ->name('api.stocks.search');

// =================================
// AUTHENTICATION ACTIONS
// =================================

// Auth form submissions
Route::post('/signin', [AuthController::class, 'signin'])
    ->middleware('rate_limit:auth')
    ->name('auth.signin');

Route::post('/signup', [AuthController::class, 'signup'])
    ->middleware('rate_limit:auth')
    ->name('auth.signup');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
    ->middleware('rate_limit:auth')
    ->name('auth.forgot');

Route::post('/reset-password/{token}', [AuthController::class, 'resetPassword'])
    ->middleware('rate_limit:auth')
    ->name('auth.reset');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('auth.logout');

// Email verification
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])
    ->name('auth.verify');


// =================================
// AUTHENTICATED ROUTES
// =================================

// Authenticated Routes Group
Route::middleware(['auth.session'])->group(function () {
    // Main Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])
        ->name('dashboard');
    
    // Market Page
    Route::get('/market', [MarketController::class, 'index'])
        ->name('market.index');

    Route::post('/market/refresh', [MarketController::class, 'refresh'])
        ->name('market.refresh');

    // Signals Page
    Route::get('/signals', [SignalsController::class, 'index'])
        ->name('signals.index');

    Route::get('/api/signals', [SignalsController::class, 'getSignals'])
        ->name('api.signals.get');

    Route::get('/api/signals/stats', [SignalsController::class, 'getStats'])
        ->name('api.signals.stats');

    Route::get('/api/signals/{signal}', [SignalsController::class, 'show'])
        ->name('api.signals.show');
    
    // Stock Requests Management
    Route::get('/requests', [AdminController::class, 'stocks'])
        ->name('requests.index');
    
    Route::get('/requests/{id}', [AdminController::class, 'detail'])
        ->name('requests.show');
    
    Route::post('/requests', [AdminController::class, 'store'])
        ->name('requests.store');
    
    Route::post('/requests/{id}/update', [AdminController::class, 'update'])
        ->name('requests.update');
    
    Route::post('/requests/{id}/delete', [AdminController::class, 'delete'])
        ->name('requests.destroy');
    
    Route::get('/requests/{id}/advice', [AdminController::class, 'getAdvice'])
        ->name('requests.advice');
    
    // User Management (Admin only)
    Route::middleware(['admin.access'])->group(function () {
        Route::get('/users', [AdminController::class, 'users'])
            ->name('users.index');
        
        Route::get('/users/{id}', [AdminController::class, 'userDetail'])
            ->name('users.show');
        
        Route::post('/users', [AdminController::class, 'createUser'])
            ->name('users.store');
        
        Route::post('/create-user', function(\Illuminate\Http\Request $request) {
            try {
                // Simple user creation without complex validation
                \Log::info('Simple create user called', $request->all());
                
                $user = new \App\Models\User();
                $user->name = $request->full_name;
                $user->email = $request->email;
                $user->mobile_number = $request->mobile_number;
                $user->password = \Hash::make($request->password);
                $user->role = $request->role;
                $user->save();
                
                // Clear dashboard cache since new user was added
                \Cache::flush();
                
                \Log::info('User created', ['id' => $user->id]);
                return redirect()->route('users.index')->with('success', 'User created successfully!');
            } catch (\Exception $e) {
                \Log::error('User creation failed', ['error' => $e->getMessage()]);
                return redirect()->route('users.index')->with('error', 'Error: ' . $e->getMessage());
            }
        })->name('users.create.simple');
        
        Route::post('/users/{id}/update', [AdminController::class, 'updateUser'])
            ->name('users.update');
        
        Route::post('/users/{id}/delete', [AdminController::class, 'deleteUser'])
            ->name('users.destroy');
        
        Route::post('/users/{id}/verify', [AdminController::class, 'verifyUser'])
            ->name('users.verify');
    });
    
    // User Settings
    Route::get('/settings', [SettingsController::class, 'profile'])
        ->name('settings');

    Route::post('/settings', [SettingsController::class, 'updateProfile'])
        ->name('settings.update');

    // DEBUG ROUTE - Remove after fixing
    Route::post('/settings/debug', [SettingsController::class, 'debugForm'])
        ->name('settings.debug');

    // Admin monitoring tools
    Route::post('/admin/process-timeouts', function() {
        try {
            $monitoringService = app(\App\Services\PriceMonitoringService::class);
            $timeoutCount = $monitoringService->processTimeoutRequests();

            \Log::info('Manual timeout processing triggered', [
                'timeout_count' => $timeoutCount,
                'triggered_by' => 'admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Processed {$timeoutCount} timeout requests",
                'timeout_count' => $timeoutCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Manual timeout processing failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    })->name('admin.process.timeouts');
});

// =================================
// LEGACY REDIRECTS (for compatibility)
// =================================

// Old stock-analytics routes redirect to new clean URLs
Route::get('/stock-analytics', function () {
    return redirect()->route('home');
});

Route::get('/stock-analytics/admin', function () {
    return redirect()->route('dashboard');
});

Route::get('/stock-analytics/admin/{path}', function ($path) {
    $routeMap = [
        'dashboard' => 'dashboard',
        'requests' => 'requests.index',
        'stocks' => 'requests.index', // legacy compatibility
        'users' => 'users.index',
    ];
    
    if (isset($routeMap[$path])) {
        return redirect()->route($routeMap[$path]);
    }
    
    return redirect()->route('dashboard');
})->where('path', '.*');

// =================================
// FALLBACK ROUTES
// =================================

// Handle confirmation pages
Route::get('/confirmation', function () {
    return view('confirmation');
})->name('confirmation');

Route::get('/registration-success', function () {
    return view('registration-success');
})->name('registration.success');

// View sent emails (admin)
Route::get('/emails/sent', function () {
    $emails = []; // Add your email logic here
    return view('emails.view-sent', compact('emails'));
})->name('emails.sent');

// Emergency timeout processing (no auth required for urgent fixes)
Route::post('/emergency/process-timeouts', function() {
    try {
        $monitoringService = app(\App\Services\PriceMonitoringService::class);
        $timeoutCount = $monitoringService->processTimeoutRequests();

        \Log::info('Emergency timeout processing triggered', [
            'timeout_count' => $timeoutCount,
            'triggered_by' => 'emergency_endpoint',
            'ip' => request()->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => "Emergency processing completed: {$timeoutCount} timeout requests processed",
            'timeout_count' => $timeoutCount,
            'timestamp' => now()->toDateTimeString()
        ]);
    } catch (\Exception $e) {
        \Log::error('Emergency timeout processing failed', [
            'error' => $e->getMessage(),
            'ip' => request()->ip()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Emergency processing failed: ' . $e->getMessage()
        ], 500);
    }
})->name('emergency.process.timeouts');

// =================================
// TEMPORARY: Cache Clear Route
// TODO: Remove after deployment issue resolved
// =================================
Route::get('/clear-cache-temp-fix-20251110', function() {
    try {
        \Artisan::call('view:clear');
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('route:clear');

        return response()->json([
            'success' => true,
            'message' => 'All caches cleared successfully!',
            'cleared' => [
                'view_cache' => '✅',
                'application_cache' => '✅',
                'config_cache' => '✅',
                'route_cache' => '✅'
            ],
            'timestamp' => now()->toDateTimeString()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Cache clear failed: ' . $e->getMessage()
        ], 500);
    }
});