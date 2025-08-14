@extends('layout')

@section('content')
<div class="container">
    <div class="reset-password-form">
        <h2>Reset Your Password</h2>
        
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('stock-analytics.reset-password.post') }}" method="GET">
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="form-group">
                <label for="password">New Password</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" required minlength="8" onkeyup="checkPasswordStrength(this.value)">
                    <button type="button" onclick="togglePassword('password')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #666;">👁</button>
                </div>
                <div id="password-strength" style="margin-top: 5px; font-size: 12px;"></div>
            </div>
            
            <div class="form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <div style="position: relative;">
                    <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8" onkeyup="checkPasswordMatch()">
                    <button type="button" onclick="togglePassword('password_confirmation')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #666;">👁</button>
                </div>
                <div id="password-match" style="margin-top: 5px; font-size: 12px;"></div>
            </div>
            
            <button type="submit" class="btn" id="resetBtn" onclick="return validateResetForm()">Reset Password</button>
        </form>
        
        <div class="back-link">
            <a href="{{ route('stock-analytics.index') }}">Back to Stock Analytics</a>
        </div>
    </div>
</div>

<script>
function checkPasswordStrength(password) {
    const strengthDiv = document.getElementById('password-strength');
    let strength = 0;
    let message = '';
    let color = '';
    
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    
    switch(strength) {
        case 0:
        case 1:
            message = 'Very Weak';
            color = '#ff0000';
            break;
        case 2:
            message = 'Weak';
            color = '#ff6600';
            break;
        case 3:
            message = 'Medium';
            color = '#ffcc00';
            break;
        case 4:
            message = 'Strong';
            color = '#99cc00';
            break;
        case 5:
            message = 'Very Strong';
            color = '#009900';
            break;
    }
    
    strengthDiv.innerHTML = `<span style="color: ${color};">${message}</span>`;
}

// Using common togglePassword function from app.js

function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    const matchDiv = document.getElementById('password-match');
    
    if (confirmPassword === '') {
        matchDiv.innerHTML = '';
        return;
    }
    
    if (password === confirmPassword) {
        matchDiv.innerHTML = '<span style="color: #009900;">✓ Passwords match</span>';
    } else {
        matchDiv.innerHTML = '<span style="color: #ff0000;">✗ Passwords do not match</span>';
    }
}

function validateResetForm() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    
    if (password.length < 8) {
        alert('Password must be at least 8 characters long');
        return false;
    }
    
    if (password !== confirmPassword) {
        alert('Passwords do not match');
        return false;
    }
    
    return true;
}

// Handle form submission loading state
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const resetBtn = document.getElementById('resetBtn');
    
    if (form && resetBtn) {
        form.addEventListener('submit', function() {
            resetBtn.innerHTML = '<span class="spinner"></span>Resetting...';
            resetBtn.disabled = true;
            
            // Show loading message
            const loadingMsg = document.createElement('div');
            loadingMsg.className = 'success-message';
            loadingMsg.textContent = 'Resetting your password...';
            form.parentNode.insertBefore(loadingMsg, form);
        });
    }
});
</script>

<style>
.container {
    max-width: 500px;
    margin: 50px auto;
    padding: 20px;
}

.reset-password-form {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.reset-password-form h2 {
    text-align: center;
    margin-bottom: 30px;
    color: #333;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #333;
}

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
}

/* Using consolidated button styles from layout.blade.php */

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.alert-danger {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.back-link {
    text-align: center;
    margin-top: 20px;
}

.back-link a {
    color: #007bff;
    text-decoration: none;
}

.back-link a:hover {
    text-decoration: underline;
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

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn:disabled:hover {
    background-color: #007bff;
}
</style>
@endsection 