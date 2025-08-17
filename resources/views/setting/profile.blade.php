@extends('layout')

@section('body-class', 'admin-layout')

@section('content')
    @php $isAdminLayout = true; @endphp
    <h2>Profile</h2>
    
    <div style="margin-bottom: 32px;">
        @if(session('success'))
            <div class="success-message">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="error-message">{{ session('error') }}</div>
        @endif
        
        <form action="{{ route('stock-analytics.setting.user.update') }}" method="POST">
            @csrf
            
            <!-- Profile Information Section -->
            <div style="margin-bottom: 32px;">
                
                <div class="form-group">
                    <label for="full_name">Full Name<span style="color: red;">*</span></label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $user->name) }}" required>
                    @error('full_name')<div class="error">{{ $message }}</div>@enderror
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address<span style="color: red;">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="error">{{ $message }}</div>@enderror
                </div>
                
                <div class="form-group">
                    <label for="mobile_number">Mobile Number</label>
                    <input type="text" name="mobile_number" id="mobile_number" value="{{ old('mobile_number', $user->mobile_number) }}" placeholder="Optional">
                    @error('mobile_number')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
            
            <!-- Change Password Section -->
            <div style="margin-bottom: 32px;">
                <h3 style="margin-bottom: 16px; color: #333; border-bottom: 2px solid #eee; padding-bottom: 8px;">Change Password</h3>
                <p style="color: #666; margin-bottom: 16px; font-size: 14px;">Leave password field empty if you don't want to change your password.</p>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" placeholder="Enter new password">
                    @error('new_password')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
            
            <button type="submit" class="btn">Update</button>
        </form>
        
        <!-- User Role Information -->
        <div style="margin-top: 24px; padding: 16px; background: #f8f9fa; border-radius: 6px; border-left: 4px solid #2196F3;">
            <div style="font-weight: bold; color: #333; margin-bottom: 8px;">Account Information</div>
            <div style="color: #666; font-size: 14px;">
                <strong>Role:</strong> {{ ucfirst($user->role ?? 'User') }}
                @if($user->role === 'admin')
                    <span style="color: #2196F3; font-weight: bold;"> (Administrator)</span>
                @elseif($user->role === 'super_admin')
                    <span style="color: #FF6B35; font-weight: bold;"> (Super Administrator)</span>
                @endif
            </div>
            <div style="color: #666; font-size: 14px; margin-top: 4px;">
                <strong>Member Since:</strong> {{ $user->created_at->format('F j, Y') }}
            </div>
        </div>
    </div>
@endsection