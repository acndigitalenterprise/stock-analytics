<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://*.tradingview.com https://s3.tradingview.com; style-src 'self' 'unsafe-inline' https://*.tradingview.com https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; connect-src 'self'; img-src 'self' data: https:; media-src 'self'; object-src 'none'; frame-src https://*.tradingview.com;">
    <title>@yield('title', 'Stock Analytics')</title>
    <link rel="stylesheet" href="{{ asset('Admin/admin.css') }}?v={{ time() }}">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            background: #f8f8f8; 
            color: #ffffff;
        }
        
        /* Global text color override */
        * {
            color: #ffffff !important;
        }
        
        /* Exception for buttons to keep their readable colors */
        .btn, button, a.btn {
            color: white !important;
        }
        
        /* Exception for specific button types */
        .btn.secondary {
            color: #ffffff !important;
        }
        
        /* Exception for error messages to stay red */
        .error, .error-message {
            color: #dc3545 !important;
        }
        
        /* Main Container */
        .main-container {
            display: flex;
            min-height: calc(100vh - 64px);
        }
        
        /* Sidebar */
        .sidebar { 
            width: 100px; 
            background: #f5f5f5; 
            border-right: 1px solid #ddd;
            display: flex;
            flex-direction: column;
        }
        

        
        .sidebar-content { 
            padding: 24px 0; 
            flex: 1;
        }
        
        .sidebar a { 
            display: block; 
            color: #ffffff; 
            text-decoration: none; 
            padding: 12px 24px; 
            margin-bottom: 4px;
            transition: background-color 0.2s;
        }
        
        .sidebar a.active, .sidebar a:hover { 
            background: #e0e0e0; 
            color: #222;
        }
        
        .sidebar-signout-link {
            margin-top: 20px !important;
            color: #b00 !important;
        }
        
        .sidebar-signout-link:hover {
            background: #ffebee !important;
            color: #900 !important;
        }
        
        .sidebar-signout { 
            padding: 24px; 
            border-top: 1px solid #ddd; 
            background: #fafafa;
        }
        
        .sidebar-signout .btn { 
            width: 100%; 
            background: #b00; 
            color: #fff; 
            border: none; 
            padding: 10px 16px; 
            cursor: pointer; 
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        
        .sidebar-signout .btn:hover { 
            background: #900; 
        }
        
        /* Content Area */
        .content { 
            flex: 1;
            padding: 32px 24px; 
            background: #fff;
        }
        
        .admin-layout .content { 
            margin-left: 0; 
        }
        
        /* Buttons */
        .btn { 
            background: #222; 
            color: #fff; 
            border: none; 
            padding: 8px 16px; 
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.2s;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 400;
        }
        
        .btn:hover {
            background: #444;
        }
        
        .btn.secondary { 
            background: #888; 
            text-decoration: none;
        }
        
        .btn.secondary:hover {
            background: #666;
        }
        
        /* Forms */
        .form-group { 
            margin-bottom: 16px; 
        }
        
        label { 
            display: block; 
            margin-bottom: 6px; 
            font-weight: bold;
            color: #ffffff;
        }
        
        input, select, textarea { 
            width: 100%; 
            padding: 8px; 
            border: 1px solid #ccc; 
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #2196F3;
            box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.2);
        }
        
        .error { 
            font-size: 0.95em; 
        }
        
        /* Table */
        .table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 24px;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .table th, .table td { 
            border: 1px solid #eee; 
            padding: 12px 8px; 
            text-align: left; 
        }
        
        .table th { 
            background: #f8f8f8; 
            font-weight: bold;
            color: #ffffff;
        }
        
        .table tr:hover {
            background: #f9f9f9;
        }
        
        /* Utilities */
        .flex { 
            display: flex; 
            align-items: center; 
        }
        
        .flex-between { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        
        /* Popup */
        .popup { 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100vw; 
            height: 100vh; 
            background: rgba(0,0,0,0.3); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            z-index: 1000; 
        }
        
        .popup-content { 
            background: #fff; 
            padding: 32px; 
            border-radius: 8px; 
            min-width: 320px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        }
        
        /* Sortable Headers */
        .sortable {
            cursor: pointer;
            user-select: none;
            position: relative;
            padding-right: 20px;
        }
        
        .sortable:hover {
            background-color: #e8e8e8;
        }
        
        .sort-indicator {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            font-weight: bold;
            color: #2196F3;
        }
        
        /* Loading and No Results */
        .loading-indicator {
            text-align: center;
            padding: 10px;
            color: #cccccc;
            font-style: italic;
        }
        
        .no-results {
            text-align: center;
            padding: 10px;
            color: #999;
            font-style: italic;
        }

        /* Consolidated Button Styles */
        .btn-primary {
            background: #333;
            color: white;
        }
        
        .btn-primary:hover {
            background: #555;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }

        /* Consolidated Autocomplete Styles */
        .autocomplete-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s;
        }
        
        .autocomplete-item:hover {
            background-color: #f5f5f5;
        }
        
        .autocomplete-item.selected {
            background-color: #e3f2fd;
        }
        
        .autocomplete-item .stock-code {
            font-weight: bold;
            color: #2196F3;
        }
        
        .autocomplete-item .stock-name {
            color: #cccccc;
            font-size: 0.9em;
        }

        /* Message Styles */
        .success-message {
            color: #28a745;
            font-size: 14px;
            margin-top: 5px;
            transition: opacity 0.3s ease;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
            transition: opacity 0.3s ease;
        }

        .admin-content-container { padding: 0 32px; }
        .admin-filter-bar-inner { display: flex; gap: 16px; align-items: center; margin-bottom: 24px; }
        .admin-filter-actions { display: flex; gap: 8px; align-items: center; justify-content: flex-end; }
        .btn, .btn.secondary { text-align: center; }
        .hide-mobile { display: table-cell; }
        .table .btn, .table a.btn, .table button.btn { display: inline-block; vertical-align: middle; font-size: 0.9em; padding: 4px 8px; line-height: 1.2; min-width: 64px; }

        /* Mobile Card Layout Styles */
        .mobile-card {
            display: none;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .mobile-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }

        .mobile-card-date {
            font-size: 0.85em;
            color: #cccccc;
            font-weight: bold;
        }

        .mobile-card-actions {
            display: flex;
            gap: 8px;
        }

        .mobile-card-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }

        .mobile-card-item {
            display: flex;
            flex-direction: column;
        }

        .mobile-card-label {
            font-size: 0.75em;
            color: #cccccc;
            margin-bottom: 4px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .mobile-card-value {
            font-size: 0.9em;
            color: #ffffff;
            font-weight: 500;
        }

        .mobile-card-advice {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 12px;
        }

        .mobile-card-advice-label {
            font-size: 0.8em;
            color: #cccccc;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .mobile-card-advice-content {
            font-size: 0.85em;
            color: #ffffff;
            line-height: 1.4;
            max-height: 80px;
            overflow: hidden;
            position: relative;
        }

        .mobile-card-advice-content.expanded {
            max-height: none;
        }

        .mobile-card-advice-toggle {
            color: #007bff;
            font-size: 0.8em;
            cursor: pointer;
            margin-top: 8px;
            text-decoration: underline;
        }

        /* Markdown Rendering Styles */
        .advice-content {
            line-height: 1.6;
            color: #ffffff;
        }

        .advice-content strong {
            font-weight: bold;
            color: #ffffff;
        }

        .advice-content h3 {
            font-size: 1.1em;
            font-weight: bold;
            margin: 16px 0 8px 0;
            color: #ffffff;
            border-bottom: 1px solid #eee;
            padding-bottom: 4px;
        }

        .advice-content ul {
            margin: 8px 0;
            padding-left: 20px;
        }

        .advice-content li {
            margin: 4px 0;
        }

        .advice-content ol {
            margin: 8px 0;
            padding-left: 20px;
        }

        .advice-content ol li {
            margin: 4px 0;
        }

        /* Message styles */
        .message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 16px;
            border-radius: 6px;
            color: white;
            font-weight: bold;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .message.success {
            background-color: #28a745;
        }

        .message.info {
            background-color: #17a2b8;
        }

        .message.error {
            background-color: #dc3545;
        }

        @media (max-width: 768px) {
          .main-container {
            display: block !important;
          }
          .flex, .flex-between {
            flex-direction: column !important;
            align-items: stretch !important;
          }
          .form-group, .btn, input, select, textarea {
            width: 100% !important;
            box-sizing: border-box;
          }
          .content {
            padding: 16px 4px;
            margin-left: 0 !important;
          }
          .sidebar {
            display: none !important;
          }
          .sidebar-content, .sidebar-signout {
            padding: 8px 0;
          }
          
          /* Hide table on mobile, show cards */
          .table {
            display: none !important;
          }
          
          .mobile-card {
            display: block !important;
          }
          
          .admin-content-container { padding: 0 16px !important; }
          .admin-filter-bar-inner { flex-direction: column !important; gap: 12px !important; align-items: stretch !important; }
          .admin-content-container form > div { flex-direction: column !important; gap: 12px !important; align-items: stretch !important; }
          .admin-filter-actions { flex-direction: row !important; justify-content: flex-end !important; }
          .hide-mobile { display: none !important; }
        }

        /* Footer Styles */
        .footer {
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 20px 0;
            margin-top: 40px;
        }

        .footer-content {
            text-align: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .copyright {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 5px;
            line-height: 1.5;
        }
        
        /* Mobile Navigation Styles */
        .mobile-nav {
            display: none !important; /* Force hide on desktop */
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #ddd;
            padding: 8px 0;
            z-index: 1000;
            justify-content: space-around;
            align-items: center;
        }
        
        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #cccccc !important;
            padding: 8px 4px;
            border-radius: 4px;
            min-width: 60px;
            transition: all 0.2s;
        }
        
        .mobile-nav-item span:first-child {
            font-size: 18px;
            margin-bottom: 2px;
        }
        
        .mobile-nav-item span:last-child {
            font-size: 10px;
            font-weight: 500;
        }
        
        .mobile-nav-item.active {
            color: #007bff !important;
            background: #f0f8ff;
        }
        
        .mobile-nav-item:hover {
            color: #007bff !important;
            background: #f8f9fa;
        }

        /* Mobile Footer Styles */
        @media (max-width: 768px) {
            .mobile-nav {
                display: flex !important;
            }
            
            .footer {
                padding: 16px 0 80px 0; /* Add bottom padding for mobile nav */
                margin-top: 20px;
            }
            .footer-content {
                padding: 0 16px;
            }
            .copyright {
                font-size: 12px;
                line-height: 1.6;
            }
        }

        .powered-by {
            font-size: 12px;
            color: #6c757d;
            font-weight: 500;
        }

        /* Pagination Styles */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding: 15px 0;
            border-top: 1px solid #e9ecef;
            flex-wrap: wrap;
            gap: 10px;
        }

        .pagination-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 120px;
        }

        .pagination-left label {
            font-size: 14px;
            color: #6c757d;
            font-weight: 500;
            white-space: nowrap;
        }

        .pagination-left select {
            padding: 5px 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
            background-color: white;
            min-width: 60px;
        }

        .pagination-right {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .pagination-info {
            font-size: 14px;
            color: #6c757d;
            white-space: nowrap;
        }

        .pagination-buttons {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .pagination-btn {
            padding: 8px 16px;
            border: 1px solid #ced4da;
            background-color: white;
            color: #495057;
            cursor: pointer;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            min-width: 44px;
            text-align: center;
            transition: all 0.2s ease;
        }

        .pagination-btn:hover:not(:disabled) {
            background-color: #e9ecef;
            border-color: #adb5bd;
        }

        .pagination-btn:disabled {
            background-color: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Mobile Pagination Adjustments */
        @media (max-width: 768px) {
            .pagination-container {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }
            
            .pagination-left {
                justify-content: center;
            }
            
            .pagination-right {
                justify-content: center;
                flex-direction: column;
                gap: 10px;
            }
            
            .pagination-info {
                text-align: center;
            }
            
            .pagination-buttons {
                justify-content: center;
            }
        }

        /* Admin table custom styles */
        .admin-layout .table {
            table-layout: fixed;
            width: 100%;
        }

        .admin-layout .table th, .admin-layout .table td {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            vertical-align: top;
        }

        /* Override white-space for Advice column to allow wrapping */
        .admin-layout .table td:nth-last-child(3) {
            white-space: normal;
            word-wrap: break-word;
        }

        /* Override for Advice column in admin/index (different position) */
        .admin-layout .table td:nth-last-child(2) {
            white-space: normal;
            word-wrap: break-word;
        }
    </style>
    
    <!-- Meta tags for sorting functionality -->
    <meta name="current-sort" content="{{ request('sort', '') }}">
    <meta name="current-order" content="{{ request('order', 'desc') }}">
    
    @yield('head')
</head>
<body class="@yield('body-class')">
    @php
        $isAdminPage = request()->is('stock-analytics/admin*') || request()->is('stock-analytics/setting*');
    @endphp
    
    @if($isAdminPage && session()->has('user'))
        <!-- MOBILE ADMIN LAYOUT -->
        <div class="mobile-admin-container">
            <!-- Mobile Header -->
            <header class="mobile-admin-header">
                <button class="mobile-menu-toggle" onclick="toggleMobileSidebar()">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1 class="mobile-header-title">Ticker AI</h1>
            </header>
            
            <!-- Mobile Sidebar Overlay -->
            <div class="mobile-sidebar-overlay" onclick="closeMobileSidebar()"></div>
            
            <!-- Mobile Sidebar -->
            <nav class="mobile-sidebar" id="mobileSidebar">
                <div class="mobile-sidebar-header">
                    <h3>Navigation</h3>
                    <button class="mobile-sidebar-close" onclick="closeMobileSidebar()">&times;</button>
                </div>
                <div class="mobile-sidebar-menu">
                    @php
                        $user = session('user');
                        $isUserRole = $user && $user->role === 'user';
                        $isAdminRole = $user && in_array($user->role, ['admin', 'super_admin']);
                    @endphp
                    
                    <!-- Stock Group Menu -->
                    <div class="mobile-menu-group">
                        <div class="mobile-menu-group-title">Stocks</div>
                        
                        @if($isUserRole)
                            <a href="/stock-analytics/admin" class="mobile-sidebar-link mobile-submenu-link {{ 
                                request()->is('stock-analytics/admin/dashboard*') || 
                                request()->is('stock-analytics/admin') ? 'active' : '' 
                            }}">
                                Dashboard
                            </a>
                        @else
                            <a href="/stock-analytics/admin/dashboard" class="mobile-sidebar-link mobile-submenu-link {{ 
                                request()->is('stock-analytics/admin/dashboard*') ? 'active' : '' 
                            }}">
                                Dashboard
                            </a>
                        @endif
                        
                        <a href="/stock-analytics/admin/requests" class="mobile-sidebar-link mobile-submenu-link {{ 
                            request()->is('stock-analytics/admin/requests*') ? 'active' : '' 
                        }}">
                            Requests
                        </a>
                        
                        @if($isAdminRole)
                        <a href="/stock-analytics/admin/users" class="mobile-sidebar-link mobile-submenu-link {{ request()->is('stock-analytics/admin/users*') ? 'active' : '' }}">
                            Users
                        </a>
                        @endif
                    </div>
                    
                    <!-- Other Menu Items -->
                    <a href="/stock-analytics/setting" class="mobile-sidebar-link {{ request()->is('stock-analytics/setting*') ? 'active' : '' }}">
                        Setting
                    </a>
                    
                    <!-- Sign Out -->
                    <form action="{{ route('auth.logout') }}" method="POST" class="mobile-signout-form">
                        @csrf
                        <button type="submit" class="mobile-sidebar-link mobile-signout-btn">
                            Sign Out
                        </button>
                    </form>
                </div>
            </nav>

            <!-- Mobile Content -->
            <main class="mobile-admin-content">
                @yield('content')
            </main>
            
            <!-- Mobile Footer -->
            <footer class="mobile-footer">
                <div class="mobile-footer-content">
                    &copy; {{ date('Y') }} AI Insights - AI Stock Analytics. All rights reserved.
                </div>
            </footer>
        </div>
        
        <!-- DESKTOP ADMIN LAYOUT -->
        <div class="admin-layout-container">
                <!-- Admin Header -->
                <header class="admin-header">
                    <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <h1 class="admin-header-title">Ticker AI</h1>
                    <div class="admin-header-center">
                    </div>
                    <div class="admin-header-actions">
                        <div class="user-dropdown">
                            <button class="user-dropdown-btn" onclick="toggleUserDropdown()">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="12" cy="7" r="4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <div class="user-dropdown-menu" id="userDropdownMenu">
                                <a href="/stock-analytics/setting" class="user-dropdown-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                                        <path d="M12 1v6m0 6v6m11-7h-6m-6 0H1" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    Setting
                                </a>
                                <form action="{{ route('auth.logout') }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="user-dropdown-item">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <polyline points="16,17 21,12 16,7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <line x1="21" y1="12" x2="9" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>
                
                <!-- Mobile Sidebar Overlay -->
                <div class="mobile-sidebar-overlay" onclick="closeMobileMenu()"></div>
                
                <!-- Main Content Area -->
                <div class="admin-main-content">
                    <!-- Admin Sidebar -->
                    <nav class="admin-sidebar" id="adminSidebar">
                        <div class="admin-sidebar-menu">
                            @php
                                $user = session('user');
                                $isUserRole = $user && $user->role === 'user';
                                $isAdminRole = $user && in_array($user->role, ['admin', 'super_admin']);
                            @endphp
                            
                            <!-- Stocks Group -->
                            <div class="admin-menu-group">
                                <div class="admin-menu-group-title">Stocks</div>
                                <div class="admin-menu-group-items">
                                    @if($isUserRole)
                                        <a href="/stock-analytics/admin" class="admin-menu-item {{ 
                                            request()->is('stock-analytics/admin/dashboard*') || 
                                            request()->is('stock-analytics/admin') ? 'active' : '' 
                                        }}">
                                            Dashboard
                                        </a>
                                    @else
                                        <a href="/stock-analytics/admin/dashboard" class="admin-menu-item {{ 
                                            request()->is('stock-analytics/admin/dashboard*') ? 'active' : '' 
                                        }}">
                                            Dashboard
                                        </a>
                                    @endif
                                    <a href="{{ route('market.index') }}" class="admin-menu-item {{ 
                                        request()->is('market*') ? 'active' : '' 
                                    }}">
                                        Market
                                    </a>
                                    <a href="/stock-analytics/admin/requests" class="admin-menu-item {{ 
                                        request()->is('stock-analytics/admin/requests*') ? 'active' : '' 
                                    }}">
                                        Requests
                                    </a>
                                    @if($isAdminRole)
                                    <a href="/stock-analytics/admin/users" class="admin-menu-item {{ request()->is('stock-analytics/admin/users*') ? 'active' : '' }}">
                                        Users
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </nav>
                    
                    <!-- Admin Body Content -->
                    <main class="admin-body">
                        @yield('content')
                    </main>
                </div>
                
                <!-- Admin Footer -->
                <footer class="admin-footer">
                    <div class="admin-footer-content">
                        &copy; {{ date('Y') }} AI Insights - AI Stock Analytics. All rights reserved.
                    </div>
                </footer>
        </div>
    @else
        <!-- Default Layout for non-admin pages -->
        @include('Partials.header')
        <div class="main-container">
            @include('Partials.sidebar')
            <div class="content @if(isset($isAdminLayout) && $isAdminLayout) admin-layout @endif">
                @yield('content')
            </div>
        </div>
        @include('Partials.footer')
    @endif
    
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
    
    <!-- Mobile Layout Detection Script -->
    <script>
    function initMobileLayout() {
        const isMobile = window.innerWidth <= 768 || 
                        /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        
        const mobileContainer = document.querySelector('.mobile-admin-container');
        const desktopContainer = document.querySelector('.admin-layout-container');
        
        if (mobileContainer && desktopContainer) {
            if (isMobile || window.innerWidth <= 768) {
                // Force mobile layout
                mobileContainer.style.display = 'flex';
                mobileContainer.style.flexDirection = 'column';
                mobileContainer.style.minHeight = '100vh';
                desktopContainer.style.display = 'none';
                document.body.classList.add('mobile-layout-active');
            } else {
                // Force desktop layout  
                mobileContainer.style.display = 'none';
                desktopContainer.style.display = 'flex';
                desktopContainer.style.flexDirection = 'column';
                desktopContainer.style.minHeight = '100vh';
                document.body.classList.remove('mobile-layout-active');
            }
        }
    }
    
    // Optimized: Only run once, use CSS for responsive behavior
    </script>
    
    <script>
    // Sidebar Accordion Functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Debug: Check if elements exist
        const groupTitles = document.querySelectorAll('.admin-menu-group-title');
        const groupItems = document.querySelectorAll('.admin-menu-group-items');
        
        console.log('Group titles found:', groupTitles.length);
        console.log('Group items found:', groupItems.length);
        
        if (groupTitles.length === 0) {
            console.log('No group titles found, sidebar accordion not initialized');
            return;
        }
        
        // Set default state - Stocks open, Life closed
        groupTitles.forEach(function(title, index) {
            const items = title.nextElementSibling;
            
            if (index === 0) {
                // First group (Stocks) - open
                title.classList.remove('collapsed');
                if (items) items.classList.remove('collapsed');
            } else {
                // Other groups - closed
                title.classList.add('collapsed');
                if (items) items.classList.add('collapsed');
            }
        });
        
        // Add click handlers
        groupTitles.forEach(function(title) {
            title.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Group clicked:', this.textContent);
                
                const currentItems = this.nextElementSibling;
                const isCurrentlyOpen = !this.classList.contains('collapsed');
                
                // Close all groups first
                groupTitles.forEach(function(otherTitle) {
                    const otherItems = otherTitle.nextElementSibling;
                    
                    otherTitle.classList.add('collapsed');
                    if (otherItems) otherItems.classList.add('collapsed');
                });
                
                // If the clicked group was closed, open it
                if (!isCurrentlyOpen) {
                    this.classList.remove('collapsed');
                    if (currentItems) currentItems.classList.remove('collapsed');
                }
            });
        });
    });
    </script>
    
    <script>
    // User Dropdown Functionality
    function toggleUserDropdown() {
        const dropdown = document.getElementById('userDropdownMenu');
        dropdown.classList.toggle('show');
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('userDropdownMenu');
        const button = document.querySelector('.user-dropdown-btn');
        
        if (!dropdown.contains(event.target) && !button.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    });
    
    // Close dropdown when pressing Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const dropdown = document.getElementById('userDropdownMenu');
            dropdown.classList.remove('show');
        }
    });
    
    // Mobile Menu Functionality
    function toggleMobileMenu() {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.querySelector('.mobile-sidebar-overlay');
        
        sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('active');
    }
    
    function closeMobileMenu() {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.querySelector('.mobile-sidebar-overlay');
        
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    }
    
    // Mobile Sidebar Functionality (for mobile layout)
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('mobileSidebar');
        const overlay = document.querySelector('.mobile-sidebar-overlay');
        
        if (sidebar && overlay) {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }
    }
    
    function closeMobileSidebar() {
        const sidebar = document.getElementById('mobileSidebar');
        const overlay = document.querySelector('.mobile-sidebar-overlay');
        
        if (sidebar && overlay) {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        }
    }
    
    // Close mobile menu when window is resized to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            closeMobileMenu();
        }
    });
    </script>
    
    <script>
// Page titles management
document.addEventListener('DOMContentLoaded', function() {
    const path = window.location.pathname;
    let title = 'Stock Analytics';
    
    if (path.includes('/admin/users')) {
        title = 'Users - Stock Analytics';
    } else if (path.includes('/admin/requests') || path.includes('/admin/stocks')) {
        title = 'Requests - Stock Analytics';
    } else if (path.includes('/admin')) {
        title = 'Dashboard - Stock Analytics';
    } else if (path.includes('/setting')) {
        title = 'Setting - Stock Analytics';
    }
    
    document.title = title;
});
</script>
</body>
</html> 