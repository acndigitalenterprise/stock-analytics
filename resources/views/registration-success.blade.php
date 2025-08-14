@extends('layout')

@section('head')
<style>
.success-content {
    text-align: center;
    max-width: 600px;
    margin: 60px auto;
    padding: 0 20px;
}

.success-header {
    margin-bottom: 40px;
}

.success-header h1 {
    font-size: 2.5rem;
    font-weight: 600;
    margin-bottom: 10px;
    color: #28a745;
}

.success-header p {
    font-size: 1.1rem;
    color: #666;
}

.success-icon {
    width: 80px;
    height: 80px;
    background: #28a745;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 30px;
    color: white;
    font-size: 40px;
}

.success-message {
    margin-bottom: 30px;
}

.success-message h2 {
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 15px;
    color: #333;
}

.success-message p {
    font-size: 1.1rem;
    color: #666;
    line-height: 1.6;
    margin-bottom: 10px;
}

.email-note {
    background: #e3f2fd;
    border: 1px solid #bbdefb;
    border-radius: 10px;
    padding: 20px;
    margin: 30px 0;
}

.email-note h3 {
    color: #1976d2;
    font-size: 1.2rem;
    margin-bottom: 10px;
}

.email-note p {
    color: #1976d2;
    font-size: 1rem;
    margin: 0;
}

.spam-note {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 15px;
    margin-top: 20px;
    font-size: 14px;
    color: #856404;
}

.action-buttons {
    margin-top: 30px;
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

@media (max-width: 600px) {
    .success-content {
        margin: 40px 20px;
    }
    
    .success-header h1 {
        font-size: 2rem;
    }
    
    .action-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .action-buttons .btn {
        width: 100%;
        max-width: 200px;
    }
}
</style>
@endsection

@section('content')
<div class="success-content">
    <div class="success-header">
        <h1>Registration Successful!</h1>
        <p>Welcome to Stock Trading AI Advisor</p>
    </div>
    
    <div class="success-icon">✓</div>
    
    <div class="success-message">
        <h2>Thank you for registering!</h2>
        <p>Your account has been successfully created. We've sent your login credentials to your email address.</p>
    </div>
    
    <div class="email-note">
        <h3>📧 Check Your Email</h3>
        <p>Please check your email inbox for your login information. The email contains your username and password to access your account.</p>
    </div>
    
    <div class="spam-note">
        <strong>Note:</strong> If you don't see the email in your inbox, please check your spam/junk folder.
    </div>
    
    <div class="action-buttons">
        <a href="{{ route('stock-analytics.index') }}" class="btn btn-primary">Back to Home</a>
    </div>
</div>
@endsection 