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
                <a href="{{ route('settings') }}" class="user-dropdown-item">
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