<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Detail - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('Admin/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('Users/userdetail.css') }}?v={{ time() }}">
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
                        <h2>Users &gt; Detail</h2>
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
                @if(session('password_error'))
                    <div class="auth-error-block">{{ session('password_error') }}</div>
                @endif

                <!-- User Detail Form -->
                <div class="auth-form-container" style="max-width: 500px; margin: 32px auto 0;">
                    <div class="auth-info-note">
                        <strong>Profile</strong><br>
                        Edit user profile.
                    </div>

                    <form id="user-update-form" action="{{ route('users.update', $targetUser->id) }}" method="POST" class="auth-form">
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
                        
                        <div class="auth-form-group">
                            <label for="user_role">Role</label>
                            @if(isset($user) && $user->role === 'super_admin')
                            <select name="role" id="user_role">
                                <option value="user" {{ $targetUser->role == 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ $targetUser->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="super_admin" {{ $targetUser->role == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            </select>
                            @else
                            <input type="text" id="user_role" value="{{ ucfirst(str_replace('_', ' ', $targetUser->role)) }}" readonly style="background: rgba(255, 255, 255, 0.05) !important; border: 1px solid rgba(255, 255, 255, 0.15) !important; color: rgba(255, 255, 255, 0.8) !important; cursor: not-allowed !important;">
                            @endif
                            @error('role')
                                <div class="auth-error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="auth-info-note" style="margin-top: 32px;">
                            <strong>Security</strong><br>
                            Change user password.
                        </div>
                        
                        <div class="auth-form-group">
                            <label for="user_new_pwd">Change Password</label>
                            <div class="auth-password-container">
                                <input type="password" id="user_new_pwd" name="user_new_pwd" class="auth-password-input" placeholder="Enter new password">
                                <button type="button" class="auth-password-toggle" onclick="toggleAuthPassword('user_new_pwd')">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                            @error('user_new_pwd')
                                <div class="auth-error-message">{{ $message }}</div>
                            @enderror
                            @error('password')
                                <div class="auth-error-message">{{ $message }}</div>
                            @enderror
                            <small style="color: rgba(255, 255, 255, 0.6); font-size: 0.85rem; margin-top: 6px; display: block;">
                                Leave blank if you don't want to change the password
                            </small>
                        </div>
                        
                    </form>
                </div>
                
                <!-- Action Buttons Section -->
                <div class="user-detail-actions" style="margin: 48px auto 0; max-width: 500px;">
                    <div class="user-detail-buttons">
                        <button type="submit" form="user-update-form" id="updateUserBtn" class="user-detail-btn user-detail-btn-update">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Update
                        </button>
                        
                        <form action="{{ route('users.destroy', $targetUser->id) }}" method="POST" onsubmit="return confirmUserDeletion(event, '{{ $targetUser->name }}')">
                            @csrf
                            <button type="submit" class="user-detail-btn user-detail-btn-delete">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete
                            </button>
                        </form>
                        
                        @if($targetUser->email_verified_at)
                            <button class="user-detail-btn user-detail-btn-verified" disabled>
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Verified
                            </button>
                        @else
                            <form action="{{ route('users.verify', $targetUser->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="user-detail-btn user-detail-btn-verify">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Verify
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('users.index') }}" class="user-detail-btn user-detail-btn-back">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    @include('Components.footer')
</div>

@include('Components.admin-scripts')

<script>
// SIMPLE: Just password toggle, NO AJAX
function toggleAuthPassword(fieldId) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    field.type = field.type === 'password' ? 'text' : 'password';
}
</script>

</body>
</html>