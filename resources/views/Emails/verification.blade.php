<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Your Email - Stock Analytics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2196F3;
            margin-bottom: 10px;
        }
        .content {
            margin-bottom: 30px;
        }
        .verification-button {
            display: inline-block;
            background-color: #2196F3;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .verification-button:hover {
            background-color: #1976D2;
        }
        .verification-link {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            word-break: break-all;
            font-size: 14px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 14px;
            color: #666;
            text-align: center;
        }
        .info-box {
            background-color: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">AI Stock Analytics</div>
            <p>Email Verification Required</p>
        </div>

        <div class="content">
            <h2>Welcome to AI Stock Analytics, {{ $user->name }}!</h2>
            
            <p>Thank you for signing up for AI Stock Analytics. To complete your registration and start using our platform, please verify your email address by clicking the button below:</p>

            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="verification-button">Verify My Email Address</a>
            </div>

            <div class="info-box">
                <strong>Account Details:</strong><br>
                <strong>Email:</strong> {{ $user->email }}<br>
                <strong>Name:</strong> {{ $user->name }}
            </div>

            <p>If the button above doesn't work, you can copy and paste the following link into your browser:</p>
            
            <div class="verification-link">
                {{ $verificationUrl }}
            </div>

            <p><strong>Important:</strong> You must verify your email address before you can sign in to your account. This verification link will expire for security reasons, so please verify your email soon.</p>

            <p>Once your email is verified, you can sign in using the email and password you chose during registration.</p>
        </div>

        <div class="footer">
            <p>If you didn't create an account with AI Stock Analytics, please ignore this email.</p>
            <p>For support, please contact us or visit our help center.</p>
            <p>&copy; {{ date('Y') }} AI Stock Analytics. All rights reserved.</p>
        </div>
    </div>
</body>
</html>