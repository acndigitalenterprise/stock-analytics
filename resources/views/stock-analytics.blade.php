@extends('layout')

@section('head')
<style>
/* Using consolidated CSS from layout.blade.php */

.forgot-password {
    text-align: center;
    margin-top: 15px;
}

.forgot-password a {
    color: #007bff;
    text-decoration: none;
    font-size: 14px;
}

.forgot-password a:hover {
    text-decoration: underline;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn:disabled:hover {
    background-color: #007bff;
}

.success-message {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
    padding: 12px;
    border-radius: 4px;
    margin-bottom: 16px;
    text-align: center;
    transition: opacity 0.5s ease;
}

.error-message {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
    padding: 12px;
    border-radius: 4px;
    margin-bottom: 16px;
    text-align: center;
    transition: opacity 0.5s ease;
}

.spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s ease-in-out infinite;
    margin-right: 8px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

@media (max-width: 600px) {
  .header-auth-desktop { display: none !important; }
  .header-auth-mobile { display: flex !important; flex-direction: row !important; gap: 12px !important; justify-content: center !important; max-width: 300px !important; margin: 18px auto 24px auto !important; }
  .header-auth-mobile .btn, .header-auth-mobile .btn.secondary { width: auto !important; min-width: 90px; }
  .stock-analytics-form-container { padding: 0 16px !important; }
}
@media (min-width: 601px) {
  .header-auth-desktop { display: flex !important; }
  .header-auth-mobile { display: none !important; }
  .stock-analytics-form-container { padding: 0 32px !important; }
}
</style>
@endsection

@section('header-right')
    <div class="header-auth-desktop">
        @if(session('user'))
            <form action="{{ route('stock-analytics.logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn secondary">Logout</button>
            </form>
        @else
            <button class="btn" onclick="showPopup('signin')">Sign In</button>
            <button class="btn secondary" onclick="showPopup('signup')">Sign Up</button>
            <a href="{{ route('stock-analytics.forgot-password.show') }}" style="color: #007bff; text-decoration: none; font-size: 0.9rem; margin-left: 16px; font-weight: 500;">Forgot Password?</a>
        @endif
    </div>
@endsection

@section('after-header')
    <div class="header-auth-mobile" style="margin: 18px auto 24px auto; display: none; gap: 12px; flex-wrap: wrap; justify-content: center; max-width: 300px;">
        @if(session('user'))
            <form action="{{ route('stock-analytics.logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn secondary">Logout</button>
            </form>
        @else
            <button class="btn" onclick="showPopup('signin')">Sign In</button>
            <button class="btn secondary" onclick="showPopup('signup')">Sign Up</button>
            <a href="{{ route('stock-analytics.forgot-password.show') }}" style="color: #007bff; text-decoration: none; font-size: 0.9rem; font-weight: 500; margin-top: 8px;">Forgot Password?</a>
        @endif
    </div>
@endsection

@push('head')
<style>
    /* Hapus .top-right dan styling terkait button Sign In pojok kanan atas */
</style>
@endpush

@section('content')
<h1 style="text-align:center;margin-bottom:16px;">📊 Stock Analytics Platform</h1>
<div style="text-align:center;margin-bottom:32px;padding:16px;background-color:#e7f3ff;border:1px solid #b3d9ff;border-radius:8px;">
    <p style="margin:0;color:#0056b3;font-size:14px;">
        <strong>🤖 AI-Powered Analysis:</strong> GPT-4 + Technical Indicators | 
        <strong>📈 Real-time Monitoring:</strong> Win/Loss tracking for {{ config('services.openai.model') === 'gpt-4' ? 'AI' : 'hybrid' }} recommendations
    </p>
</div>
<div style="padding: 0 32px;">
    @if(session('success'))
        <div id="confirmation-popup" style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.35);z-index:2000;display:flex;align-items:center;justify-content:center;">
            <div style="background:#fff;padding:36px 32px 28px 32px;border-radius:12px;box-shadow:0 2px 16px rgba(0,0,0,0.13);max-width:350px;text-align:center;">
                <div style="font-size:1.2rem;margin-bottom:18px;">AI will be sending suggestions soon.<br>Make sure to check your email for your login details.</div>
                <button class="btn" style="background:#111;color:#fff;width:100%;" onclick="document.getElementById('confirmation-popup').style.display='none'">Close</button>
            </div>
        </div>
    @endif
    @if(session('success'))
        <div class="success-message">AI will be sending suggestions soon. Make sure to check your email for your login details.</div>
    @endif
    @if(session('error'))
        <div class="error-message">{{ session('error') }}</div>
    @endif
    <form action="{{ route('stock-analytics.submit') }}" method="POST" id="requestForm" autocomplete="off">
        @csrf
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}">
            @error('full_name')<div class="error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label for="mobile_number">Mobile Number</label>
            <input type="text" name="mobile_number" id="mobile_number" value="{{ old('mobile_number') }}">
            @error('mobile_number')<div class="error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}">
            @error('email')<div class="error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group" style="position:relative;">
            <label for="stock_code">Stock Code</label>
            <input type="text" name="stock_code" id="stock_code" value="{{ old('stock_code') }}" 
                   oninput="handleStockSearch('stock_code', 'autocomplete-list')" 
                   onkeydown="handleKeyDown(event, 'autocomplete-list', 'stock_code')"
                   autocomplete="off"
                   placeholder="Type to search stocks (e.g., BBCA, TLKM, BBRI)">
            <div id="autocomplete-list" style="position:absolute;z-index:10;background:#fff;border:1px solid #ccc;width:100%;max-height:300px;overflow-y:auto;display:none;box-shadow:0 2px 8px rgba(0,0,0,0.1);"></div>
            @error('stock_code')<div class="error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
                    <label for="timeframe">Timeframe</label>
        <select name="timeframe" id="timeframe">
            <option value="1h" {{ old('timeframe')=='1h'?'selected':'' }}>1h</option>
            <option value="1d" {{ old('timeframe')=='1d'?'selected':'' }}>1d</option>
        </select>
        @error('timeframe')<div class="error">{{ $message }}</div>@enderror
        </div>
        @php
            $currentHour = now()->setTimezone('Asia/Jakarta')->format('H');
            $isTradingHours = $currentHour >= 9 && $currentHour < 16;
            $currentTime = now()->setTimezone('Asia/Jakarta')->format('H:i');
        @endphp
        
        @if($isTradingHours)
            <button type="submit" class="btn">Submit</button>
        @else
            <button type="button" class="btn" disabled style="background-color:#ccc; cursor:not-allowed; opacity:0.6;" 
                    title="Trading hours: 09:00-16:00 WIB (Current: {{ $currentTime }} WIB)">
                Submit (Market Closed)
            </button>
        @endif
    </form>

    <!-- Sign In Popup -->
    <div class="popup" id="signin-popup" style="display:none;">
        <div class="popup-content" style="position:relative;">
            <button type="button" onclick="closePopup('signin')" style="position:absolute;top:10px;right:10px;background:transparent;border:none;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
            <h3>Sign In</h3>
            @if($errors->has('signin_error'))
                <div style="color: #dc3545; font-size: 14px; margin-top: 5px; margin-bottom: 15px;">{{ $errors->first('signin_error') }}</div>
            @endif
            @if(session('success'))
                <div class="success-message">{{ session('success') }}</div>
            @endif
            <form action="{{ route('stock-analytics.signin') }}" method="POST" id="signinForm">
                @csrf
                <div class="form-group">
                    <label for="signin_email">Email</label>
                    <input type="email" name="email" id="signin_email" required>
                </div>
                <div class="form-group" style="position:relative;">
                    <label for="signin_password">Password</label>
                    <input type="password" name="password" id="signin_password" required style="padding-right:36px;">
                    <span class="toggle-password" onclick="togglePassword('signin_password', this)" style="position:absolute;top:50%;right:10px;transform:translateY(-50%);cursor:pointer;">
                        <svg id="icon-signin_password" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </span>
                </div>
                <button type="submit" class="btn" id="signinBtn">Sign In</button>
                <button type="button" class="btn secondary" onclick="closePopup('signin')">Cancel</button>
                <div class="forgot-password" style="text-align: center; margin-top: 16px; padding: 8px;">
                    <a href="{{ route('stock-analytics.forgot-password.show') }}" style="color: #007bff; text-decoration: underline; font-size: 0.9rem; font-weight: 500;">Forgot Password?</a>
                </div>
            </form>
        </div>
    </div>
    <!-- Sign Up Popup -->
    <div class="popup" id="signup-popup" style="display:none;">
        <div class="popup-content">
            <h3>Sign Up</h3>
            <form action="{{ route('stock-analytics.signup') }}" method="POST" id="signupForm">
                @csrf
                <div class="form-group">
                    <label for="signup_full_name">Full Name</label>
                    <input type="text" name="full_name" id="signup_full_name" value="{{ old('full_name') }}" required>
                </div>
                <div class="form-group">
                    <label for="signup_mobile_number">Mobile Number</label>
                    <input type="text" name="mobile_number" id="signup_mobile_number" value="{{ old('mobile_number') }}" required>
                    @error('mobile_number')<div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="signup_email">Email Address</label>
                    <input type="email" name="email" id="signup_email" value="{{ old('email') }}" required>
                    @error('email')<div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group" style="position:relative;">
                    <label for="signup_password">Password</label>
                    <input type="password" name="password" id="signup_password" required style="padding-right:36px;">
                    <span class="toggle-password" onclick="togglePassword('signup_password', this)" style="position:absolute;top:50%;right:10px;transform:translateY(-50%);cursor:pointer;">
                        <svg id="icon-signup_password" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </span>
                </div>
                <button type="submit" class="btn">Sign Up</button>
                <button type="button" class="btn secondary" onclick="closePopup('signup')">Cancel</button>
            </form>
        </div>
    </div>
    
    <!-- Forgot Password Popup -->
    <div class="popup" id="forgot-password-popup" style="display:none;">
        <div class="popup-content">
            <h3>Forgot Password</h3>
            @if(session('error'))
                <div class="error-message">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="success-message">{{ session('success') }}</div>
            @endif
            <form action="{{ route('stock-analytics.forgot-password') }}" method="POST" id="forgotPasswordForm">
                @csrf
                <div class="form-group">
                    <label for="forgot_email">Email Address</label>
                    <input type="email" name="email" id="forgot_email" required onblur="validateEmail(this)">
                    <div id="email-error" style="color:red;font-size:12px;display:none;"></div>
                </div>
                <button type="submit" class="btn" id="forgotPasswordBtn">Send Reset Link</button>
                <button type="button" class="btn secondary" onclick="closePopup('forgot-password')">Cancel</button>
            </form>
        </div>
    </div>
