<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('Home/forgotpassword.css') }}?v={{ time() }}">
</head>
<body>
    <div class="homepage-main-content">
        <div class="homepage-content-wrapper">
            @include('Partials.frontheader')
            <h3 class="homepage-tagline">Forgot Password</h3>
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
                        {{ $error }}
                    @endforeach
                </div>
            @endif
            
            <div class="auth-form-container">
                <div class="auth-info-note">
                    Enter your email address and we'll send you a link to reset your password.
                </div>
                
                <form action="{{ route('auth.forgot') }}" method="POST" class="auth-form">
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
                    Remember your password? <a href="{{ route('auth.signin.page') }}">Sign In</a>
                </div>
                
                <div class="auth-switch-link" style="margin-top: 12px;">
                    Don't have an account? <a href="{{ route('auth.signup.page') }}">Sign Up</a>
                </div>
            </div>
            
            @include('Partials.frontfooter')
        </div>
    </div>

    <script src="{{ asset('Home/forgotpassword.js') }}"></script>
</body>
</html>