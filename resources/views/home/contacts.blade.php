<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Contacts - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('home/contacts.css') }}?v={{ time() }}">
</head>
<body>
    <div class="homepage-main-content">
        <div class="homepage-content-wrapper">
            @include('partials.frontheader')
            <h3 class="homepage-tagline">Contacts</h3>
            <h3 class="homepage-description">Get in touch with us</h3>
            
            <div class="auth-form-container">
                <form class="auth-form">
                    @csrf
                    
                    <div class="auth-form-group">
                        <label for="full_name">Full Name<span class="auth-required">*</span></label>
                        <input type="text" id="full_name" name="full_name" required>
                    </div>
                    
                    <div class="auth-form-group">
                        <label for="mobile_number">Mobile Number<span class="auth-required">*</span></label>
                        <input type="text" id="mobile_number" name="mobile_number" required>
                    </div>
                    
                    <div class="auth-form-group">
                        <label for="email_address">Email Address<span class="auth-required">*</span></label>
                        <input type="email" id="email_address" name="email_address" required>
                    </div>
                    
                    <button type="submit" class="auth-btn auth-btn-primary">Send Message</button>
                </form>
            </div>
            
            @include('partials.frontfooter')
        </div>
    </div>
</body>
</html>