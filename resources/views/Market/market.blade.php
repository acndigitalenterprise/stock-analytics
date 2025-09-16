<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Market - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('Admin/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('Market/market.css') }}?v={{ time() }}">
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
                        <div class="market-section">
                            <h3>Most Active Stocks</h3>
                            
                            <!-- Desktop Table Version -->
                            <div class="table-responsive market-desktop-table">
                                <table class="table users-table">
                                <thead>
                                    <tr>
                                        <th class="market-table-number">#</th>
                                        <th class="market-table-code">Stock Code</th>
                                        <th>Company Name</th>
                                        <th class="market-table-price">Price</th>
                                        <th class="market-table-change">Change</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(array_slice($marketInsights['top_active'], 0, 10) as $index => $stock)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $stock['symbol'] }}</td>
                                        <td>{{ $stock['name'] }}</td>
                                        <td class="market-table-price">{{ number_format($stock['price'], 0) }}</td>
                                        <td class="market-table-change {{ $stock['is_gaining'] ? 'admin-stock-gain' : 'admin-stock-loss' }}">
                                            {{ $stock['is_gaining'] ? '+' : '' }}{{ number_format($stock['change_percent'], 1) }}%
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                </table>
                            </div>
                            
                            <!-- Mobile Card Version -->
                            <div class="market-mobile-cards">
                                <div class="admin-stat-box market-admin-stat-box">
                                    @foreach(array_slice($marketInsights['top_active'], 0, 10) as $index => $stock)
                                        <div class="market-mobile-item">
                                            <div class="market-mobile-row">
                                                <span class="market-mobile-number">{{ $index + 1 }}</span>
                                                <div class="market-mobile-stock">
                                                    <div class="market-mobile-code">{{ $stock['symbol'] }}</div>
                                                    <div class="market-mobile-name">{{ $stock['name'] }}</div>
                                                </div>
                                                <div class="market-mobile-price">
                                                    <span>{{ number_format($stock['price'], 0) }}</span>
                                                    <span class="market-mobile-change {{ $stock['is_gaining'] ? 'admin-stock-gain' : 'admin-stock-loss' }}">
                                                        {{ $stock['is_gaining'] ? '+' : '' }}{{ number_format($stock['change_percent'], 1) }}%
                                                    </span>
                                                </div>
                                            </div>
                                            @if(!$loop->last)
                                                <div class="market-mobile-divider"></div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Most Promising Stocks Section -->
                    @if(!empty($marketInsights['top_promising']))
                        <div class="market-section">
                            <h3>Most Promising Stocks</h3>
                            
                            <!-- Desktop Table Version -->
                            <div class="table-responsive market-desktop-table">
                                <table class="table users-table">
                                <thead>
                                    <tr>
                                        <th class="market-table-number">#</th>
                                        <th class="market-table-code">Stock Code</th>
                                        <th>Company Name</th>
                                        <th class="market-table-price">Price</th>
                                        <th class="market-table-score">Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(array_slice($marketInsights['top_promising'], 0, 10) as $index => $stock)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $stock['symbol'] }}</td>
                                        <td>{{ $stock['name'] }}</td>
                                        <td class="market-table-price">{{ number_format($stock['price'], 0) }}</td>
                                        <td class="market-table-score">
                                            <span class="{{ $stock['promising_score'] >= 6 ? 'admin-stock-gain' : 'admin-stock-loss' }}">
                                                {{ number_format($stock['promising_score'], 1) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                </table>
                            </div>
                            
                            <!-- Mobile Card Version -->
                            <div class="market-mobile-cards">
                                <div class="admin-stat-box market-admin-stat-box">
                                    @foreach(array_slice($marketInsights['top_promising'], 0, 10) as $index => $stock)
                                        <div class="market-mobile-item">
                                            <div class="market-mobile-row">
                                                <span class="market-mobile-number">{{ $index + 1 }}</span>
                                                <div class="market-mobile-stock">
                                                    <div class="market-mobile-code">{{ $stock['symbol'] }}</div>
                                                    <div class="market-mobile-name">{{ $stock['name'] }}</div>
                                                </div>
                                                <div class="market-mobile-price">
                                                    <span>{{ number_format($stock['price'], 0) }}</span>
                                                    <span class="market-mobile-change market-mobile-score {{ $stock['promising_score'] >= 6 ? 'admin-stock-gain' : 'admin-stock-loss' }}">
                                                        {{ number_format($stock['promising_score'], 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            @if(!$loop->last)
                                                <div class="market-mobile-divider"></div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="market-update-info">
                        <small class="market-update-time">
                            Last update: {{ $marketInsights['last_update'] ?? '' }}
                        </small>
                        <br>
                        <small class="market-update-note">
                            Data refreshes automatically every 30 minutes
                        </small>
                    </div>

                @elseif(isset($marketInsights) && isset($marketInsights['loading']) && $marketInsights['loading'])
                    <!-- Initial Loading State with Auto-refresh -->
                    <div id="loading-container" class="market-loading-container">
                        <div class="market-loading-text">
                            <div class="market-loading-title">🔄 Loading market data...</div>
                            <div class="market-loading-subtitle">This will take a moment for fresh data</div>
                        </div>
                        <div class="market-loading-bar-container">
                            <div id="loading-bar" class="market-loading-bar"></div>
                        </div>
                    </div>
                @elseif(isset($marketInsights) && !$marketInsights['success'])
                    <!-- Market Data Error -->
                    <div class="market-error-container">
                        <p class="market-error-text">
                            {{ $marketInsights['error'] ?? 'Market data temporarily unavailable' }}
                        </p>
                        <button onclick="refreshMarketData()" class="btn users-new-btn">
                            Try Again
                        </button>
                    </div>
                @else
                    <!-- Fallback Loading State -->
                    <div class="market-loading-container">
                        <p class="market-loading-text">Loading market data...</p>
                    </div>
                @endif
            </div>
        </main>
    </div>
    
    @include('Components.footer')
</div>

<script>
// Set market refresh URL from Laravel route
setMarketRefreshUrl('{{ route("market.refresh") }}');
</script>

<script src="{{ asset('Market/market.js') }}"></script>

</body>
</html>