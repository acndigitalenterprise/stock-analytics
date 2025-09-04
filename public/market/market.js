/**
 * =================================
 * MARKET PAGE JAVASCRIPT
 * =================================
 */

// Global variables
let marketCurrentPopup = null;

/**
 * Initialize Market Page
 */
function initMarketPage() {
    console.log('🚀 Initializing Market Page...');
    initRefreshButton();
    initTableSorting();
    console.log('✅ Market Page initialized');
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
 * UTILITY FUNCTIONS
 * =================================
 */

/**
 * Format Number with Commas
 */
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

/**
 * Format Currency
 */
function formatCurrency(num) {
    return 'Rp ' + formatNumber(num);
}

/**
 * Format Percentage
 */
function formatPercentage(num) {
    return num.toFixed(2) + '%';
}

/**
 * =================================
 * INITIALIZATION
 * =================================
 */

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM Content Loaded - Market.js starting...');
    initMarketPage();
});

// Also try window.onload as fallback
window.addEventListener('load', function() {
    console.log('🪟 Window Load - Market.js fallback init...');
    // Try again after a delay
    setTimeout(() => {
        console.log('⏰ Delayed init - Market.js...');
        initMarketPage();
    }, 1000);
});

// Immediate check
console.log('🔧 Market.js file loaded!');

// Global functions for backward compatibility
window.refreshMarketData = refreshMarketData;
window.showMarketMessage = showMarketMessage;