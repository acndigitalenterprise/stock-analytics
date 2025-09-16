<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Account Action Notification</title>
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
        .badge.super_admin {
            background: #dc3545;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
            font-size: 12px;
        }
        .action-created {
            color: #28a745;
        }
        .action-updated {
            color: #2196F3;
        }
        .action-deleted {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>
            @if($action === 'created')
                👋 Welcome to Stock Analytics
            @elseif($action === 'updated')
                🔄 Account Updated
            @else
                🗑️ Account Deleted
            @endif
        </h1>
    </div>

    <div class="content">
        @if($action === 'created')
            <p>Hello <strong>{{ $targetUser->name }}</strong>,</p>
            
            <p>Your account has been <strong class="action-{{ $action }}">created</strong> by <strong>{{ $actionBy->name }}</strong> ({{ $actionBy->email }}).</p>

            <div style="background: #e8f5e8; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0;">
                <p style="margin: 0; font-weight: bold;">🎉 Account Created Successfully!</p>
                <p style="margin: 5px 0 0 0;">You now have access to the Stock Analytics platform.</p>
            </div>

            <div style="background: #f8f9fa; padding: 20px; border-radius: 6px; margin: 20px 0;">
                <p style="margin: 0;"><strong>Your Account Details:</strong></p>
                <p style="margin: 10px 0 0 0;">
                    <strong>Name:</strong> {{ $targetUser->name }}<br>
                    <strong>Email:</strong> {{ $targetUser->email }}<br>
                    <strong>Mobile:</strong> {{ $targetUser->mobile_number }}<br>
                    <strong>Role:</strong> <span class="badge {{ strtolower($newRole) }}">{{ $newRole }}</span>
                </p>
            </div>

        @elseif($action === 'updated')
            <p>Hello <strong>{{ $targetUser->name }}</strong>,</p>
            
            <p>Your account has been <strong class="action-{{ $action }}">updated</strong> by <strong>{{ $actionBy->name }}</strong> ({{ $actionBy->email }}).</p>

            <div style="background: #e3f2fd; border-left: 4px solid #2196F3; padding: 15px; margin: 20px 0;">
                <p style="margin: 0; font-weight: bold;">📝 Account Information Updated</p>
                <p style="margin: 5px 0 0 0;">Your account details have been modified.</p>
            </div>

            <div style="background: #f8f9fa; padding: 20px; border-radius: 6px; margin: 20px 0;">
                <p style="margin: 0;"><strong>Current Account Details:</strong></p>
                <p style="margin: 10px 0 0 0;">
                    <strong>Name:</strong> {{ $targetUser->name }}<br>
                    <strong>Email:</strong> {{ $targetUser->email }}<br>
                    <strong>Mobile:</strong> {{ $targetUser->mobile_number }}<br>
                    <strong>Role:</strong> <span class="badge {{ strtolower($newRole) }}">{{ $newRole }}</span>
                </p>
            </div>

        @else
            <p>This is to inform you that user account <strong>{{ $targetUser->name }}</strong> ({{ $targetUser->email }}) has been <strong class="action-{{ $action }}">deleted</strong> by <strong>{{ $actionBy->name }}</strong> ({{ $actionBy->email }}).</p>

            <div style="background: #ffebee; border-left: 4px solid #dc3545; padding: 15px; margin: 20px 0;">
                <p style="margin: 0; font-weight: bold;">🗑️ User Account Deleted</p>
                <p style="margin: 5px 0 0 0;">This action has permanently removed the user and all associated data.</p>
            </div>

            <div style="background: #f8f9fa; padding: 20px; border-radius: 6px; margin: 20px 0;">
                <p style="margin: 0;"><strong>Deleted Account Details:</strong></p>
                <p style="margin: 10px 0 0 0;">
                    <strong>Name:</strong> {{ $targetUser->name }}<br>
                    <strong>Email:</strong> {{ $targetUser->email }}<br>
                    <strong>Role:</strong> <span class="badge {{ strtolower($newRole) }}">{{ $newRole }}</span>
                </p>
            </div>
        @endif

        @if($action !== 'deleted')
            <p>You can access your account at: <a href="{{ url('/stock-analytics') }}">{{ url('/stock-analytics') }}</a></p>
        @endif

        <p>If you have any questions about this {{ $action }}, please contact the administrator.</p>

        <p>Best regards,<br>
        <strong>Stock Analytics Team</strong></p>
    </div>

    <div class="footer">
        <p>© 2025 ACN Digital Enterprise. All Rights Reserved.<br>
        Powered By ABAIP</p>
    </div>
</body>
</html>