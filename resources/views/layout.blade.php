<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stock Analytics</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            background: #f8f8f8; 
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
            color: #333; 
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
            color: #333;
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
            color: #b00; 
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
            color: #333;
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
            color: #666;
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
            color: #666;
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
            color: #666;
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
            color: #666;
            margin-bottom: 4px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .mobile-card-value {
            font-size: 0.9em;
            color: #333;
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
            color: #666;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .mobile-card-advice-content {
            font-size: 0.85em;
            color: #333;
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
            color: #333;
        }

        .advice-content strong {
            font-weight: bold;
            color: #000;
        }

        .advice-content h3 {
            font-size: 1.1em;
            font-weight: bold;
            margin: 16px 0 8px 0;
            color: #000;
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
          .main-container, .flex, .flex-between {
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
            width: 100% !important;
            min-width: 0 !important;
            border-right: none;
            border-bottom: 1px solid #ddd;
            flex-direction: row !important;
            overflow-x: auto;
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
    @include('partials.header')
    {{-- @yield('header-right') --}}
    {{-- @yield('after-header') --}}
    <!-- Main Container -->
    <div class="main-container">
        @if(isset($isAdminLayout) && $isAdminLayout)
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="sidebar-content">
                    @php
                        $user = session('user');
                        $isUserRole = $user && $user->role === 'user';
                        $isAdminRole = $user && in_array($user->role, ['admin', 'super_admin']);
                    @endphp
                    
                    @if($isUserRole)
                        <a href="/stock-analytics/admin" class="{{ 
                            request()->is('stock-analytics/admin/dashboard*') || 
                            request()->is('stock-analytics/admin') ? 'active' : '' 
                        }}">
                            Home
                        </a>
                        <a href="/stock-analytics/admin/requests" class="{{ 
                            request()->is('stock-analytics/admin/requests*') ? 'active' : '' 
                        }}">
                            Requests
                        </a>
                    @else
                        <a href="/stock-analytics/admin/dashboard" class="{{ 
                            request()->is('stock-analytics/admin/dashboard*') ? 'active' : '' 
                        }}">
                            Home
                        </a>
                        <a href="/stock-analytics/admin/requests" class="{{ 
                            request()->is('stock-analytics/admin/requests*') ? 'active' : '' 
                        }}">
                            Requests
                        </a>
                    @endif
                    @if(session('user') && in_array(session('user')->role, ['admin', 'super_admin']))
                    <a href="/stock-analytics/admin/users" class="{{ request()->is('stock-analytics/admin/users*') ? 'active' : '' }}">
                        Users
                    </a>
                    @endif
                    <div class="sidebar-menu-group">
                        <a href="#" class="{{ request()->is('stock-analytics/setting*') ? 'active' : '' }}" onclick="toggleSettingMenu(event)">
                            Setting
                        </a>
                        <div id="setting-submenu" class="sidebar-submenu" style="display: {{ request()->is('stock-analytics/setting*') ? 'block' : 'none' }};">
                            <a href="/stock-analytics/setting/profile" class="{{ request()->is('stock-analytics/setting/profile') ? 'active' : '' }}">Profile</a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Content Area -->
        <div class="content @if(isset($isAdminLayout) && $isAdminLayout) admin-layout @endif">
            @yield('content')
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="copyright">© 2025 AI Stock Analytics. All Rights Reserved.<br>Developed By ACN Digital Enterprise</div>
        </div>
    </footer>
    
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
    <script>
function toggleSettingMenu(e) {
    e.preventDefault();
    var submenu = document.getElementById('setting-submenu');
    if (submenu.style.display === 'block') {
        submenu.style.display = 'none';
    } else {
        submenu.style.display = 'block';
    }
}
</script>
</body>
</html> 