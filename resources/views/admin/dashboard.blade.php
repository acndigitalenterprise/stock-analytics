@extends('layout')

@section('body-class', 'admin-layout')

@section('content')
@php $isAdminLayout = true; @endphp

<h2>Dashboard</h2>

<!-- First Row - 5 boxes horizontal -->
<div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; margin: 30px 0;">
    <!-- Box 1: Total Requests -->
    <div style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 24px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="font-size: 32px; font-weight: bold; color: #000; margin-bottom: 8px;">{{ number_format($totalRequests) }}</div>
        <div style="font-size: 14px; color: #000; font-weight: 500;">Total Requests</div>
    </div>
    
    <!-- Box 2: Total Stocks -->
    <div style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 24px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="font-size: 32px; font-weight: bold; color: #000; margin-bottom: 8px;">{{ number_format($totalStocks) }}</div>
        <div style="font-size: 14px; color: #000; font-weight: 500;">Total Stocks</div>
    </div>
    
    <!-- Box 3: Total Wins -->
    <div style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 24px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="font-size: 32px; font-weight: bold; color: #000; margin-bottom: 8px;">{{ number_format($totalWins) }}</div>
        <div style="font-size: 14px; color: #000; font-weight: 500;">Total Wins</div>
    </div>
    
    <!-- Box 4: Total Loss -->
    <div style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 24px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="font-size: 32px; font-weight: bold; color: #000; margin-bottom: 8px;">{{ number_format($totalLoss) }}</div>
        <div style="font-size: 14px; color: #000; font-weight: 500;">Total Loss</div>
    </div>
    
    <!-- Box 5: Total Timeout -->
    <div style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 24px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="font-size: 32px; font-weight: bold; color: #000; margin-bottom: 8px;">{{ number_format($totalTimeout) }}</div>
        <div style="font-size: 14px; color: #000; font-weight: 500;">Total Timeout</div>
    </div>
</div>

<!-- Second Row - 3 boxes horizontal (Only for Admin/Super Admin) -->
@php
    $user = session('user');
    $isAdminOrSuperAdmin = $user && in_array($user->role, ['admin', 'super_admin']);
@endphp

@if($isAdminOrSuperAdmin && isset($totalUsers))
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin: 30px 0;">
    <!-- Box 1: Total Users -->
    <div style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 24px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="font-size: 32px; font-weight: bold; color: #000; margin-bottom: 8px;">{{ number_format($totalUsers) }}</div>
        <div style="font-size: 14px; color: #000; font-weight: 500;">Total Users</div>
    </div>
    
    <!-- Box 2: Total Active Users -->
    <div style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 24px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="font-size: 32px; font-weight: bold; color: #000; margin-bottom: 8px;">{{ number_format($activeUsers) }}</div>
        <div style="font-size: 14px; color: #000; font-weight: 500;">Total Active Users</div>
    </div>
    
    <!-- Box 3: Total In-Active Users -->
    <div style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 24px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="font-size: 32px; font-weight: bold; color: #000; margin-bottom: 8px;">{{ number_format($inactiveUsers) }}</div>
        <div style="font-size: 14px; color: #000; font-weight: 500;">Total In-Active Users</div>
    </div>
</div>
@endif

