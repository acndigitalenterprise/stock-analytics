/**
 * =================================
 * MARKET PAGE JAVASCRIPT
 * =================================
 */

// Market page JavaScript - Optimized version

/**
 * Initialize Market Page
 */
function initMarketPage() {
    initRefreshButton();
    initTableSorting();
    initUserDropdown();
    initSidebarAccordion();
    initMobileMenu();
    console.log('✅ Market page initialized');
}

/**
 * =================================
 * REFRESH FUNCTIONALITY
 * =================================
 */

/**
 * Initialize Refresh Button
 */
function initRefreshButton() {
    const refreshBtn = document.querySelector('.users-new-btn');
    if (refreshBtn && refreshBtn.textContent.includes('Refresh')) {
        refreshBtn.addEventListener('click', refreshMarketData);
    }
}

/**
 * Refresh Market Data (already defined in blade, but backup here)
 */
function refreshMarketData() {
    const btn = document.querySelector('.users-new-btn');
    const originalText = btn.textContent;
    btn.textContent = 'Refreshing...';
    btn.disabled = true;

    fetch(getMarketRefreshUrl(), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMarketMessage('Market data refreshed successfully!', 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showMarketMessage('Error: ' + (data.message || 'Failed to refresh market data'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMarketMessage('Error refreshing market data', 'error');
    })
    .finally(() => {
        btn.textContent = originalText;
        btn.disabled = false;
    });
}

/**
 * Get Market Refresh URL
 */
function getMarketRefreshUrl() {
    return window.marketRefreshUrl || '/market/refresh';
}

/**
 * =================================
 * TABLE SORTING FUNCTIONS
 * =================================
 */

/**
 * Initialize Table Sorting
 */
function initTableSorting() {
    const tables = document.querySelectorAll('.users-table');
    
    tables.forEach(table => {
        const headers = table.querySelectorAll('th');
        headers.forEach((header, index) => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', () => {
                sortTable(table, index);
            });
        });
    });
}

/**
 * Sort Table Function
 */
function sortTable(table, columnIndex) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    // Check current sort direction
    const header = table.querySelectorAll('th')[columnIndex];
    const isAscending = !header.classList.contains('sorted-desc');
    
    // Remove all sort classes
    table.querySelectorAll('th').forEach(th => {
        th.classList.remove('sorted-asc', 'sorted-desc');
    });
    
    // Add appropriate sort class
    header.classList.add(isAscending ? 'sorted-asc' : 'sorted-desc');
    
    // Sort rows
    rows.sort((a, b) => {
        const aValue = a.cells[columnIndex].textContent.trim();
        const bValue = b.cells[columnIndex].textContent.trim();
        
        // Try to parse as numbers first
        const aNum = parseFloat(aValue.replace(/[^0-9.-]/g, ''));
        const bNum = parseFloat(bValue.replace(/[^0-9.-]/g, ''));
        
        if (!isNaN(aNum) && !isNaN(bNum)) {
            return isAscending ? aNum - bNum : bNum - aNum;
        }
        
        // Fallback to string comparison
        return isAscending 
            ? aValue.localeCompare(bValue) 
            : bValue.localeCompare(aValue);
    });
    
    // Re-append sorted rows
    rows.forEach(row => tbody.appendChild(row));
}

/**
 * =================================
 * MESSAGE FUNCTIONS
 * =================================
 */

/**
 * Show Message to User
 */
function showMarketMessage(message, type = 'info') {
    // Remove existing messages
    const existingMessages = document.querySelectorAll('.users-message');
    existingMessages.forEach(msg => msg.remove());
    
    // Create new message
    const messageDiv = document.createElement('div');
    messageDiv.className = `users-message users-${type}`;
    messageDiv.textContent = message;
    
    // Insert at top of content
    const container = document.querySelector('.admin-content-container') || 
                     document.querySelector('.market-container') ||
                     document.body;
    
    if (container.firstChild) {
        container.insertBefore(messageDiv, container.firstChild);
    } else {
        container.appendChild(messageDiv);
    }
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 5000);
}


/**
 * =================================
 * INITIALIZATION
 * =================================
 */

// Initialize when DOM is loaded (single initialization)
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 Market.js: Initializing...');
    initMarketPage();
});

/**
 * Initialize Auto-refresh for Loading State
 */
function initAutoRefresh() {
    // Check if page is in loading state and auto-refresh is needed
    const loadingContainer = document.getElementById('loading-container');
    if (loadingContainer) {
        // Auto-refresh after 3 seconds to get fresh data
        setTimeout(function() {
            refreshMarketData();
        }, 3000);
    }
}

/**
 * Set Market Refresh URL from server
 */
function setMarketRefreshUrl(url) {
    window.marketRefreshUrl = url;
}

// Initialize auto-refresh when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initAutoRefresh();
});

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

// Global functions for backward compatibility
window.refreshMarketData = refreshMarketData;
window.showMarketMessage = showMarketMessage;
window.setMarketRefreshUrl = setMarketRefreshUrl;
window.toggleUserDropdown = toggleUserDropdown;
window.toggleMobileMenu = toggleMobileMenu;
window.closeMobileMenu = closeMobileMenu;