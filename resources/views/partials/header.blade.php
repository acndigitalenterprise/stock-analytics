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
        color: white;
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
</style> 