<!-- Market Insights Section -->
@if(isset($marketInsights) && $marketInsights['success'])
<div style="margin: 40px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">Market Insights</h2>
        <span style="font-size: 12px; color: #666;">{{ $marketInsights['last_update'] ?? '' }}</span>
    </div>
    
    <!-- Market Insights Cards -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        <!-- Top Active Stocks -->
        <div style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h4 style="margin: 0 0 15px 0; color: #333; font-size: 16px;">🔥 Most Active Stocks</h4>
            @if(!empty($marketInsights['top_active']))
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="border-bottom: 1px solid #eee;">
                                <th style="text-align: left; padding: 6px 0; font-weight: 600;">#</th>
                                <th style="text-align: left; padding: 6px 0; font-weight: 600;">Stock</th>
                                <th style="text-align: right; padding: 6px 0; font-weight: 600;">Price</th>
                                <th style="text-align: right; padding: 6px 0; font-weight: 600;">Change</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_slice($marketInsights['top_active'], 0, 5) as $index => $stock)
                            <tr style="border-bottom: 1px solid #f5f5f5;">
                                <td style="padding: 8px 0;">{{ $index + 1 }}</td>
                                <td style="padding: 8px 0;">
                                    <div style="font-weight: 600;">{{ $stock['symbol'] }}</div>
                                    <div style="font-size: 11px; color: #666;">{{ $stock['name'] }}</div>
                                </td>
                                <td style="padding: 8px 0; text-align: right;">{{ number_format($stock['price'], 0) }}</td>
                                <td style="padding: 8px 0; text-align: right; color: {{ $stock['is_gaining'] ? '#28a745' : '#dc3545' }};">
                                    {{ $stock['is_gaining'] ? '+' : '' }}{{ number_format($stock['change_percent'], 1) }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="color: #666; font-style: italic; margin: 0;">No data available</p>
            @endif
        </div>

        <!-- Top Promising Stocks -->
        <div style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h4 style="margin: 0 0 15px 0; color: #333; font-size: 16px;">⭐ Most Promising Stocks</h4>
            @if(!empty($marketInsights['top_promising']))
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="border-bottom: 1px solid #eee;">
                                <th style="text-align: left; padding: 6px 0; font-weight: 600;">#</th>
                                <th style="text-align: left; padding: 6px 0; font-weight: 600;">Stock</th>
                                <th style="text-align: right; padding: 6px 0; font-weight: 600;">Price</th>
                                <th style="text-align: right; padding: 6px 0; font-weight: 600;">Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_slice($marketInsights['top_promising'], 0, 5) as $index => $stock)
                            <tr style="border-bottom: 1px solid #f5f5f5;">
                                <td style="padding: 8px 0;">{{ $index + 1 }}</td>
                                <td style="padding: 8px 0;">
                                    <div style="font-weight: 600;">{{ $stock['symbol'] }}</div>
                                    <div style="font-size: 11px; color: #666;">{{ $stock['name'] }}</div>
                                </td>
                                <td style="padding: 8px 0; text-align: right;">{{ number_format($stock['price'], 0) }}</td>
                                <td style="padding: 8px 0; text-align: right;">
                                    <span style="background: #007bff; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px;">
                                        {{ number_format($stock['promising_score'], 1) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="color: #666; font-style: italic; margin: 0;">No data available</p>
            @endif
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 15px;">
        <button onclick="refreshMarketData()" class="btn secondary" style="font-size: 12px; padding: 6px 12px;">
            🔄 Refresh Market Data
        </button>
        <small style="display: block; margin-top: 8px; color: #666; font-size: 11px;">
            Data refreshes automatically every 30 minutes
        </small>
    </div>
</div>
@elseif(isset($marketInsights) && !$marketInsights['success'])
<div style="margin: 40px 0;">
    <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 20px; text-align: center;">
        <h2 style="margin: 0 0 10px 0; color: #856404;">Market Insights</h2>
        <p style="margin: 0; color: #856404;">{{ $marketInsights['error'] ?? 'Market data temporarily unavailable' }}</p>
        <button onclick="refreshMarketData()" class="btn secondary" style="font-size: 12px; padding: 6px 12px; margin-top: 10px;">
            🔄 Try Again
        </button>
    </div>
</div>
@endif

<!-- Responsive styles for mobile -->
<style>
@media (max-width: 768px) {
    div[style*="grid-template-columns: repeat(5, 1fr)"] {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    div[style*="grid-template-columns: repeat(3, 1fr)"] {
        grid-template-columns: 1fr !important;
    }
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 480px) {
    div[style*="grid-template-columns: repeat(5, 1fr)"],
    div[style*="grid-template-columns: repeat(3, 1fr)"],
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

@endsection

@section('scripts')
<script>
function refreshMarketData() {
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = '🔄 Loading...';
    button.disabled = true;
    
    // Create a form to trigger refresh
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("stock-analytics.admin.dashboard") }}';
    form.style.display = 'none';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    
    const refreshInput = document.createElement('input');
    refreshInput.type = 'hidden';
    refreshInput.name = 'refresh_market';
    refreshInput.value = '1';
    
    form.appendChild(csrfInput);
    form.appendChild(refreshInput);
    document.body.appendChild(form);
    
    // Instead of form submission, let's just reload the page
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}
</script>
@endsection