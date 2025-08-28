@extends('layout')

@section('body-class', 'admin-layout')

@section('content')
<link rel="stylesheet" href="{{ asset('Admin/admin.css') }}">
<link rel="stylesheet" href="{{ asset('Users/users.css') }}?v={{ time() }}">
<link rel="stylesheet" href="{{ asset('Requests/requests.css') }}?v={{ time() }}">

<div class="admin-content-container">
    @php $isAdminLayout = true; @endphp
    
    <div class="users-flex-between">
        <div>
            <h2>Dashboard</h2>
        </div>
        <div></div>
    </div>

    <!-- First Row - 5 boxes horizontal -->
    <div class="admin-stats-grid-5">
        <!-- Box 1: Requests -->
        <div class="admin-stat-box">
            <div class="admin-stat-value">{{ number_format($totalRequests) }}</div>
            <div class="admin-stat-label">Requests</div>
        </div>
        
        <!-- Box 2: Stocks -->
        <div class="admin-stat-box">
            <div class="admin-stat-value">{{ number_format($totalStocks) }}</div>
            <div class="admin-stat-label">Stocks</div>
        </div>
        
        <!-- Box 3: Wins -->
        <div class="admin-stat-box">
            <div class="admin-stat-value">{{ number_format($totalWins) }}</div>
            <div class="admin-stat-label">Wins</div>
        </div>
        
        <!-- Box 4: Loss -->
        <div class="admin-stat-box">
            <div class="admin-stat-value">{{ number_format($totalLoss) }}</div>
            <div class="admin-stat-label">Loss</div>
        </div>
        
        <!-- Box 5: Timeout -->
        <div class="admin-stat-box">
            <div class="admin-stat-value">{{ number_format($totalTimeout) }}</div>
            <div class="admin-stat-label">Timeout</div>
        </div>
    </div>

    <!-- Second Row - 3 boxes horizontal (Only for Admin/Super Admin) -->
    @php
        $user = session('user');
        $isAdminOrSuperAdmin = $user && in_array($user->role, ['admin', 'super_admin']);
    @endphp

    @if($isAdminOrSuperAdmin && isset($totalUsers))
    <div class="admin-stats-grid-3">
        <!-- Box 1: Users -->
        <div class="admin-stat-box">
            <div class="admin-stat-value">{{ number_format($totalUsers) }}</div>
            <div class="admin-stat-label">Users</div>
        </div>
        
        <!-- Box 2: Active Users -->
        <div class="admin-stat-box">
            <div class="admin-stat-value">{{ number_format($activeUsers) }}</div>
            <div class="admin-stat-label">Active Users</div>
        </div>
        
        <!-- Box 3: In-Active Users -->
        <div class="admin-stat-box">
            <div class="admin-stat-value">{{ number_format($inactiveUsers) }}</div>
            <div class="admin-stat-label">In-Active Users</div>
        </div>
    </div>
    @endif
</div>

    <!-- Market Insights Section -->
    @if(isset($marketInsights) && $marketInsights['success'])
    <!-- Market Insights Box -->
    <div class="admin-content-card" style="margin-top: 40px; border: none !important; box-shadow: none !important;">
        <div class="users-flex-between">
            <div>
                <h2>Market Insights</h2>
            </div>
            <div>
                <span style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem;">{{ $marketInsights['last_update'] ?? '' }}</span>
            </div>
        </div>
        
        <!-- Most Active Stocks Table -->
        @if(!empty($marketInsights['top_active']))
            <div style="margin-top: 32px;">
                <h3 style="color: white; font-size: 1.5rem; font-weight: 600; margin-bottom: 20px;">Most Active Stocks</h3>
                <div class="table-responsive">
                    <table class="table requests-table">
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
                        @foreach(array_slice($marketInsights['top_active'], 0, 5) as $index => $stock)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="font-weight: 600; color: #fff;">{{ $stock['symbol'] }}</td>
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
        
        <!-- Most Promising Stocks Table -->
        @if(!empty($marketInsights['top_promising']))
            <div style="margin-top: 32px;">
                <h3 style="color: white; font-size: 1.5rem; font-weight: 600; margin-bottom: 20px;">Most Promising Stocks</h3>
                <div class="table-responsive">
                    <table class="table requests-table">
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
                        @foreach(array_slice($marketInsights['top_promising'], 0, 5) as $index => $stock)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="font-weight: 600; color: #fff;">{{ $stock['symbol'] }}</td>
                            <td>{{ $stock['name'] }}</td>
                            <td style="text-align: right;">{{ number_format($stock['price'], 0) }}</td>
                            <td style="font-weight: 600; color: white; text-align: right;">
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
            <button onclick="refreshMarketData()" class="admin-logout-btn">
                Refresh
            </button>
            <br>
            <small style="color: white !important; margin-top: 12px; display: block;">
                Data refreshes automatically every 30 minutes
            </small>
        </div>
    </div>
    @elseif(isset($marketInsights) && !$marketInsights['success'])
    <!-- Market Insights Error Box/Card -->
    <div class="admin-content-card" style="margin-top: 40px; border: none !important; box-shadow: none !important;">
        <h2>Market Insights</h2>
        <p style="color: rgba(255, 255, 255, 0.8); text-align: center; margin: 24px 0;">
            {{ $marketInsights['error'] ?? 'Market data temporarily unavailable' }}
        </p>
        <div style="text-align: center;">
            <button onclick="refreshMarketData()" class="admin-logout-btn">
                Try Again
            </button>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="{{ asset('Dashboard/dashboard.js') }}"></script>
@endsection