<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Role Change Notification</title>
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
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
        }
        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .badge.admin {
            background: #28a745;
        }
        .badge.user {
            background: #6c757d;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
            font-size: 12px;
        }
        .promoted {
            color: #28a745;
        }
        .demoted {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔐 Role Change Notification</h1>
    </div>

    <div class="content">
        <p>Hello <strong>{{ $targetUser->name }}</strong>,</p>

        <p>Your account role has been <strong class="{{ $action }}">{{ $action }}</strong> by <strong>{{ $actionBy->name }}</strong> ({{ $actionBy->email }}).</p>

        <div style="background: #f8f9fa; padding: 20px; border-radius: 6px; margin: 20px 0;">
            <p style="margin: 0;"><strong>Role Change Details:</strong></p>
            <p style="margin: 10px 0 0 0;">
                Previous Role: <span class="badge {{ strtolower($oldRole) }}">{{ $oldRole }}</span><br>
                New Role: <span class="badge {{ strtolower($newRole) }}">{{ $newRole }}</span>
            </p>
        </div>

        @if($newRole === 'Admin')
        <div style="background: #e3f2fd; border-left: 4px solid #2196F3; padding: 15px; margin: 20px 0;">
            <p style="margin: 0; font-weight: bold;">🎉 Congratulations!</p>
            <p style="margin: 5px 0 0 0;">You now have administrator privileges and can manage other users.</p>
        </div>
        @elseif($oldRole === 'Admin')
        <div style="background: #fff3e0; border-left: 4px solid #ff9800; padding: 15px; margin: 20px 0;">
            <p style="margin: 0; font-weight: bold;">⚠️ Role Change</p>
            <p style="margin: 5px 0 0 0;">Your administrator privileges have been revoked. You now have standard user access.</p>
        </div>
        @endif

        <p>If you have any questions about this change, please contact the Super Administrator or system administrator.</p>

        <p>Best regards,<br>
        <strong>Stock Analytics Team</strong></p>
    </div>

    <div class="footer">
        <p>© 2025 ACN Digital Enterprise. All Rights Reserved.<br>
        Powered By ABAIP</p>
    </div>
</body>
</html>