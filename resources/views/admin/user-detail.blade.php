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
            <a href="{{ route('stock-analytics.admin.users') }}" class="btn secondary" style="margin-left: 12px;">Back</a>
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
@endsection

@section('scripts')
<script>
// Using common togglePassword function from app.js
</script>
@endsection