@extends('layout')

@section('title', 'Forgot Password - Stock Analytics')

@section('body-class', 'forgot-password-layout')

@section('content')
<div style="max-width: 400px; margin: 80px auto; padding: 0 24px;">
    <div style="background: #fff; padding: 40px 32px; border-radius: 12px; box-shadow: 0 2px 16px rgba(0,0,0,0.1);">
        <h1 style="text-align: center; margin-bottom: 8px; font-size: 1.8rem;">Forgot Password</h1>
        <p style="text-align: center; color: #666; margin-bottom: 32px; font-size: 0.9rem;">Enter your email address and we'll send you a link to reset your password.</p>

        @if(session('success'))
            <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px; font-size: 0.9rem;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px; font-size: 0.9rem;">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('stock-analytics.forgot-password') }}" method="POST" id="forgotPasswordForm">
            @csrf
            
            <div class="form-group" style="margin-bottom: 24px;">
                <label for="email" style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Email Address</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    required 
                    value="{{ old('email') }}"
                    style="width: 100%; padding: 12px 16px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; box-sizing: border-box;"
                    placeholder="Enter your email address"
                >
                @if($errors->has('email'))
                    <div style="color: #dc3545; font-size: 0.85rem; margin-top: 4px;">
                        {{ $errors->first('email') }}
                    </div>
                @endif
            </div>

            <button 
                type="submit" 
                class="btn" 
                id="submitBtn"
                style="width: 100%; background: #007bff; color: white; padding: 14px; border: none; border-radius: 6px; font-size: 1rem; font-weight: 600; cursor: pointer; margin-bottom: 16px;"
                onmouseover="this.style.background='#0056b3'"
                onmouseout="this.style.background='#007bff'"
            >
                Send Reset Link
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px;">
            <a href="{{ route('stock-analytics.index') }}" style="color: #007bff; text-decoration: none; font-size: 0.9rem;">
                ← Back to Sign In
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.textContent;
    
    submitBtn.textContent = 'Sending...';
    submitBtn.disabled = true;
    
    // Re-enable after 5 seconds to prevent stuck state
    setTimeout(function() {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    }, 5000);
});

// Auto-hide messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const successMessage = document.querySelector('[style*="background: #d4edda"]');
    const errorMessage = document.querySelector('[style*="background: #f8d7da"]');
    
    if (successMessage) {
        setTimeout(function() {
            successMessage.style.display = 'none';
        }, 5000);
    }
    
    if (errorMessage) {
        setTimeout(function() {
            errorMessage.style.display = 'none';
        }, 5000);
    }
});
</script>

<style>
.forgot-password-layout {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
}

.form-group input:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}
</style>
@endsection