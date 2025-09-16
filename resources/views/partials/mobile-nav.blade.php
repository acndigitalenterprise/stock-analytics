<!-- Mobile Navigation -->
@php
    $isAdminPage = request()->is('stock-analytics/admin*') || request()->is('stock-analytics/setting*');
@endphp
@if($isAdminPage && session()->has('user'))
<div class="mobile-nav">
    @php
        $user = session('user');
        $isUserRole = $user && $user->role === 'user';
        $isAdminRole = $user && in_array($user->role, ['admin', 'super_admin']);
    @endphp
    
    <!-- Dashboard -->
    @if($isUserRole)
        <a href="/stock-analytics/admin" class="mobile-nav-item {{ 
            request()->is('stock-analytics/admin/dashboard*') || 
            request()->is('stock-analytics/admin') ? 'active' : '' 
        }}">
            <span>Dashboard</span>
        </a>
    @else
        <a href="/stock-analytics/admin/dashboard" class="mobile-nav-item {{ 
            request()->is('stock-analytics/admin/dashboard*') ? 'active' : '' 
        }}">
            <span>Dashboard</span>
        </a>
    @endif
    
    <!-- Requests -->
    <a href="/stock-analytics/admin/requests" class="mobile-nav-item {{ 
        request()->is('stock-analytics/admin/requests*') ? 'active' : '' 
    }}">
        <span>Requests</span>
    </a>
    
    <!-- Users (Admin/Super Admin only) -->
    @if($isAdminRole)
    <a href="/stock-analytics/admin/users" class="mobile-nav-item {{ request()->is('stock-analytics/admin/users*') ? 'active' : '' }}">
        <span>Users</span>
    </a>
    @endif
    
    <!-- Settings -->
    <a href="/stock-analytics/setting" class="mobile-nav-item {{ request()->is('stock-analytics/setting*') ? 'active' : '' }}">
        <span>Settings</span>
    </a>
</div>
@endif