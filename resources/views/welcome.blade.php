@extends('layout')

@section('content')
    <div class="main-content">
        <h2 class="page-title">Welcome to AI Stock Analytics</h2>
        <p class="page-subtitle">Your AI partner for stock market analysis</p>
        
        @if(session('error'))
            <div class="error-message">{{ session('error') }}</div>
        @endif
        
        @if(session('success'))
            <div class="success-message">{{ session('success') }}</div>
        @endif
        
        <!-- Auth Container with Tabs -->
        <div class="auth-container">
            <!-- Tab Navigation -->
            <div class="tab-navigation">
                <button class="tab-btn active" onclick="switchTab('signin')" id="signin-tab">Sign In</button>
                <button class="tab-btn" onclick="switchTab('signup')" id="signup-tab">Sign Up</button>
            </div>
            
            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Sign In Form -->
                <div class="tab-panel active" id="signin-panel">
                    @if($errors->has('signin_error'))
                        <div class="error-message" style="margin-bottom: 16px;">{{ $errors->first('signin_error') }}</div>
                    @endif
                    @if($errors->has('csrf_error'))
                        <div class="error-message" style="margin-bottom: 16px;">{{ $errors->first('csrf_error') }}</div>
                    @endif
                    
                    <form action="{{ route('stock-analytics.signin') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="login_email">Email Address<span style="color: red;">*</span></label>
                            <input type="email" id="login_email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="login_password">Password<span style="color: red;">*</span></label>
                            <div style="position:relative;">
                                <input type="password" id="login_password" name="password" required style="padding-right:36px;">
                                <span class="toggle-password" onclick="togglePassword('login_password', this)" style="position:absolute;top:50%;right:10px;transform:translateY(-50%);cursor:pointer;">
                                    <svg id="icon-login_password" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Sign In</button>
                        
                        <div style="text-align: center; margin-top: 16px;">
                            <a href="{{ route('stock-analytics.forgot-password.show') }}" style="color: #007bff; text-decoration: none; font-size: 0.9rem; font-weight: 500;">Forgot Password?</a>
                        </div>
                    </form>
                </div>
                
                <!-- Sign Up Form -->
                <div class="tab-panel" id="signup-panel">
                    @if($errors->has('signup_error'))
                        <div class="error-message" style="margin-bottom: 16px;">{{ $errors->first('signup_error') }}</div>
                    @endif
                    @if($errors->has('csrf_error'))
                        <div class="error-message" style="margin-bottom: 16px;">{{ $errors->first('csrf_error') }}</div>
                    @endif
                    
                    <form action="{{ route('stock-analytics.register') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="full_name">Full Name<span style="color: red;">*</span></label>
                            <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                            @error('full_name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address<span style="color: red;">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password<span style="color: red;">*</span></label>
                            <div style="position:relative;">
                                <input type="password" id="password" name="password" required style="padding-right:36px;">
                                <span class="toggle-password" onclick="togglePassword('password', this)" style="position:absolute;top:50%;right:10px;transform:translateY(-50%);cursor:pointer;">
                                    <svg id="icon-password" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                            </div>
                            @error('password')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password<span style="color: red;">*</span></label>
                            <div style="position:relative;">
                                <input type="password" id="password_confirmation" name="password_confirmation" required style="padding-right:36px;">
                                <span class="toggle-password" onclick="togglePassword('password_confirmation', this)" style="position:absolute;top:50%;right:10px;transform:translateY(-50%);cursor:pointer;">
                                    <svg id="icon-password_confirmation" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                            </div>
                            @error('password_confirmation')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="info-note">
                            <strong>Note:</strong> After registration, please check your email to verify your account before signing in.
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Sign Up</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('head')
<style>
    /* Main Content */
    .main-content {
        padding: 32px;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .page-title {
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
        text-align: center;
    }
    
    .page-subtitle {
        font-size: 1.1rem;
        color: #666;
        margin-bottom: 40px;
        text-align: center;
    }
    
    /* Auth Container */
    .auth-container {
        max-width: 500px;
        margin: 0 auto;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    /* Tab Navigation */
    .tab-navigation {
        display: flex;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    .tab-btn {
        flex: 1;
        padding: 16px 24px;
        background: none;
        border: none;
        font-size: 16px;
        font-weight: 500;
        color: #6c757d;
        cursor: pointer;
        transition: all 0.3s ease;
        border-bottom: 3px solid transparent;
    }
    
    .tab-btn:hover {
        background: #e9ecef;
        color: #495057;
    }
    
    .tab-btn.active {
        background: #fff;
        color: #007bff;
        border-bottom-color: #007bff;
    }
    
    /* Tab Content */
    .tab-content {
        padding: 32px;
    }
    
    .tab-panel {
        display: none;
    }
    
    .tab-panel.active {
        display: block;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #555;
    }
    
    .form-group input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        transition: border-color 0.3s ease;
        box-sizing: border-box;
    }
    
    .form-group input:focus {
        outline: none;
        border-color: #2196F3;
    }
    
    .error-message {
        color: #dc3545;
        font-size: 14px;
        margin-top: 5px;
    }
    
    .success-message {
        color: #28a745;
        font-size: 14px;
        margin-top: 5px;
    }
    
    .info-note {
        background: #e3f2fd;
        border: 1px solid #bbdefb;
        border-radius: 6px;
        padding: 16px;
        margin: 20px 0;
        font-size: 14px;
        color: #1976d2;
    }
    
    @media (max-width: 768px) {
        .main-content {
            padding: 20px;
        }
        
        .auth-container {
            margin: 0 16px;
        }
        
        .tab-content {
            padding: 24px;
        }
    }
</style>
@endsection

@section('scripts')
<script>
// Tab switching functionality
function switchTab(tabName) {
    // Hide all tab panels
    const panels = document.querySelectorAll('.tab-panel');
    panels.forEach(panel => panel.classList.remove('active'));
    
    // Remove active class from all tab buttons
    const tabs = document.querySelectorAll('.tab-btn');
    tabs.forEach(tab => tab.classList.remove('active'));
    
    // Show selected panel and activate corresponding tab
    document.getElementById(tabName + '-panel').classList.add('active');
    document.getElementById(tabName + '-tab').classList.add('active');
}

// Password toggle functionality
function togglePassword(inputId, toggleElement) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById('icon-' + inputId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464m1.414 1.414L19.071 19.07m-9.193-9.193l4.242 4.242"/>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}
</script>
@endsection
