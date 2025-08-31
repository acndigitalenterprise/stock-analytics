@extends('layout')

@section('body-class', 'admin-layout')

@section('content')
<link rel="stylesheet" href="{{ asset('Admin/admin.css') }}">
<link rel="stylesheet" href="{{ asset('Users/users.css') }}?v={{ time() }}">

<!-- Inline tweaks to ensure the password-eye is visible and clickable on this page -->
<style>
    .auth-password-container { position: relative; display: block; }
    .auth-password-input { width: 100%; padding-right: 56px; box-sizing: border-box; }
    .auth-toggle-password {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255,255,255,0.95) !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: rgba(0,0,0,0.18) !important;
        border-radius: 8px;
        cursor: pointer;
        transition: background 120ms ease, color 120ms ease;
        z-index: 9999 !important;
        pointer-events: auto !important;
        border: 1px solid rgba(255,255,255,0.06) !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.25);
    }
    .auth-toggle-password:hover,
    .auth-toggle-password:focus {
        background: rgba(255,255,255,0.08);
        color: rgba(255,255,255,1);
        outline: none;
    }
    /* ensure svg is clickable through the span area */
    .auth-toggle-password svg { width: 20px; height: 20px; display: block; pointer-events: auto; }
</style>

<div class="admin-content-container">
    @php $isAdminLayout = true; @endphp
    
    <div class="users-flex-between">
        <div>
            <h2>Users &gt; Detail</h2>
        </div>
        <div></div>
    </div>

    @if(session('success'))
        <div class="users-message users-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="users-message users-error">{{ session('error') }}</div>
    @endif
    
    @if(session('password_error'))
        <div class="users-message users-error">{{ session('password_error') }}</div>
    @endif
    
    <!-- User Detail Form -->
    <div class="auth-form-container" style="max-width: 500px; margin: 32px auto 0;">
        <div class="auth-info-note">
            <strong>Profile</strong><br>
            Edit user profile.
        </div>
        
        <form action="{{ route('stock-analytics.admin.users.update', $targetUser->id) }}" method="POST" class="auth-form">
            @csrf
            
            <div class="auth-form-group">
                <label for="full_name">Full Name<span class="auth-required">*</span></label>
                <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $targetUser->name) }}" required>
                @error('full_name')
                    <div class="auth-error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="auth-form-group">
                <label for="email">Email Address<span class="auth-required">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $targetUser->email) }}" required>
                @error('email')
                    <div class="auth-error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="auth-form-group">
                <label for="mobile_number">Mobile Number</label>
                <input type="text" name="mobile_number" id="mobile_number" value="{{ old('mobile_number', $targetUser->mobile_number) }}" placeholder="Optional">
                @error('mobile_number')
                    <div class="auth-error-message">{{ $message }}</div>
                @enderror
            </div>

            @if(isset($user) && $user->role === 'super_admin')
            <div class="auth-form-group">
                <label for="user_role">Role</label>
                <select name="role" id="user_role">
                    <option value="user" {{ $targetUser->role == 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ $targetUser->role == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')
                    <div class="auth-error-message">{{ $message }}</div>
                @enderror
            </div>
            @endif
            
            <div class="auth-info-note" style="margin-top: 32px;">
                <strong>Security</strong><br>
                Change user password.
            </div>
            
            <div class="auth-form-group">
                <label for="password">Change Password</label>
                <div class="auth-password-container">
                    <input type="password" name="password" id="password" placeholder="Enter new password" class="auth-password-input">
                    <span class="auth-toggle-password" onclick="(function(e){console.log('toggle clicked', e); togglePassword('password', e.currentTarget||this) })(event)" role="button" tabindex="0" aria-label="Toggle password visibility">
                        <svg id="icon-password" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </span>
                </div>
                @error('password')
                    <div class="auth-error-message">{{ $message }}</div>
                @enderror
                <small style="color: rgba(255, 255, 255, 0.6); font-size: 0.85rem; margin-top: 6px; display: block;">
                    Leave blank if you don't want to change the password
                </small>
            </div>
            
            <div style="display: flex; gap: 16px; margin-top: 32px;">
                <button type="submit" class="auth-btn auth-btn-primary">Update</button>
                <a href="{{ route('stock-analytics.admin.users') }}" class="auth-btn" style="background: rgba(255, 255, 255, 0.1); text-decoration: none; text-align: center;">Back</a>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePassword(inputId, toggleSpan) {
        const input = document.getElementById(inputId);
        if (!input) return;

        // Prefer the svg inside the clicked span; fall back to icon-{id}
        let svg = null;
        if (toggleSpan && toggleSpan.querySelector) {
            svg = toggleSpan.querySelector('svg');
            // make sure it's visually obvious this is clickable
            try { toggleSpan.style.cursor = 'pointer'; } catch (e) {}
            // add keyboard accessibility
            if (!toggleSpan.hasAttribute('role')) toggleSpan.setAttribute('role', 'button');
            if (!toggleSpan.hasAttribute('tabindex')) toggleSpan.setAttribute('tabindex', '0');
            // allow Enter/Space to toggle when focused
            if (!toggleSpan._toggleKeyBound) {
                toggleSpan.addEventListener('keydown', function (ev) {
                    if (ev.key === 'Enter' || ev.key === ' ') {
                        ev.preventDefault();
                        togglePassword(inputId, toggleSpan);
                    }
                });
                toggleSpan._toggleKeyBound = true;
            }
        }

        if (!svg) svg = document.getElementById('icon-' + inputId);
        if (!svg) return;

        const eyePath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
        const eyeOffPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.05 8.05m0 0a5.971 5.971 0 00-.908 1.563M8.05 8.05l2.828 2.828m0 0A3.01 3.01 0 0010 12c0 .483.115.934.32 1.336M10.88 13.12l2.828 2.828M12.95 11.95l2.828-2.828m0 0a3.01 3.01 0 00.08-4.243m-2.828 2.828L12.95 11.95m0 0a5.971 5.971 0 011.563-.908m-1.563.908l2.828 2.828M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>';

        if (input.type === 'password') {
            input.type = 'text';
            svg.innerHTML = eyeOffPath;
        } else {
            input.type = 'password';
            svg.innerHTML = eyePath;
        }
    }
</script>
@endsection