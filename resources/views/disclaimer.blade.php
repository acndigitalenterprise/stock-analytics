@extends('layout')

@section('content')
    <div class="main-content">
        <h2 class="page-title">Disclaimer</h2>
        <p class="page-subtitle">Important legal information and terms of use</p>
        
        <!-- Content Container -->
        <div class="content-container">
            <div class="content-panel">
                <p>AI Stock Analytics is a platform that provides analysis and recommendations powered by artificial intelligence regarding stocks and other financial instruments. However, <strong>we are not licensed financial advisors</strong>. All information, suggestions, or recommendations displayed on this platform are general, informational, and educational in nature, and do not constitute definitive instructions to buy, sell, or hold any stock or investment instrument.</p>
                
                <p><strong>We are not responsible for any losses, damages, or other consequences</strong> arising from the use of this service. <strong>All investment risks are the sole responsibility of each user.</strong></p>
                
                <p>Users are expected to <strong>conduct independent research</strong>, <strong>consult with professional financial advisors</strong>, and <strong>consider their personal risk profiles</strong> before making investment decisions.</p>
                
                <p>By using AI Stock Analytics, you acknowledge that <strong>we cannot guarantee investment results</strong> and <strong>are not responsible for the investment decisions you make</strong>.</p>
                
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