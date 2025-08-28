@extends('layout')

@section('body-class', 'admin-layout')

@section('content')
<link rel="stylesheet" href="{{ asset('Admin/admin.css') }}">
<link rel="stylesheet" href="{{ asset('Users/users.css') }}?v={{ time() }}">

<div class="admin-content-container">
    @php $isAdminLayout = true; @endphp
    
    <div class="users-flex-between">
        <div>
            <h2>Setting</h2>
        </div>
        <div></div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="auth-success-block">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="auth-error-block">{{ session('error') }}</div>
    @endif
    
    <!-- Settings Form with Sign Up box styling -->
    <div class="auth-form-container" style="max-width: 500px; margin: 32px auto 0;">
        <div class="auth-info-note">
            <strong>Profile</strong><br>
            Keep your profile update.
        </div>
        
        <form action="{{ route('stock-analytics.setting.update') }}" method="POST" class="auth-form">
            @csrf
            
            <div class="auth-form-group">
                <label for="full_name">Full Name<span class="auth-required">*</span></label>
                <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $user->name) }}" required>
                @error('full_name')
                    <div class="auth-error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="auth-form-group">
                <label for="email">Email Address<span class="auth-required">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="auth-error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="auth-form-group">
                <label for="mobile_number">Mobile Number</label>
                <input type="text" name="mobile_number" id="mobile_number" value="{{ old('mobile_number', $user->mobile_number) }}" placeholder="Optional">
                @error('mobile_number')
                    <div class="auth-error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="auth-info-note" style="margin-top: 32px;">
                <strong>Security</strong><br>
                Keep your account secure.
            </div>
            
            <div class="auth-form-group">
                <label for="new_password">Change Password</label>
                <div class="auth-password-container">
                    <input type="password" name="new_password" id="new_password" placeholder="Enter new password" class="auth-password-input">
                    <span class="auth-toggle-password" onclick="togglePassword('new_password', this)">
                        <svg id="icon-new_password" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </span>
                </div>
                @error('new_password')
                    <div class="auth-error-message">{{ $message }}</div>
                @enderror
                <small style="color: rgba(255, 255, 255, 0.6); font-size: 0.85rem; margin-top: 6px; display: block;">
                    Leave blank if you don't want to change your password
                </small>
            </div>
            
            <button type="submit" class="auth-btn auth-btn-primary">Update</button>
        </form>
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
@endsection

