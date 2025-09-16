<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('Home/signin.css') }}?v={{ time() }}&bust={{ microtime() }}">
</head>
<body>
    <div class="homepage-main-content">
        <div class="homepage-content-wrapper">
            @include('Partials.frontheader')
            <h3 class="homepage-tagline">Sign In</h3>
            <h3 class="homepage-description">Please enter your credential</h3>
            
            @if(session('error'))
                <div class="auth-error-block">{{ session('error') }}</div>
            @endif
            
            @if(session('success'))
                <div class="auth-success-block">{{ session('success') }}</div>
            @endif

            @if($errors->has('signin_error'))
                <div class="auth-error-block">{{ $errors->first('signin_error') }}</div>
            @endif

            @if($errors->has('csrf_error'))
                <div class="auth-error-block">{{ $errors->first('csrf_error') }}</div>
            @endif
            
            <div class="auth-form-container">
                <form action="{{ route('auth.signin') }}" method="POST" class="auth-form">
                    @csrf
                    
                    <div class="auth-form-group">
                        <label for="email">Email Address<span class="auth-required">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="auth-error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="auth-form-group">
                        <label for="password">Password<span class="auth-required">*</span></label>
                        <div class="auth-password-container">
                            <input type="password" id="password" name="password" required class="auth-password-input">
                            <button type="button" class="auth-password-toggle" onclick="toggleAuthPassword('password')">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <div class="auth-error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="auth-btn auth-btn-primary">Sign In</button>
                    
                    <div class="auth-forgot-link">
                        <a href="{{ route('auth.forgot.page') }}">Forgot Password?</a>
                    </div>
                </form>
                
                <div class="auth-switch-link">
                    Don't have an account? <a href="{{ route('auth.signup.page') }}">Sign Up</a>
                </div>
            </div>
            
            @include('Partials.frontfooter')
        </div>
    </div>

    <script src="{{ asset('Home/signin.js') }}"></script>
</body>
</html>