</div>
<footer style="text-align:center; margin-top:48px; color:#888; font-size:14px;">
    <button class="btn" style="background:#111;color:#fff;margin-bottom:8px;" onclick="showPopup('signin')">Sign In</button><br>
    <a href="{{ route('stock-analytics.forgot-password.show') }}" style="color: #007bff; text-decoration: none; font-size: 14px; font-weight: 500; margin-bottom: 16px; display: inline-block;">Forgot Password?</a><br>
    © 2025 Anom Brams. All Rights Reserved.
</footer>
@endsection

@section('scripts')
<script>
// Using common functions from app.js

function showForgotPassword() {
    closePopup('signin');
    document.getElementById('forgot-password-popup').style.display = 'flex';
}

    // Auto-close popup after success message
    document.addEventListener('DOMContentLoaded', function() {
        const successMessages = document.querySelectorAll('.success-message');
        successMessages.forEach(function(message) {
            if (message.textContent.includes('Password reset link has been sent')) {
                setTimeout(function() {
                    closePopup('forgot-password');
                    // Clear form
                    document.getElementById('forgot_email').value = '';
                    document.getElementById('email-error').style.display = 'none';
                    const btn = document.getElementById('forgotPasswordBtn');
                    btn.innerHTML = 'Send Reset Link';
                    btn.disabled = false;
                }, 3000);
            }
        });
        
        // Auto-hide success/error messages after 5 seconds
        const messages = document.querySelectorAll('.success-message, .error-message');
        messages.forEach(function(message) {
            setTimeout(function() {
                message.style.opacity = '0';
                setTimeout(function() {
                    message.style.display = 'none';
                }, 500);
            }, 5000);
        });
    
    // Handle forgot password form submission
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', function(e) {
            const email = document.getElementById('forgot_email');
            if (!validateEmail(email)) {
                e.preventDefault();
                return false;
            }
            const btn = document.getElementById('forgotPasswordBtn');
            btn.innerHTML = '<span class="spinner"></span>Sending...';
            btn.disabled = true;
            
            // Show loading message
            const loadingMsg = document.createElement('div');
            loadingMsg.className = 'success-message';
            loadingMsg.textContent = 'Sending reset link...';
            forgotPasswordForm.parentNode.insertBefore(loadingMsg, forgotPasswordForm);
        });
    }
    
    // Handle signin form submission
    const signinForm = document.getElementById('signinForm');
    if (signinForm) {
        signinForm.addEventListener('submit', function() {
            const btn = document.getElementById('signinBtn');
            btn.innerHTML = '<span class="spinner"></span>Signing In...';
            btn.disabled = true;
        });
    }
});

