<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>About - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('Home/about.css') }}?v={{ time() }}">
</head>
<body>
    <div class="homepage-main-content">
        <div class="homepage-content-wrapper">
            @include('Partials.frontheader')
            <h3 class="homepage-tagline">About</h3>
            <h3 class="homepage-description">AI Stock Analytics that helps trade better</h3>
            
            <div class="auth-form-container">
                <div class="about-content">
                    <p class="about-text-main">AI Stock Analytics is an AI tool that helps stock traders make better decisions.</p>
                    
                    <p class="about-text-sub">Powered by AI, it delivers real-time insights, clear signals, and simple analytics to guide your trading with clarity.</p>
                </div>
                
            </div>
            
            @include('Partials.frontfooter')
        </div>
    </div>
</body>
</html>