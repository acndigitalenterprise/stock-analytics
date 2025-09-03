<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }
        .content {
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            margin-top: 20px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reset Your Password</h1>
    </div>
    
    <div class="content">
        <p>Hello {{ $user->name }},</p>
        
        <p>You have requested to reset your password for your Stock Analytics account.</p>
        
        <p>Click the button below to reset your password:</p>
        
        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="button">Reset Password</a>
        </div>
        
        <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
        <p style="word-break: break-all; color: #007bff;">{{ $resetUrl }}</p>
        
        <p>This link will expire in 24 hours for security reasons.</p>
        
        <p>If you didn't request this password reset, please ignore this email. Your password will remain unchanged.</p>
    </div>
    
    <div class="footer">
        <p>This email was sent from Stock Analytics. If you have any questions, please contact our support team.</p>
        <p>Thank you for using Stock Analytics!</p>
    </div>
</body>
</html> 