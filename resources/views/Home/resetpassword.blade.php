<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('Home/resetpassword.css') }}?v={{ time() }}">
</head>
<body>
    <div class="homepage-main-content">
        <div class="homepage-content-wrapper">
            @include('Partials.frontheader')
            <h3 class="homepage-tagline">Reset Password</h3>
            <h3 class="homepage-description">Please enter your credential</h3>
            
            @if(session('error'))
                <div class="auth-error-block">{{ session('error') }}</div>
            @endif
            
            @if(session('success'))
                <div class="auth-success-block">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="auth-error-block">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif
            
            <div class="auth-form-container">
                <div class="auth-info-note">
                    Please enter your new password. Make sure it's secure and at least 8 characters long.
                </div>
                
                <form action="{{ route('auth.reset', $token) }}" method="POST" class="auth-form">
                    <input type="hidden" name="token" value="{{ $token }}">
                    
                    <div class="auth-form-group">
                        <label for="password">New Password<span class="auth-required">*</span></label>
                        <div class="auth-password-container">
                            <input type="password" id="password" name="password" required class="auth-password-input" minlength="8">
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
                        <label for="password_confirmation">Confirm New Password<span class="auth-required">*</span></label>
                        <div class="auth-password-container">
                            <input type="password" id="password_confirmation" name="password_confirmation" required class="auth-password-input" minlength="8">
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
                    
                    <button type="submit" class="auth-btn auth-btn-primary">Update</button>
                </form>
                
                <div class="auth-switch-link">
                    Remember your password? <a href="{{ route('auth.signin.page') }}">Sign In</a>
                </div>
            </div>
            
            @include('Partials.frontfooter')
        </div>
    </div>

    <script src="{{ asset('Home/signin.js') }}"></script>
</body>
</html>