<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Email Verification - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('Home/emailverification.css') }}?v={{ time() }}">
</head>
<body>
    <div class="homepage-main-content">
        <div class="homepage-content-wrapper">
            @include('Partials.frontheader')
            <h3 class="homepage-tagline">Email Verification</h3>
            <h3 class="homepage-description">Email Successfully Verified!</h3>
            
            @if(session('error'))
                <div class="auth-error-block">{{ session('error') }}</div>
            @endif
            
            @if(session('success'))
                <div class="auth-success-block">{{ session('success') }}</div>
            @endif
            
            <div class="auth-form-container">
                <div class="auth-info-note">
                    Your email address has been verified. You can now sign in to your account and start using Ticker AI.
                </div>
                
                <a href="{{ route('auth.signin.page') }}" class="auth-btn auth-btn-primary">Sign In</a>

            </div>
            
            @include('Partials.frontfooter')
        </div>
    </div>

</body>
</html>