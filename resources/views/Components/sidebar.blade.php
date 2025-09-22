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
                    <a href="{{ route('dashboard') }}" class="admin-menu-item {{ 
                        request()->is('dashboard*') ? 'active' : '' 
                    }}">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="admin-menu-item {{ 
                        request()->is('dashboard*') ? 'active' : '' 
                    }}">
                        Dashboard
                    </a>
                @endif
                <a href="{{ route('market.index') }}" class="admin-menu-item {{
                    request()->is('market*') ? 'active' : ''
                }}">
                    Market
                </a>
                <a href="{{ route('signals.index') }}" class="admin-menu-item {{
                    request()->is('signals*') ? 'active' : ''
                }}">
                    Signals
                </a>
                <a href="{{ route('requests.index') }}" class="admin-menu-item {{
                    request()->is('requests*') ? 'active' : ''
                }}">
                    Requests
                </a>
                @if($isAdminRole)
                <a href="{{ route('users.index') }}" class="admin-menu-item {{ request()->is('users*') ? 'active' : '' }}">
                    Users
                </a>
                @endif
            </div>
        </div>
    </div>
</nav>