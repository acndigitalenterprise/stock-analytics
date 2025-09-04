@extends('layout')

@section('body-class', 'admin-layout')

@section('content')
<link rel="stylesheet" href="{{ asset('Admin/admin.css') }}">
<link rel="stylesheet" href="{{ asset('settings/settings.css') }}?v={{ time() }}">

<div class="admin-content-container">
    @php $isAdminLayout = true; @endphp
    
    <div class="users-flex-between">
        <div>
            <h2>settings</h2>
        </div>
        <div></div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="users-message users-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="users-message users-error">{{ session('error') }}</div>
    @endif
    
    <!-- settings Form -->
    <div class="settings-auth-form-container">
        
        <!-- Profile Section -->
        <div class="auth-info-note">
            <strong>Profile</strong><br>
            Keep your profile up to date.
        </div>
        
        <form id="settings-form" action="{{ route('settings.update') }}" method="POST" class="auth-form">
            @csrf
            
            <!-- Registered Date (Read-only) -->
            <div class="auth-form-group">
                <label for="registered_date">Registered Date</label>
                <input type="text" id="registered_date" value="{{ $user->created_at ? $user->created_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') : 'N/A' }}" readonly class="settings-readonly-input">
            </div>
            
            <!-- Full Name (Editable) -->
            <div class="auth-form-group">
                <label for="full_name">Full Name<span class="auth-required">*</span></label>
                <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $user->name) }}" required>
                @error('full_name')
                    <div class="auth-error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Email Address (Editable) -->
            <div class="auth-form-group">
                <label for="email">Email Address<span class="auth-required">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="auth-error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Mobile Number (Optional) -->
            <div class="auth-form-group">
                <label for="mobile_number">Mobile Number</label>
                <input type="text" name="mobile_number" id="mobile_number" value="{{ old('mobile_number', $user->mobile_number) }}" placeholder="Optional">
                @error('mobile_number')
                    <div class="auth-error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- User Role (Read-only display) -->
            <div class="auth-form-group">
                <label for="user_role_display">User Role</label>
                <input type="text" id="user_role_display" value="{{ ucfirst($user->role) }}" readonly class="settings-readonly-input capitalize">
            </div>
            
            <!-- Security Section -->
            <div class="auth-info-note settings-security-section">
                <strong>Security</strong><br>
                Change your password.
            </div>
            
            <!-- Change Password (Optional) -->
            <div class="auth-form-group">
                <label for="settings_new_password">Change Password</label>
                <input type="password" name="new_password" id="settings_new_password" placeholder="Enter new password">
                @error('new_password')
                    <div class="auth-error-message">{{ $message }}</div>
                @enderror
                <small class="settings-helper-text">
                    Leave blank if you don't want to change the password
                </small>
            </div>
            
        </form>
    </div>
    
    <!-- Action Buttons Section -->
    <div class="user-detail-actions">
        <div class="user-detail-buttons">
            <button type="submit" form="settings-form" class="user-detail-btn user-detail-btn-update">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Update
            </button>
            <a href="{{ route('dashboard') }}" class="user-detail-btn user-detail-btn-back">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </div>
    
</div>

<script src="{{ asset('settings/settings.js') }}?v={{ time() }}"></script>
@endsection