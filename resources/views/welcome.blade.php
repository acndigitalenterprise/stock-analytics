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
        
        <div class="forms-container">
            <!-- Sign Up Section -->
            <div class="form-section">
                <h3 class="form-title">Sign Up</h3>
                
                @if($errors->has('csrf_error'))
                    <div style="color: #dc3545; font-size: 14px; margin-top: 5px; margin-bottom: 15px;">{{ $errors->first('csrf_error') }}</div>
                @endif
                
                <form action="{{ route('stock-analytics.register') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                        @error('full_name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="mobile_number">Mobile Number</label>
                        <input type="text" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" required>
                        @error('mobile_number')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="info-note">
                        <strong>Note:</strong> Your password will be automatically generated and sent to your email address.
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Sign Up</button>
                </form>
            </div>
            
            <!-- Sign In Section -->
            <div class="form-section">
                <h3 class="form-title">Sign In</h3>
                @if($errors->has('signin_error'))
                    <div style="color: #dc3545; font-size: 14px; margin-top: 5px; margin-bottom: 15px;">{{ $errors->first('signin_error') }}</div>
                @endif
                @if($errors->has('csrf_error'))
                    <div style="color: #dc3545; font-size: 14px; margin-top: 5px; margin-bottom: 15px;">{{ $errors->first('csrf_error') }}</div>
                @endif
                
                <form action="{{ route('stock-analytics.signin') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="login_email">Email Address</label>
                        <input type="email" id="login_email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="login_password">Password</label>
                        <div style="position:relative;">
                            <input type="password" id="login_password" name="password" required style="padding-right:36px;">
                            <span class="toggle-password" onclick="togglePassword('login_password', this)" style="position:absolute;top:50%;right:10px;transform:translateY(-50%);cursor:pointer;">
                                <svg id="icon-login_password" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Sign In</button>
                    
                    <div style="text-align: left; margin-top: 16px;">
                        <a href="{{ route('stock-analytics.forgot-password.show') }}" style="color: #007bff; text-decoration: none; font-size: 0.9rem; font-weight: 500;">Forgot Password?</a>
                    </div>
                </form>
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
    }
    
    .page-subtitle {
        font-size: 1.1rem;
        color: #666;
        margin-bottom: 40px;
    }
    
    /* Forms Container */
    .forms-container {
        display: flex;
        gap: 40px;
        margin-top: 40px;
    }
    
    .form-section {
        flex: 1;
    }
    
    .form-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 24px;
        color: #333;
        border-bottom: 2px solid #eee;
        padding-bottom: 8px;
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
    }
    
    .form-group input:focus {
        outline: none;
        border-color: #2196F3;
    }
    
            /* Using consolidated button styles from layout.blade.php */
    
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
        .forms-container {
            flex-direction: column;
        }
        
        .main-content {
            padding: 20px;
        }
    }
</style>
@endsection

@section('scripts')
<script>
// Using common togglePassword function from app.js
</script>
@endsection
