<!DOCTYPE html>
<html>
<head>
    <title>Stock Analytics Advice - {{ $request->stock_code }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #222;">📊 Stock Analytics Advice</h2>
        
        <p>Dear {{ $user->name }},</p>
        
        <p>Your stock analysis request for <strong>{{ $request->stock_code }}</strong> has been processed. Here's your AI-powered investment advice:</p>
        
        <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="color: #007bff; margin-top: 0;">📊 Stock Analysis Report</h3>
            <p style="color: #666; font-size: 12px; margin: 0 0 15px 0;">
                <em>Analysis powered by GPT-4 AI combined with technical indicators. Fallback to deterministic algorithm when AI unavailable.</em>
            </p>
            <div style="white-space: pre-line; font-family: monospace; font-size: 14px;">
{!! nl2br(e($request->advice)) !!}
            </div>
        </div>
        
        <h3>Request Details:</h3>
        <ul>
            <li><strong>Stock Code:</strong> {{ $request->stock_code }}</li>
            <li><strong>Company Name:</strong> {{ $request->company_name }}</li>
            <li><strong>Timeframe:</strong> {{ ucfirst(str_replace('1', '1 ', $request->timeframe)) }}</li>
            <li><strong>Analysis Date:</strong> {{ \Carbon\Carbon::parse($request->updated_at)->setTimezone('Asia/Jakarta')->format('F j, Y g:i A') }} (WIB)</li>
        </ul>
        
        <p>You can view and manage this request by logging into your account at: <a href="{{ url('/stock-analytics/admin') }}">{{ url('/stock-analytics/admin') }}</a></p>
        
        <div style="background-color: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; margin: 20px 0;">
            <h4 style="color: #856404; margin-top: 0;">⚠️ Important Disclaimers</h4>
            <ul style="color: #856404; margin: 0; font-size: 13px;">
                <li><strong>Not Financial Advice:</strong> This analysis is for informational purposes only</li>
                <li><strong>AI + Technical Analysis:</strong> Results combine GPT-4 AI with algorithmic technical indicators</li>
                <li><strong>Monitoring Period:</strong> Results tracked for {{ ucfirst(str_replace('1', '1 ', $request->timeframe)) }} from analysis time</li>
                <li><strong>Risk Warning:</strong> All investments carry risk. Never invest more than you can afford to lose</li>
                <li><strong>Do Your Research:</strong> Always conduct your own analysis before making investment decisions</li>
            </ul>
        </div>
        
        <p>Thank you for using Stock Analytics!</p>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ccc;">
        <p style="font-size: 12px; color: #666;">This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html> 