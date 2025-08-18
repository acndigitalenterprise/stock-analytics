@extends('layout')

@section('body-class', 'admin-layout')

@section('content')
    @php $isAdminLayout = true; @endphp
    <h2>Users > Detail</h2>
    
    <div style="margin-bottom: 32px;">
        @if(session('success'))
            <div class="success-message">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="error-message">{{ session('error') }}</div>
        @endif
        @if(session('password_error'))
            <div class="error-message">{{ session('password_error') }}</div>
        @endif
        
        <form action="{{ route('stock-analytics.admin.users.update', $targetUser->id) }}" method="POST">
            @csrf
            
            <!-- Profile Information Section -->
            <div style="margin-bottom: 32px;">
                
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $targetUser->name) }}" required>
                    @error('full_name')<div class="error">{{ $message }}</div>@enderror
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $targetUser->email) }}" required>
                    @error('email')<div class="error">{{ $message }}</div>@enderror
                </div>
                
                <div class="form-group">
                    <label for="mobile_number">Mobile Number</label>
                    <input type="text" name="mobile_number" id="mobile_number" value="{{ old('mobile_number', $targetUser->mobile_number) }}" placeholder="Optional">
                    @error('mobile_number')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
            
            <!-- Change Password Section -->
            <div style="margin-bottom: 32px;">
                <h3 style="margin-bottom: 16px; color: #333; border-bottom: 2px solid #eee; padding-bottom: 8px;">Change Password</h3>
                <p style="color: #666; margin-bottom: 16px; font-size: 14px;">Leave password field empty if you don't want to change this user's password.</p>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="new_password" style="padding-right:36px;">
                        <span class="toggle-password" onclick="togglePassword('new_password', this)" style="position:absolute;top:50%;right:10px;transform:translateY(-50%);cursor:pointer;">
                            <svg id="icon-new_password" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </span>
                    </div>
                    @error('password')<div class="error">{{ $message }}</div>@enderror
                </div>
                
                @if(isset($user) && $user->role === 'super_admin')
                <div class="form-group">
                    <label for="user_role">Role</label>
                    <select name="role" id="user_role">
                        <option value="">-- Pilih --</option>
                        <option value="user" {{ $targetUser->role == 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ $targetUser->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                @endif
            </div>
            
            <button type="submit" class="btn">Update</button>
            <div style="margin-top: 12px;">
                <a href="{{ route('stock-analytics.admin.users') }}" class="btn secondary">Back</a>
            </div>
        </form>
        
        <!-- User Role Information -->
        <div style="margin-top: 24px; padding: 16px; background: #f8f9fa; border-radius: 6px; border-left: 4px solid #2196F3;">
            <div style="font-weight: bold; color: #333; margin-bottom: 8px;">Account Information</div>
            <div style="color: #666; font-size: 14px;">
                <strong>Role:</strong> {{ ucfirst($targetUser->role ?? 'User') }}
                @if($targetUser->role === 'admin')
                    <span style="color: #2196F3; font-weight: bold;"> (Administrator)</span>
                @endif
            </div>
            <div style="color: #666; font-size: 14px; margin-top: 4px;">
                <strong>Member Since:</strong> {{ $targetUser->created_at->format('F j, Y') }}
            </div>
        </div>
    </div>

    <!-- Mobile Layout -->
    <div class="mobile-card">
        <div class="mobile-detail" style="display:none;">
            <form action="{{ route('stock-analytics.admin.users.update', $targetUser->id) }}" method="POST">
                @csrf
                
                <!-- Full Name -->
                <div style="font-size: 0.9em; color: #000000; margin-bottom: 16px;">
                    <b>Full Name</b><br>
                    <input type="text" name="full_name" value="{{ old('full_name', $targetUser->name) }}" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                    @error('full_name')<div style="color: #dc3545; font-size: 0.8em; margin-top: 4px;">{{ $message }}</div>@enderror
                </div>
                
                <!-- Email Address -->
                <div style="font-size: 0.9em; color: #000000; margin-bottom: 16px;">
                    <b>Email Address</b><br>
                    <input type="email" name="email" value="{{ old('email', $targetUser->email) }}" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                    @error('email')<div style="color: #dc3545; font-size: 0.8em; margin-top: 4px;">{{ $message }}</div>@enderror
                </div>
                
                <!-- Mobile Number -->
                <div style="font-size: 0.9em; color: #000000; margin-bottom: 16px;">
                    <b>Mobile Number</b><br>
                    <input type="text" name="mobile_number" value="{{ old('mobile_number', $targetUser->mobile_number) }}" placeholder="Optional" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                    @error('mobile_number')<div style="color: #dc3545; font-size: 0.8em; margin-top: 4px;">{{ $message }}</div>@enderror
                </div>
                
                <!-- New Password -->
                <div style="font-size: 0.9em; color: #000000; margin-bottom: 16px;">
                    <b>New Password</b><br>
                    <div style="color: #666; font-size: 0.8em; margin-bottom: 8px;">Leave empty if you don't want to change password</div>
                    <input type="password" name="password" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                    @error('password')<div style="color: #dc3545; font-size: 0.8em; margin-top: 4px;">{{ $message }}</div>@enderror
                </div>
                
                @if(isset($user) && $user->role === 'super_admin')
                <!-- Role -->
                <div style="font-size: 0.9em; color: #000000; margin-bottom: 16px;">
                    <b>Role</b><br>
                    <select name="role" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                        <option value="">-- Pilih --</option>
                        <option value="user" {{ $targetUser->role == 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ $targetUser->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                @endif
                
                <!-- Current Role Info -->
                <div style="font-size: 0.9em; color: #000000; margin-bottom: 16px;">
                    <b>Current Role</b><br>
                    {{ ucfirst($targetUser->role ?? 'User') }}
                    @if($targetUser->role === 'admin')
                        (Administrator)
                    @endif
                </div>
                
                <!-- Member Since -->
                <div style="font-size: 0.9em; color: #000000; margin-bottom: 20px;">
                    <b>Member Since</b><br>
                    {{ $targetUser->created_at->format('F j, Y') }}
                </div>
                
                <!-- Action Buttons -->
                <button type="submit" class="btn" style="width: 100%; margin-bottom: 12px;">Update</button>
                <a href="{{ route('stock-analytics.admin.users') }}" class="btn secondary" style="width: 100%; text-decoration: none; text-align: center; display: block;">Back</a>
            </form>
        </div>
    </div>

<style>
@media (max-width: 768px) {
    .admin-content-container > div:first-child {
        display: none !important;
    }
    .mobile-card {
        display: block !important;
    }
    .mobile-card .mobile-detail {
        display: block !important;
    }
}
</style>
@endsection

@section('scripts')
<script>
// Using common togglePassword function from app.js
</script>
@endsection