function validateEmail(input) {
    const email = input.value;
    const emailError = document.getElementById('email-error');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!email) {
        emailError.textContent = 'Email is required';
        emailError.style.display = 'block';
        return false;
    } else if (!emailRegex.test(email)) {
        emailError.textContent = 'Please enter a valid email address';
        emailError.style.display = 'block';
        return false;
    } else {
        emailError.style.display = 'none';
        return true;
    }
}

// Using common stock search and keyboard navigation functions from app.js
// These functions are called directly from the HTML input elements

// Hide autocomplete when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#stock_code') && !e.target.closest('#autocomplete-list')) {
        hideAutocomplete('autocomplete-list');
    }
});

// Basic frontend validation
function validateForm() {
    let valid = true;
    const fields = ['full_name','mobile_number','email','stock_code','timeframe'];
    fields.forEach(f => {
        const el = document.getElementById(f);
        if (el && !el.value) {
            el.style.borderColor = '#b00';
            valid = false;
        } else if (el) {
            el.style.borderColor = '#ccc';
        }
    });
    return valid;
}

// Using common togglePassword function from app.js

// Auto-open popup if there are validation errors
document.addEventListener('DOMContentLoaded', function() {
    @if($errors->has('signin_error'))
        showPopup('signin');
    @endif
    
    @if($errors->has('email') || $errors->has('mobile_number'))
        showPopup('signup');
    @endif
});
</script>
@endsection 