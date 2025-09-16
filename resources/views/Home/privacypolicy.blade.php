<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Privacy Policy - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('Home/privacypolicy.css') }}?v={{ time() }}">
</head>
<body>
    <div class="homepage-main-content">
        <div class="homepage-content-wrapper">
            @include('Partials.frontheader')
            <h3 class="homepage-tagline">Privacy Policy</h3>
            <h3 class="homepage-description">How we collect, use, and protect your information</h3>
            
            <div class="auth-form-container">
                <div style="color: rgba(255, 255, 255, 0.9); line-height: 1.6; text-align: left;">
                    <h4 style="color: white; margin-bottom: 16px;">Information Collection</h4>
                    <p style="margin-bottom: 16px;">We collect information you provide directly to us, such as when you create an account, use our services, or contact us for support.</p>
                    
                    <h4 style="color: white; margin-bottom: 16px; margin-top: 24px;">How We Use Your Information</h4>
                    <p style="margin-bottom: 16px;">We use the information we collect to provide, maintain, and improve our services, process transactions, and communicate with you.</p>
                    
                    <h4 style="color: white; margin-bottom: 16px; margin-top: 24px;">Information Sharing</h4>
                    <p style="margin-bottom: 16px;">We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as described in this policy.</p>
                    
                    <h4 style="color: white; margin-bottom: 16px; margin-top: 24px;">Data Security</h4>
                    <p style="margin-bottom: 16px;">We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
                    
                    <h4 style="color: white; margin-bottom: 16px; margin-top: 24px;">Contact Us</h4>
                    <p style="margin-bottom: 0;">If you have any questions about this Privacy Policy, please contact us through our support channels.</p>
                </div>
            </div>
            
            @include('Partials.frontfooter')
        </div>
    </div>
</body>
</html>