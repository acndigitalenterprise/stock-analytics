<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Stock Analytics - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('Home/home.css') }}?v={{ time() }}">
</head>
<body>
    <div class="homepage-main-content">
        <div class="homepage-content-wrapper">
            @include('Partials.frontheader')
            <h2 class="homepage-tagline">AI Stock Analytics</h2>
            <h3 class="homepage-description">AI-powered insights to trade<br>with real-time signals and clear advice.</h3>
            
            <div class="homepage-buttons">
                <a href="{{ route('auth.signup.page') }}" class="homepage-btn homepage-btn-signup">Sign Up</a>
                <a href="{{ route('auth.signin.page') }}" class="homepage-btn homepage-btn-signin">Sign In</a>
            </div>
        </div>
@include('Partials.frontfooter')
    </div>

    <script src="{{ asset('Home/home.js') }}"></script>
</body>
</html>