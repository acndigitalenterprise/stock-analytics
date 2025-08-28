<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Change Password - AI Insights</title>
    <link rel="stylesheet" href="{{ asset('Homepage/homepage.css') }}">
</head>
<body>
    <div class="homepage-main-content">
        <div class="homepage-content-wrapper">
            <h1 class="homepage-title">AI Insights</h1>
            <h3 class="homepage-tagline">Change Password</h3>
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
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif
            
            <!-- Change Password Form -->
            <div class="auth-form-container">
                <div class="auth-info-note">
                    <strong>Create New Password</strong><br>
                    Please enter your new password. Make sure it's secure and at least 8 characters long.
                </div>
                
                <form action="{{ route('stock-analytics.reset-password.post') }}" method="GET" class="auth-form">
                    <input type="hidden" name="token" value="{{ $token }}">
                    
                    <div class="auth-form-group">
                        <label for="password">New Password<span class="auth-required">*</span></label>
                        <div class="auth-password-container">
                            <input type="password" id="password" name="password" required class="auth-password-input" minlength="8">
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
                        <label for="password_confirmation">Confirm New Password<span class="auth-required">*</span></label>
                        <div class="auth-password-container">
                            <input type="password" id="password_confirmation" name="password_confirmation" required class="auth-password-input" minlength="8">
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
                    
                    <button type="submit" class="auth-btn auth-btn-primary">Update</button>
                </form>
                
                <div class="auth-switch-link">
                    Remember your password? <a href="http://ai-stock-analytics.local/sign-in">Sign In</a>
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
    </script>
</body>
</html>