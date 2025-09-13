<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('Admin/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard/dashboard.css') }}?v={{ time() }}">
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
            <h2>Dashboard</h2>
        </div>
        <div>
            <button onclick="refreshDashboard()" class="btn users-new-btn">Refresh</button>
        </div>
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

            </div>
        </main>
    </div>
    
    @include('Components.footer')
</div>

@include('Components.admin-scripts')

</body>
</html>