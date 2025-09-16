<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Contacts - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('Home/contacts.css') }}?v={{ time() }}">
</head>
<body>
    <div class="homepage-main-content">
        <div class="homepage-content-wrapper">
            @include('Partials.frontheader')
            <h3 class="homepage-tagline">Contacts</h3>
            <h3 class="homepage-description">Get in touch with us</h3>
            
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="auth-success-block">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="auth-error-block">{{ session('error') }}</div>
            @endif
            
            @if($errors->any())
                <div class="auth-error-block">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="auth-form-container">
                <form action="{{ route('contacts.send') }}" method="POST" class="auth-form">
                    @csrf
                    
                    <div class="auth-form-group">
                        <label for="full_name">Full Name<span class="auth-required">*</span></label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                        @error('full_name')
                            <div class="auth-error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="auth-form-group">
                        <label for="mobile_number">Mobile Number<span class="auth-required">*</span></label>
                        <input type="text" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" required>
                        @error('mobile_number')
                            <div class="auth-error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="auth-form-group">
                        <label for="email_address">Email Address<span class="auth-required">*</span></label>
                        <input type="email" id="email_address" name="email_address" value="{{ old('email_address') }}" required>
                        @error('email_address')
                            <div class="auth-error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="auth-form-group">
                        <label for="message">Message<span class="auth-required">*</span></label>
                        <textarea id="message" name="message" rows="5" placeholder="Please describe your message here..." required>{{ old('message') }}</textarea>
                        @error('message')
                            <div class="auth-error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="auth-btn auth-btn-primary">Send Message</button>
                </form>
            </div>
            
            @include('Partials.frontfooter')
        </div>
    </div>
</body>
</html>