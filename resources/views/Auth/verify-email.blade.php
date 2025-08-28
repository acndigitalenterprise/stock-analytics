<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Email Verification - AI Insights</title>
    <link rel="stylesheet" href="{{ asset('Homepage/homepage.css') }}">
</head>
<body>
    <div class="homepage-main-content">
        <div class="homepage-content-wrapper">
            <h1 class="homepage-title">AI Insights</h1>
            <h3 class="homepage-tagline">Email Verification</h3>
            <h3 class="homepage-description">Please enter your credential</h3>
            
            <!-- Success/Error Messages -->
            @if(session('error'))
                <div class="auth-error-block">{{ session('error') }}</div>
            @endif
            
            @if(session('success'))
                <div class="auth-success-block">{{ session('success') }}</div>
            @endif
            
            <!-- Email Verification Status -->
            <div class="auth-form-container">
                <div class="auth-success-icon">
                    <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                
                <div class="auth-info-note">
                    <strong>Email Successfully Verified!</strong><br>
                    Your email address has been verified. You can now sign in to your account and start using AI Insights.
                </div>
                
                <div class="auth-buttons-group">
                    <a href="http://ai-stock-analytics.local/sign-in" class="auth-btn auth-btn-primary">Sign In</a>
                </div>
                
                <div class="auth-switch-link">
                    Back to <a href="http://ai-stock-analytics.local/">Homepage</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>