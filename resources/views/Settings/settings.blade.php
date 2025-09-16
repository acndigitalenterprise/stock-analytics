<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Settings - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('Admin/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('Settings/settings.css') }}?v={{ time() }}">
</head>
<body class="admin-layout">

<!-- DESKTOP ADMIN LAYOUT -->
<div class="admin-layout-container">
    @include('Components.header')
    
    <!-- Mobile Sidebar Overlay -->
    <div class="mobile-sidebar-overlay" onclick="closeMobileMenu()"></div>
    
    <!-- Main Content Area -->
    <div class="admin-main-content">
        @include('Components.sidebar')
        
        <!-- Admin Body Content -->
        <main class="admin-body">
            <div class="admin-content-container">
                <div class="users-flex-between">
                    <div>
                        <h2>Settings</h2>
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
                
                <!-- Display Validation Errors -->
                @if($errors->any())
                    <div class="auth-error-block">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Settings Form -->
                <div class="auth-form-container" style="max-width: 500px; margin: 32px auto 0;">
                    <div class="auth-info-note">
                        <strong>Profile</strong><br>
                        Keep your profile up to date.
                    </div>

                    <form id="settings-update-form" action="{{ route('settings.update') }}" method="POST" class="auth-form">
                        @csrf
                        
                        <!-- Debug field (remove in production) -->
                        <input type="hidden" name="debug_timestamp" value="{{ time() }}">
                        
                        <!-- Registered Date (Read-only) -->
                        <div class="auth-form-group">
                            <label for="registered_date">Registered Date</label>
                            <input type="text" id="registered_date" value="{{ $user->created_at ? $user->created_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') : 'N/A' }}" readonly style="background: rgba(255, 255, 255, 0.05) !important; border: 1px solid rgba(255, 255, 255, 0.15) !important; color: rgba(255, 255, 255, 0.8) !important; cursor: not-allowed !important;">
                        </div>
                        
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
                        
                        <div class="auth-form-group">
                            <label for="user_role">User Role</label>
                            <input type="text" id="user_role" value="{{ ucfirst(str_replace('_', ' ', $user->role)) }}" readonly style="background: rgba(255, 255, 255, 0.05) !important; border: 1px solid rgba(255, 255, 255, 0.15) !important; color: rgba(255, 255, 255, 0.8) !important; cursor: not-allowed !important;">
                        </div>
                        
                        <div class="auth-info-note" style="margin-top: 32px;">
                            <strong>Security</strong><br>
                            Change your password.
                        </div>
                        
                        <div class="auth-form-group">
                            <label for="new_password">Change Password</label>
                            <div class="auth-password-container">
                                <input type="password" id="new_password" name="new_password" class="auth-password-input" placeholder="Enter new password">
                                <button type="button" class="auth-password-toggle" onclick="toggleAuthPassword('new_password')">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                            @error('new_password')
                                <div class="auth-error-message">{{ $message }}</div>
                            @enderror
                            <small style="color: rgba(255, 255, 255, 0.6); font-size: 0.85rem; margin-top: 6px; display: block;">
                                Leave blank if you don't want to change the password
                            </small>
                        </div>
                        
                        <!-- Action Buttons INSIDE Form -->
                        <div class="user-detail-actions" style="margin: 48px auto 0;">
                            <div class="user-detail-buttons">
                                <button type="submit" id="settings-update-btn" class="user-detail-btn user-detail-btn-update">
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
                        
                    </form>
                </div>
            </div>
        </main>
    </div>
    
    @include('Components.footer')
</div>

@include('Components.admin-scripts')

<script>
// Simple password toggle - No changes needed here
function toggleAuthPassword(fieldId) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    field.type = field.type === 'password' ? 'text' : 'password';
}

// Debug form submission
document.getElementById('settings-update-form').addEventListener('submit', function(e) {
    console.log('Form submit event fired');
    console.log('Form data:', new FormData(this));
    console.log('CSRF token:', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Let the form submit naturally - don't prevent default
});

// Debug page load
console.log('Settings page loaded');
console.log('CSRF token available:', document.querySelector('meta[name="csrf-token"]') ? 'YES' : 'NO');
</script>

</body>
</html>