<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - AI Insights</title>
    <link rel="stylesheet" href="{{ asset('Homepage/homepage.css') }}">
</head>
<body>
    <div class="homepage-main-content">
        <div class="homepage-content-wrapper">
            <h1 class="homepage-title">AI Insights</h1>
            <h3 class="homepage-tagline">Forgot Password</h3>
            <h3 class="homepage-description">Please enter your credential</h3>
            
            <!-- Error Messages -->
            @if(session('error'))
                <div class="auth-error-block">{{ session('error') }}</div>
            @endif
            
            @if(session('success'))
                <div class="auth-success-block">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="auth-error-block">
                    @foreach($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif
            
            <!-- Forgot Password Form -->
            <div class="auth-form-container">
                <div class="auth-info-note">
                    <strong>Reset Your Password</strong><br>
                    Enter your email address and we'll send you a link to reset your password.
                </div>
                
                <form action="{{ route('stock-analytics.forgot-password') }}" method="POST" class="auth-form">
                    @csrf
                    
                    <div class="auth-form-group">
                        <label for="email">Email Address<span class="auth-required">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="auth-error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="auth-btn auth-btn-primary">Reset</button>
                </form>
                
                <div class="auth-switch-link">
                    Remember your password? <a href="http://ai-stock-analytics.local/sign-in">Sign In</a>
                </div>
                
                <div class="auth-switch-link" style="margin-top: 12px;">
                    Don't have an account? <a href="http://ai-stock-analytics.local/sign-up">Sign Up</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>