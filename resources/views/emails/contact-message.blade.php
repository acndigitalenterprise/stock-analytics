<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contact Message - Ticker AI</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            margin: -30px -30px 20px -30px;
            text-align: center;
        }
        .field {
            margin-bottom: 15px;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .field-label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }
        .field-value {
            color: #333;
        }
        .message-content {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #667eea;
            margin: 15px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Contact Message</h1>
            <p>From Ticker AI Contact Form</p>
        </div>

        <div class="field">
            <div class="field-label">Full Name:</div>
            <div class="field-value">{{ $full_name }}</div>
        </div>

        <div class="field">
            <div class="field-label">Email Address:</div>
            <div class="field-value">{{ $email_address }}</div>
        </div>

        <div class="field">
            <div class="field-label">Mobile Number:</div>
            <div class="field-value">{{ $mobile_number }}</div>
        </div>

        <div class="field">
            <div class="field-label">Message:</div>
            <div class="message-content">
                {!! nl2br(e($user_message)) !!}
            </div>
        </div>

        <div class="field">
            <div class="field-label">Submitted:</div>
            <div class="field-value">{{ $submitted_at }}</div>
        </div>

        <div class="footer">
            <p>This message was sent from the Ticker AI contact form at <a href="https://tickerai.app/contacts">https://tickerai.app/contacts</a></p>
            <p>You can reply directly to this email to respond to {{ $full_name }}.</p>
        </div>
    </div>
</body>
</html>