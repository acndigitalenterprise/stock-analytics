<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign Up - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('Home/signup.css') }}?v={{ time() }}">
</head>
<body>
    <div class="homepage-main-content">
        <div class="homepage-content-wrapper">
            @include('Partials.frontheader')
            <h3 class="homepage-tagline">Sign Up</h3>
            <h3 class="homepage-description">Please enter your credential</h3>
            
            @if(session('error'))
                <div class="auth-error-block">{{ session('error') }}</div>
            @endif
            
            @if(session('success'))
                <div class="auth-success-block">{{ session('success') }}</div>
            @endif

            @if($errors->has('signup_error'))
                <div class="auth-error-block">{{ $errors->first('signup_error') }}</div>
            @endif
            
            <div class="auth-form-container">
                <form action="{{ route('auth.signup') }}" method="POST" class="auth-form">
                    @csrf
                    
                    <div class="auth-form-group">
                        <label for="full_name">Full Name<span class="auth-required">*</span></label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                        @error('full_name')
                            <div class="auth-error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="auth-form-group">
                        <label for="email">Email Address<span class="auth-required">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            @if($message !== 'Email Address Already Registered')
                                <div class="auth-error-message">{{ $message }}</div>
                            @endif
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
                    
                    <div class="auth-form-group">
                        <label for="password_confirmation">Confirm Password<span class="auth-required">*</span></label>
                        <div class="auth-password-container">
                            <input type="password" id="password_confirmation" name="password_confirmation" required class="auth-password-input">
                            <button type="button" class="auth-password-toggle" onclick="toggleAuthPassword('password_confirmation')">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <div class="auth-error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="auth-info-note">
                        Check your email to verify your account before signing in
                    </div>
                    
                    <div class="auth-form-group">
                        <label class="auth-checkbox-container">
                            <input type="checkbox" id="terms_agreement" name="terms_agreement" required>
                            I confirm that I have read, understood, and agree to the <a href="{{ route('disclaimer') }}" target="_blank">Disclaimer</a> and <a href="{{ route('privacy') }}" target="_blank">Privacy Policy</a>.
                        </label>
                    </div>
                    
                    <button type="submit" class="auth-btn auth-btn-primary" id="signup-btn">Sign Up</button>
                </form>
                
                <div class="auth-switch-link">
                    Already have an account? <a href="{{ route('auth.signin.page') }}">Sign In</a>
                </div>
            </div>
            
            @include('Partials.frontfooter')
        </div>
    </div>

    <script src="{{ asset('Home/signup.js') }}"></script>
</body>
</html>