@extends('layout')

@section('content')
    <div class="main-content">
        <h2 class="page-title">Privacy Policy</h2>
        <p class="page-subtitle">How we collect, use, and protect your information</p>
        
        <!-- Content Container -->
        <div class="content-container">
            <div class="content-panel">
                <p>We value your privacy. This Privacy Policy explains how <strong>AI Stock Analytics</strong> <strong>collects, uses, and protects users' personal data</strong>.</p>
                
                <h4>Data Collected</h4>
                <ul>
                    <li><strong>Registration Information</strong> (name, email, phone number if required).</li>
                    <li><strong>Application Usage Activity</strong> (stock analysis history, preferences, feedback).</li>
                    <li><strong>Technical Data</strong> (IP address, device, browser).</li>
                </ul>
                
                <h4>Use of Data</h4>
                <ul>
                    <li><strong>To provide AI-based stock analysis services.</strong></li>
                    <li><strong>To improve service quality and user experience.</strong></li>
                    <li><strong>To communicate service-related updates</strong> (notifications, updates, newsletters).</li>
                </ul>
                
                <h4>Data Protection</h4>
                <ul>
                    <li>We use <strong>encryption and industry-standard security measures</strong> to protect data.</li>
                    <li><strong>Your data is not sold to third parties.</strong></li>
                    <li>Data is only shared with third parties when necessary to deliver the service (e.g., email/SMS gateway providers) and <strong>in compliance with applicable law</strong>.</li>
                </ul>
                
                <h4>User Rights</h4>
                <ul>
                    <li>You have the right to <strong>request access, correction, or deletion</strong> of your personal data.</li>
                    <li>You may <strong>opt out of promotional communications</strong> at any time.</li>
                </ul>
                
                <p>By using AI Stock Analytics, you acknowledge that you have <strong>read, understood, and agreed</strong> to this Privacy Policy.</p>
                
                <div class="back-link">
                    <a href="/" class="btn btn-secondary">← Back to Home</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('head')
<style>
    /* Main Content */
    .main-content {
        padding: 32px;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .page-title {
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
        text-align: center;
    }
    
    .page-subtitle {
        font-size: 1.1rem;
        color: #666;
        margin-bottom: 40px;
        text-align: center;
    }
    
    /* Content Container */
    .content-container {
        max-width: 800px;
        margin: 0 auto;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    /* Content Panel */
    .content-panel {
        padding: 32px;
        line-height: 1.6;
    }
    
    .content-panel h3 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 20px;
        color: #333;
    }
    
    .content-panel h4 {
        font-size: 1.2rem;
        font-weight: 600;
        margin: 24px 0 12px 0;
        color: #333;
    }
    
    .content-panel ul {
        margin-bottom: 20px;
        padding-left: 20px;
    }
    
    .content-panel li {
        margin-bottom: 8px;
        color: #555;
    }
    
    .content-panel p {
        margin-bottom: 16px;
        color: #555;
    }
    
    .back-link {
        margin-top: 40px;
        text-align: center;
    }
    
    .btn {
        display: inline-block;
        padding: 12px 24px;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
    }
    
    @media (max-width: 768px) {
        .main-content {
            padding: 20px;
        }
        
        .content-container {
            margin: 0 16px;
        }
        
        .content-panel {
            padding: 24px;
        }
    }
</style>
@endsection