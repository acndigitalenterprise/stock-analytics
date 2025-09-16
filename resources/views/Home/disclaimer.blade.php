<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Disclaimer - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('Home/disclaimer.css') }}?v={{ time() }}">
</head>
<body>
    <div class="homepage-main-content">
        <div class="homepage-content-wrapper">
            @include('Partials.frontheader')
            <h3 class="homepage-tagline">Disclaimer</h3>
            <h3 class="homepage-description">Important legal information and terms of use</h3>
            
            <div class="auth-form-container">
                <div style="color: rgba(255, 255, 255, 0.9); line-height: 1.6; text-align: left;">
                    <h4 style="color: white; margin-bottom: 16px;">General Information</h4>
                    <p style="margin-bottom: 16px;">Ticker AI is a platform that provides analysis and recommendations powered by artificial intelligence regarding stocks and other financial instruments. However, <strong>we are not licensed financial advisors</strong>. All information, suggestions, or recommendations displayed on this platform are general, informational, and educational in nature.</p>
                    
                    <h4 style="color: white; margin-bottom: 16px; margin-top: 24px;">Investment Risk</h4>
                    <p style="margin-bottom: 16px;"><strong>We are not responsible for any losses, damages, or other consequences</strong> arising from the use of this service. <strong>All investment risks are the sole responsibility of each user.</strong> Past performance does not guarantee future results.</p>
                    
                    <h4 style="color: white; margin-bottom: 16px; margin-top: 24px;">Professional Advice</h4>
                    <p style="margin-bottom: 16px;">Users are expected to <strong>conduct independent research</strong>, <strong>consult with professional financial advisors</strong>, and <strong>consider their personal risk profiles</strong> before making investment decisions.</p>
                    
                    <h4 style="color: white; margin-bottom: 16px; margin-top: 24px;">No Guarantee</h4>
                    <p style="margin-bottom: 0;">By using Ticker AI, you acknowledge that <strong>we cannot guarantee investment results</strong> and <strong>are not responsible for the investment decisions you make</strong>.</p>
                </div>
            </div>
            
            @include('Partials.frontfooter')
        </div>
    </div>
</body>
</html>