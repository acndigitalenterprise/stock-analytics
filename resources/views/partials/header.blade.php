<!-- Header -->
<div class="header">
    <h1>AI Stock Analytics</h1>
    @if(session()->has('user'))
        <form action="{{ route('stock-analytics.logout') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="btn">Sign Out</button>
        </form>
    @else
        <div></div> <!-- Empty div for spacing when not logged in -->
    @endif
</div>

<style>
    /* Header Styles */
    .header {
        background: white;
        border-bottom: 1px solid #000;
        padding: 24px 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .header h1 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
    }
    
    .header .btn {
        background: #333;
        color: white !important;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    
    .header .btn:hover {
        background: #555;
    }
    
    /* Specific styles for header auth buttons */
    .header-signin-btn {
        background: #007bff !important;
        color: #ffffff !important;
        border: 2px solid #007bff !important;
        padding: 8px 16px !important;
        border-radius: 4px !important;
        font-size: 14px !important;
        cursor: pointer !important;
        text-decoration: none !important;
        display: inline-block !important;
        font-weight: bold !important;
    }
    
    .header-signup-btn {
        background: #ffffff !important;
        color: #007bff !important;
        border: 2px solid #007bff !important;
        padding: 8px 16px !important;
        border-radius: 4px !important;
        font-size: 14px !important;
        cursor: pointer !important;
        text-decoration: none !important;
        display: inline-block !important;
        font-weight: bold !important;
    }
    
    .header-signin-btn:hover {
        background: #555 !important;
    }
    
    .header-signup-btn:hover {
        background: #888 !important;
    }
    
    /* Mobile Header Styles */
    @media (max-width: 768px) {
        .header {
            padding: 16px 20px;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            gap: 10px;
        }
        
        .header h1 {
            font-size: 1.2rem;
            margin: 0;
            order: 1;
        }
        
        .header form {
            order: 2;
        }
        
        .header .btn {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .header div {
            gap: 4px !important;
        }
    }
</style> 