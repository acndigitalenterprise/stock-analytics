<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Stock Analytics</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #222;">Welcome to Stock Analytics!</h2>
        
        <p>Dear {{ $user->name }},</p>
        
        <p>Your account has been created successfully. Here are your login details:</p>
        
        <h3>Login Information:</h3>
        <ul>
            <li><strong>Login URL:</strong> <a href="{{ url('/stock-analytics/admin') }}">{{ url('/stock-analytics/admin') }}</a></li>
            <li><strong>Username:</strong> {{ $user->email }}</li>
            <li><strong>Password:</strong> {{ $password }}</li>
        </ul>
        
        <p style="color: #b00; font-weight: bold;">Please save this password securely. You can change it after logging in.</p>
        
        <p>You can now access the admin panel to view and manage your stock analytics requests.</p>
        
        <p>Thank you for joining Stock Analytics!</p>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ccc;">
        <p style="font-size: 12px; color: #666;">This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html> 