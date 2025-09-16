<!DOCTYPE html>
<html>
<head>
    <title>Stock Analytics Request</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #222;">Stock Analytics Request Confirmation</h2>
        
        <p>Dear {{ $user->name }},</p>
        
        <p>Your stock analytics request has been received and is being processed.</p>
        
        <h3>Request Details:</h3>
        <ul>
            <li><strong>Full Name:</strong> {{ $request->full_name }}</li>
            <li><strong>Mobile Number:</strong> {{ $request->mobile_number }}</li>
            <li><strong>Email Address:</strong> {{ $request->email }}</li>
            <li><strong>Stock Code:</strong> {{ $request->stock_code }}</li>
            <li><strong>Company Name:</strong> {{ $request->company_name }}</li>
                            <li><strong>Timeframe:</strong> {{ ucfirst(str_replace('per', '', $request->timeframe)) }}</li>
        </ul>
        
        @if($isNewUser)
            <h3>Your Login Information:</h3>
            <p>We have created an account for you with the following details:</p>
            <ul>
                <li><strong>Login URL:</strong> <a href="{{ url('/stock-analytics/admin') }}">{{ url('/stock-analytics/admin') }}</a></li>
                <li><strong>Username:</strong> {{ $user->email }}</li>
                <li><strong>Password:</strong> {{ $password }}</li>
            </ul>
            <p style="color: #b00; font-weight: bold;">Please save this password securely. You can change it after logging in.</p>
        @else
            <p>You can view your request status by logging into your account at: <a href="{{ url('/stock-analytics/admin') }}">{{ url('/stock-analytics/admin') }}</a></p>
        @endif
        
        <p>Thank you for using Stock Analytics!</p>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ccc;">
        <p style="font-size: 12px; color: #666;">This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html> 