<!DOCTYPE html>
<html>
<head>
    <title>Sent Emails</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .email { border: 1px solid #ddd; margin: 20px 0; padding: 20px; border-radius: 5px; }
        .email-header { background: #f5f5f5; padding: 10px; margin: -20px -20px 20px -20px; border-radius: 5px 5px 0 0; }
        .email-content { background: white; padding: 20px; border: 1px solid #eee; }
    </style>
</head>
<body>
    <h1>Sent Emails (Array Driver)</h1>
    
    @if(count($emails) > 0)
        @foreach($emails as $index => $email)
            <div class="email">
                <div class="email-header">
                    <strong>Email #{{ $index + 1 }}</strong><br>
                    <strong>To:</strong> {{ $email->getTo()[0]->getAddress() }}<br>
                    <strong>Subject:</strong> {{ $email->getSubject() }}<br>
                    <strong>Date:</strong> {{ $email->getDate()->format('Y-m-d H:i:s') }}
                </div>
                <div class="email-content">
                    {!! $email->getHtmlBody() !!}
                </div>
            </div>
        @endforeach
    @else
        <p>No emails sent yet.</p>
    @endif
    
    <p><a href="{{ route('dashboard') }}">Back to Admin</a></p>
</body>
</html> 