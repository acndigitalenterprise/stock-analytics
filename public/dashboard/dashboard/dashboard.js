/**
 * =================================
 * DASHBOARD PAGE JAVASCRIPT
 * =================================
 */

/**
 * =================================
 * ADMIN FUNCTIONALITY
 * =================================
 */

/**
 * User Dropdown Functionality
 */
function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdownMenu');
    dropdown.classList.toggle('show');
}

/**
 * Mobile Menu Functionality
 */
function toggleMobileMenu() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.querySelector('.mobile-sidebar-overlay');
    
    if (sidebar) sidebar.classList.toggle('mobile-open');
    if (overlay) overlay.classList.toggle('active');
}

function closeMobileMenu() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.querySelector('.mobile-sidebar-overlay');
    
    if (sidebar) sidebar.classList.remove('mobile-open');
    if (overlay) overlay.classList.remove('active');
}

/**
 * =================================
 * INITIALIZATION
 * =================================
 */

/**
 * Initialize Dashboard Page
 */
function initDashboardPage() {
    initUserDropdown();
    initSidebarAccordion();
    initMobileMenu();
    console.log('✅ Dashboard page initialized');
}

/**
 * Initialize User Dropdown Events
 */
function initUserDropdown() {
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('userDropdownMenu');
        const button = document.querySelector('.user-dropdown-btn');
        
        if (dropdown && button && !dropdown.contains(event.target) && !button.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    });

    // Close dropdown when pressing Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const dropdown = document.getElementById('userDropdownMenu');
            if (dropdown) dropdown.classList.remove('show');
        }
    });
}

/**
 * Initialize Sidebar Accordion Functionality
 */
function initSidebarAccordion() {
    const groupTitles = document.querySelectorAll('.admin-menu-group-title');
    const groupItems = document.querySelectorAll('.admin-menu-group-items');
    
    if (groupTitles.length === 0) return;
    
    // Set default state - Stocks open
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
}

/**
 * Initialize Mobile Menu Events
 */
function initMobileMenu() {
    // Close mobile menu when window is resized to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            closeMobileMenu();
        }
    });
}

/**
 * =================================
 * PAGE-SPECIFIC FUNCTIONS
 * =================================
 */

/**
 * Refresh dashboard data by reloading with refresh parameter
 */
function refreshDashboard() {
    // Add loading state to button
    const refreshBtn = document.querySelector('.users-refresh-btn');
    if (refreshBtn) {
        refreshBtn.style.opacity = '0.6';
        refreshBtn.style.pointerEvents = 'none';
        
        // Rotate the icon during refresh
        const svg = refreshBtn.querySelector('svg');
        if (svg) {
            svg.style.transform = 'rotate(360deg)';
        }
    }
    
    // Add refresh parameter to current URL
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('refresh', '1');
    currentUrl.searchParams.set('_t', Date.now()); // Cache busting
    
    // Reload page with refresh parameter
    window.location.href = currentUrl.toString();
}

// Note: Each page should implement its own refresh functionality if needed
// Market page has its own refreshMarketData() function in market.js

/**
 * =================================
 * INITIALIZATION
 * =================================
 */

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 Dashboard.js: Initializing...');
    initDashboardPage();
});

// Global functions for backward compatibility
window.toggleUserDropdown = toggleUserDropdown;
window.toggleMobileMenu = toggleMobileMenu;
window.closeMobileMenu = closeMobileMenu;
window.refreshDashboard = refreshDashboard;