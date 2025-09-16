<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign Up - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('Homepage/homepage.css') }}">
</head>
<body>
    <div class="homepage-main-content">
        <div class="homepage-content-wrapper">
            <h1 class="homepage-title">Ticker AI</h1>
            <h3 class="homepage-tagline">Sign Up</h3>
            <h3 class="homepage-description">Please enter your credential</h3>
            
            <!-- Error Messages -->
            @if(session('error'))
                <div class="auth-error-block">{{ session('error') }}</div>
            @endif
            
            @if(session('success'))
                <div class="auth-success-block">{{ session('success') }}</div>
            @endif

            @if($errors->has('signup_error'))
                <div class="auth-error-block">{{ $errors->first('signup_error') }}</div>
            @endif
            
            <!-- Sign Up Form -->
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
                            <span class="auth-toggle-password" onclick="togglePassword('password', this)">
                                <svg id="icon-password" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </span>
                        </div>
                        @error('password')
                            <div class="auth-error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="auth-form-group">
                        <label for="password_confirmation">Confirm Password<span class="auth-required">*</span></label>
                        <div class="auth-password-container">
                            <input type="password" id="password_confirmation" name="password_confirmation" required class="auth-password-input">
                            <span class="auth-toggle-password" onclick="togglePassword('password_confirmation', this)">
                                <svg id="icon-password_confirmation" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </span>
                        </div>
                        @error('password_confirmation')
                            <div class="auth-error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="auth-info-note">
                        <strong>Note:</strong> Check your email to verify your account before signing in
                    </div>
                    
                    <div class="auth-form-group">
                        <label class="auth-checkbox-container">
                            <input type="checkbox" id="terms_agreement" name="terms_agreement" required>
                            I confirm that I have read, understood, and agree to the <a href="{{ route('disclaimer') }}" target="_blank">Disclaimer</a> and <a href="{{ route('privacy-policy') }}" target="_blank">Privacy Policy</a>.
                        </label>
                    </div>
                    
                    <button type="submit" class="auth-btn auth-btn-primary" id="signup-btn">Sign Up</button>
                </form>
                
                <div class="auth-switch-link">
                    Already have an account? <a href="{{ route('auth.signin.page') }}">Sign In</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconElement) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById('icon-' + inputId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.05 8.05m0 0a5.971 5.971 0 00-.908 1.563M8.05 8.05l2.828 2.828m0 0A3.01 3.01 0 0010 12c0 .483.115.934.32 1.336M10.88 13.12l2.828 2.828M12.95 11.95l2.828-2.828m0 0a3.01 3.01 0 00.08-4.243m-2.828 2.828L12.95 11.95m0 0a5.971 5.971 0 011.563-.908m-1.563.908l2.828 2.828M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
            }
        }

        // Enable/disable signup button based on terms agreement
        document.getElementById('terms_agreement').addEventListener('change', function() {
            document.getElementById('signup-btn').disabled = !this.checked;
        });

        // Initially disable signup button
        document.getElementById('signup-btn').disabled = true;
    </script>
</body>
</html>