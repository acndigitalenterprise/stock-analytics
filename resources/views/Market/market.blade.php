<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Market - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('Admin/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('page-market/market.css') }}?v={{ time() }}">
</head>
<body class="admin-layout">

<!-- DESKTOP ADMIN LAYOUT -->
<div class="admin-layout-container">
    @include('Components.header')
    
    <!-- Mobile Sidebar Overlay -->
    <div class="mobile-sidebar-overlay" onclick="closeMobileMenu()"></div>
    
    <!-- Main Content Area -->
    <div class="admin-main-content">
        @include('Components.sidebar')
        
        <!-- Admin Body Content -->
        <main class="admin-body">
            <div class="admin-content-container">
                <div class="users-flex-between">
                    <div>
                        <h2>Market</h2>
                    </div>
                    <div>
                        <button onclick="refreshMarketData()" class="btn users-new-btn">Refresh</button>
                    </div>
                </div>

                @if(isset($marketInsights) && $marketInsights['success'])
                    <!-- Most Active Stocks Section -->
                    @if(!empty($marketInsights['top_active']))
                        <div style="margin-top: 32px;">
                            <h3>Most Active Stocks</h3>
                            <div class="table-responsive">
                                <table class="table users-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th style="width: 120px;">Stock Code</th>
                                        <th>Company Name</th>
                                        <th style="width: 100px; text-align: right;">Price</th>
                                        <th style="width: 100px; text-align: right;">Change</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(array_slice($marketInsights['top_active'], 0, 10) as $index => $stock)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $stock['symbol'] }}</td>
                                        <td>{{ $stock['name'] }}</td>
                                        <td style="text-align: right;">{{ number_format($stock['price'], 0) }}</td>
                                        <td class="{{ $stock['is_gaining'] ? 'admin-stock-gain' : 'admin-stock-loss' }}" style="text-align: right;">
                                            {{ $stock['is_gaining'] ? '+' : '' }}{{ number_format($stock['change_percent'], 1) }}%
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Most Promising Stocks Section -->
                    @if(!empty($marketInsights['top_promising']))
                        <div style="margin-top: 32px;">
                            <h3>Most Promising Stocks</h3>
                            <div class="table-responsive">
                                <table class="table users-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th style="width: 120px;">Stock Code</th>
                                        <th>Company Name</th>
                                        <th style="width: 100px; text-align: right;">Price</th>
                                        <th style="width: 100px; text-align: right;">Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(array_slice($marketInsights['top_promising'], 0, 10) as $index => $stock)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $stock['symbol'] }}</td>
                                        <td>{{ $stock['name'] }}</td>
                                        <td style="text-align: right;">{{ number_format($stock['price'], 0) }}</td>
                                        <td style="text-align: right;">
                                            {{ number_format($stock['promising_score'], 1) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div style="text-align: center; margin-top: 24px;">
                        <small style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem;">
                            Last update: {{ $marketInsights['last_update'] ?? '' }}
                        </small>
                        <br>
                        <small style="margin-top: 12px; display: block;">
                            Data refreshes automatically every 30 minutes
                        </small>
                    </div>

                @elseif(isset($marketInsights) && !$marketInsights['success'])
                    <!-- Market Data Error -->
                    <div style="text-align: center; margin-top: 64px;">
                        <p style="color: rgba(255, 255, 255, 0.8); margin: 24px 0;">
                            {{ $marketInsights['error'] ?? 'Market data temporarily unavailable' }}
                        </p>
                        <button onclick="refreshMarketData()" class="btn users-new-btn">
                            Try Again
                        </button>
                    </div>
                @else
                    <!-- Loading State -->
                    <div style="text-align: center; margin-top: 64px;">
                        <p style="color: rgba(255, 255, 255, 0.8);">Loading market data...</p>
                    </div>
                @endif
            </div>
        </main>
    </div>
    
    @include('Components.footer')
</div>

@include('Components.admin-scripts')

<script>
// Set market refresh URL for JavaScript
window.marketRefreshUrl = '{{ route("market.refresh") }}';
</script>
<script src="{{ asset('page-market/market.js') }}"></script>

</body>
</html>