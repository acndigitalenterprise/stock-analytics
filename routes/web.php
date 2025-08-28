<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockAnalyticsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingController;

// ✅ Homepage route
Route::get('/', function () {
    // Check if user is logged in
    if (session()->has('user')) {
        return redirect()->route('stock-analytics.admin');
    }
    return view('Homepage.homepage');
})->name('home');

// ✅ Separate Sign Up and Sign In pages
Route::get('/sign-up', function () {
    // Check if user is logged in
    if (session()->has('user')) {
        return redirect()->route('stock-analytics.admin');
    }
    return view('Auth.signup');
})->name('signup.page');

Route::get('/sign-in', function () {
    // Check if user is logged in
    if (session()->has('user')) {
        return redirect()->route('stock-analytics.admin');
    }
    return view('Auth.signin');
})->name('signin.page');

Route::get('/forgot-password', function () {
    // Check if user is logged in
    if (session()->has('user')) {
        return redirect()->route('stock-analytics.admin');
    }
    return view('Auth.forgot-password');
})->name('forgot-password.page');

Route::get('/email-verified', function () {
    return view('Auth.verify-email');
})->name('email-verified.page');

// Public Stock Analytics page - now shows welcome page
Route::get('/stock-analytics', function () {
    // Check if user is logged in
    if (session()->has('user')) {
        return redirect()->route('stock-analytics.admin');
    }
    return view('Homepage.homepage');
})->name('stock-analytics.index');
Route::post('/stock-analytics', [StockAnalyticsController::class, 'submit'])
    ->middleware('rate_limit:stock_submit')
    ->name('stock-analytics.submit');

// Stock search API with rate limiting
Route::get('/api/stocks/search', [StockAnalyticsController::class, 'searchStocks'])
    ->middleware('rate_limit:stock_search')
    ->name('api.stocks.search');

// Auth popups with rate limiting
Route::post('/stock-analytics/signin', [AuthController::class, 'signin'])
    ->middleware('rate_limit:auth')
    ->name('stock-analytics.signin');
Route::post('/stock-analytics/signup', [AuthController::class, 'signup'])
    ->middleware('rate_limit:auth')
    ->name('stock-analytics.signup');
Route::post('/stock-analytics/register', [AuthController::class, 'signup'])
    ->middleware('rate_limit:auth')
    ->name('stock-analytics.register');
Route::post('/stock-analytics/logout', [AuthController::class, 'logout'])->name('stock-analytics.logout');
Route::get('/stock-analytics/forgot-password', [AuthController::class, 'showForgotPassword'])
    ->name('stock-analytics.forgot-password.show');
Route::post('/stock-analytics/forgot-password', [AuthController::class, 'forgotPassword'])
    ->middleware('rate_limit:auth')
    ->name('stock-analytics.forgot-password');

// Email verification route
Route::get('/stock-analytics/verify-email/{token}', [AuthController::class, 'verifyEmail'])
    ->name('stock-analytics.verify-email');

// Reset password routes without CSRF protection
Route::group(['middleware' => []], function () {
    Route::get('/stock-analytics/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('stock-analytics.reset-password');
    Route::get('/stock-analytics/reset-password', [AuthController::class, 'resetPassword'])->name('stock-analytics.reset-password.post');
});

// Admin panel (protected)
Route::middleware('auth.session')->group(function () {
    // Legacy admin route redirect to stocks
    Route::get('/stock-analytics/admin', [AdminController::class, 'index'])->name('stock-analytics.admin');
    
    // New admin menu routes
    Route::get('/stock-analytics/admin/dashboard', [AdminController::class, 'dashboard'])->name('stock-analytics.admin.dashboard');
    Route::get('/stock-analytics/admin/requests', [AdminController::class, 'stocks'])->name('stock-analytics.admin.requests');
    Route::get('/stock-analytics/admin/stocks', [AdminController::class, 'stocks'])->name('stock-analytics.admin.stocks'); // Keep for backward compatibility
    Route::get('/stock-analytics/admin/users', [AdminController::class, 'users'])->name('stock-analytics.admin.users');
    Route::get('/stock-analytics/admin/users/{id}/detail', [AdminController::class, 'userDetail'])->name('stock-analytics.admin.users.detail');
    Route::post('/stock-analytics/admin/users', [AdminController::class, 'createUser'])->name('stock-analytics.admin.users.create');
    Route::post('/stock-analytics/admin/users/{id}/update', [AdminController::class, 'updateUser'])->name('stock-analytics.admin.users.update');
    Route::post('/stock-analytics/admin/users/{id}/delete', [AdminController::class, 'deleteUser'])->name('stock-analytics.admin.users.delete');
    Route::post('/stock-analytics/admin/users/{id}/verify', [AdminController::class, 'verifyUser'])->name('stock-analytics.admin.users.verify');
    
    // Existing stock routes (keep for backward compatibility)
    Route::post('/stock-analytics/admin/request', [AdminController::class, 'store'])->name('stock-analytics.admin.request');
    Route::get('/stock-analytics/admin/edit/{id}', [AdminController::class, 'edit'])->name('stock-analytics.admin.edit');
    Route::post('/stock-analytics/admin/update/{id}', [AdminController::class, 'update'])->name('stock-analytics.admin.update');
    Route::post('/stock-analytics/admin/delete/{id}', [AdminController::class, 'delete'])->name('stock-analytics.admin.delete');
    // Detail request (admin) - update title in view
    Route::get('/stock-analytics/admin/request/{id}/detail', [App\Http\Controllers\AdminController::class, 'detail'])->name('stock-analytics.admin.detail');
    
    // Setting menu (independent)
    Route::get('/stock-analytics/setting', [SettingController::class, 'profile'])->name('stock-analytics.setting');
    Route::post('/stock-analytics/setting', [SettingController::class, 'updateProfile'])->name('stock-analytics.setting.update');
    
    // Get advice
    Route::get('/stock-analytics/admin/request/{id}/advice', [AdminController::class, 'getAdvice'])->name('stock-analytics.admin.advice');
});

Route::get('/stock-analytics/confirmation', function() {
    return view('confirmation');
})->name('stock-analytics.confirmation');

// Registration success page
Route::get('/stock-analytics/registration-success', function() {
    return view('registration-success');
})->name('stock-analytics.registration-success');

// Privacy Policy and Disclaimer pages
Route::get('/privacy-policy', function() {
    return view('privacy-policy');
})->name('privacy-policy');

Route::get('/disclaimer', function() {
    return view('disclaimer');
})->name('disclaimer');

// Route to view sent emails (for testing)
Route::get('/stock-analytics/view-emails', function() {
    $emails = Mail::getSymfonyTransport()->messages();
    return view('emails.view-sent', compact('emails'));
})->name('stock-analytics.view-emails');