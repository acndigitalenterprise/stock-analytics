<!DOCTYPE html>
<html>
<head>
    <title>New Stock Analytics Request</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #222;">New Stock Analytics Request</h2>
        
        <p>Dear {{ $user->name }},</p>
        
        <p>A new stock analytics request has been created from your account.</p>
        
        <h3>Request Details:</h3>
        <ul>
            <li><strong>Stock Code:</strong> {{ $request->stock_code }}</li>
            <li><strong>Company Name:</strong> {{ $request->company_name }}</li>
                            <li><strong>Timeframe:</strong> {{ ucfirst(str_replace('per', '', $request->timeframe)) }}</li>
            <li><strong>Created:</strong> {{ \Carbon\Carbon::parse($request->created_at)->setTimezone('Asia/Jakarta')->format('F j, Y g:i A') }} (WIB)</li>
        </ul>
        
        <p>You can view and manage this request by logging into your account at: <a href="{{ url('/stock-analytics/admin') }}">{{ url('/stock-analytics/admin') }}</a></p>
        
        <p>Thank you for using Stock Analytics!</p>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ccc;">
        <p style="font-size: 12px; color: #666;">This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html> 