@if(isset($isAdminLayout) && $isAdminLayout)
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-content">
            @php
                $user = session('user');
                $isUserRole = $user && $user->role === 'user';
                $isAdminRole = $user && in_array($user->role, ['admin', 'super_admin']);
            @endphp
            
            <!-- STOCKS Group -->
            <div class="sidebar-group-label">STOCKS</div>
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
            <a href="/stock-analytics/setting" class="{{ request()->is('stock-analytics/setting*') ? 'active' : '' }}">
                Setting
            </a>
            
            <!-- LIFE Group -->
            <div class="sidebar-group-label" style="margin-top: 32px;">LIFE</div>
            <a href="/life/face" class="{{ request()->is('life/face*') ? 'active' : '' }}">
                Face
            </a>
            <a href="/life/palm" class="{{ request()->is('life/palm*') ? 'active' : '' }}">
                Palm
            </a>
            <a href="/life/signature" class="{{ request()->is('life/signature*') ? 'active' : '' }}">
                Signature
            </a>
        </div>
    </div>
@endif