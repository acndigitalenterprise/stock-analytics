/**
 * =================================
 * REQUESTS PAGE JAVASCRIPT
 * =================================
 */

// Global variables
let requestsCurrentPopup = null;
let requestsFormSubmitting = false;

/**
 * Initialize Requests Page
 */
function initRequestsPage() {
    initNewRequestForm();
    initTableRowLinks();
    initTableSorting();
    initPagination();
    initUserDropdown();
    initSidebarAccordion();
    initMobileMenu();
    console.log('✅ Requests page initialized');
}

/**
 * =================================
 * NEW REQUEST MODAL FUNCTIONS
 * =================================
 */

// Modal functions moved to bottom of file - see MODAL FUNCTIONS section

// Legacy popup functions - replaced by showRequestModal/closeRequestModal

/**
 * Close popup when clicking outside
 */
document.addEventListener('click', function(e) {
    if (requestsCurrentPopup && e.target === requestsCurrentPopup) {
        const popupId = requestsCurrentPopup.id.replace('-popup', '');
        closeRequestsPopup(popupId);
    }
});

/**
 * Close popup on Escape key
 */
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && requestsCurrentPopup) {
        const popupId = requestsCurrentPopup.id.replace('-popup', '');
        closeRequestsPopup(popupId);
    }
});

/**
 * =================================
 * NEW REQUEST FORM FUNCTIONS
 * =================================
 */

/**
 * Initialize New Request Form
 */
function initNewRequestForm() {
    // Form will submit normally - no JavaScript intervention
    console.log('✅ Form will submit normally');
}

/**
 * Handle New Request Form Submit
 */
function handleNewRequestSubmit(e) {
    console.log('🚀 Form submit triggered');
    
    // Close modal immediately
    closeRequestModal();
    
    // Show processing message
    showRequestsMessage('Processing request...', 'info');
    
    // Let form submit normally (don't prevent default)
    return true;
}

/**
 * Show Loading State
 */
function showRequestsLoading(button) {
    if (!button) return;
    
    button.disabled = true;
    button.classList.add('requests-loading');
    
    const originalText = button.textContent;
    button.setAttribute('data-original-text', originalText);
    
    button.innerHTML = '<span class="requests-spinner"></span>Processing...';
}

/**
 * Hide Loading State
 */
function hideRequestsLoading(button) {
    if (!button) return;
    
    button.disabled = false;
    button.classList.remove('requests-loading');
    
    const originalText = button.getAttribute('data-original-text') || 'Submit';
    button.textContent = originalText;
    button.removeAttribute('data-original-text');
}

/**
 * Reset New Request Form
 */
function resetNewRequestForm() {
    const form = document.getElementById('newRequestForm');
    if (form) {
        form.reset();
        
        // Clear any search results
        const resultsContainer = document.getElementById('stockSearchResults');
        if (resultsContainer) {
            resultsContainer.innerHTML = '';
            resultsContainer.style.display = 'none';
        }
    }
}

/**
 * =================================
 * STOCK SEARCH FUNCTIONS
 * =================================
 */

/**
 * Initialize Stock Search
 */
function initStockSearch() {
    const searchInput = document.getElementById('stock_code');
    if (!searchInput) return;

    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length >= 1) {
            searchTimeout = setTimeout(() => {
                searchStocks(query);
            }, 300);
        } else {
            hideStockResults();
        }
    });

    // Handle clicking outside to hide results
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.requests-stock-search-container')) {
            hideStockResults();
        }
    });
}

/**
 * Search for stocks
 */
function searchStocks(query) {
    const resultsContainer = document.getElementById('stockSearchResults');
    if (!resultsContainer) return;

    // Show loading
    resultsContainer.innerHTML = '<div class="requests-search-loading">Searching...</div>';
    resultsContainer.style.display = 'block';

    // Make AJAX request to search endpoint
    fetch(getStockSearchUrl() + '?q=' + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
            displayStockResults(data);
        })
        .catch(error => {
            console.error('Stock search error:', error);
            resultsContainer.innerHTML = '<div class="requests-search-error">Search failed. Please try again.</div>';
        });
}

/**
 * Display stock search results
 */
function displayStockResults(results) {
    const resultsContainer = document.getElementById('stockSearchResults');
    if (!resultsContainer) return;

    if (!results || results.length === 0) {
        resultsContainer.innerHTML = '<div class="requests-search-no-results">No stocks found</div>';
        return;
    }

    let html = '';
    results.forEach(stock => {
        const stockCode = stock.symbol || stock.code;
        const stockName = stock.name;
        html += `
            <div class="requests-search-result-item" onclick="selectStock('${stockCode}', '${stockName}')">
                <div class="requests-search-symbol">${stockCode}</div>
                <div class="requests-search-name">${stockName}</div>
            </div>
        `;
    });

    resultsContainer.innerHTML = html;
    resultsContainer.style.display = 'block';
}

/**
 * Select a stock from search results
 */
function selectStock(symbol, name) {
    const stockCodeInput = document.getElementById('stock_code');
    const companyNameInput = document.getElementById('company_name');
    
    if (stockCodeInput) stockCodeInput.value = symbol;
    if (companyNameInput) companyNameInput.value = name;
    
    hideStockResults();
}

/**
 * Hide stock search results
 */
function hideStockResults() {
    const resultsContainer = document.getElementById('stockSearchResults');
    if (resultsContainer) {
        resultsContainer.style.display = 'none';
    }
}

/**
 * Get Stock Search URL
 */
function getStockSearchUrl() {
    return window.stockSearchUrl || '/api/stocks/search';
}

/**
 * =================================
 * TABLE FUNCTIONALITY
 * =================================
 */

/**
 * Initialize Table Row Links
 */
function initTableRowLinks() {
    const rowLinks = document.querySelectorAll('.requests-table .row-link');
    
    rowLinks.forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't navigate if clicking on action buttons
            if (e.target.closest('.requests-action-container')) {
                return;
            }
            
            const href = this.getAttribute('data-href');
            if (href) {
                window.location.href = href;
            }
        });
    });
}

/**
 * Initialize Table Sorting
 */
function initTableSorting() {
    const sortableHeaders = document.querySelectorAll('.requests-table .sortable');
    
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const sortField = this.getAttribute('data-sort');
            if (sortField) {
                handleRequestsSort(sortField);
            }
        });
    });
}

/**
 * Handle Table Sort
 */
function handleRequestsSort(field) {
    const url = new URL(window.location);
    const currentSort = url.searchParams.get('sort');
    const currentOrder = url.searchParams.get('order');
    
    let newOrder = 'asc';
    if (currentSort === field && currentOrder === 'asc') {
        newOrder = 'desc';
    }
    
    url.searchParams.set('sort', field);
    url.searchParams.set('order', newOrder);
    url.searchParams.delete('page'); // Reset to first page
    
    window.location.href = url.toString();
}

/**
 * Check Advice Function (moved from blade)
 */
function checkAdvice(requestId) {
    console.log('Generating advice for request:', requestId);
    
    // Show loading state
    const button = document.querySelector(`button[onclick="checkAdvice(${requestId})"]`);
    if (button) {
        button.disabled = true;
        button.textContent = 'Generating...';
        button.classList.add('requests-loading');
    }
    
    // Make AJAX call to generate advice
    fetch(`/requests/${requestId}/advice`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to show new advice
            window.location.reload();
        } else {
            alert(data.error || 'Failed to generate advice');
        }
    })
    .catch(error => {
        console.error('Error generating advice:', error);
        alert('Failed to generate advice. Please try again.');
    })
    .finally(() => {
        // Reset button state
        if (button) {
            button.disabled = false;
            button.textContent = 'Advice';
            button.classList.remove('requests-loading');
        }
    });
}

/**
 * =================================
 * PAGINATION FUNCTIONS
 * =================================
 */

/**
 * Initialize Pagination
 */
function initPagination() {
    const perPageSelect = document.getElementById('perPageRequests');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            changeRequestsPerPage(this.value);
        });
    }
}

/**
 * Change Per Page Value
 */
function changeRequestsPerPage(value) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', value);
    url.searchParams.delete('page'); // Reset to first page
    window.location.href = url.toString();
}

/**
 * =================================
 * RESULT BADGE FUNCTIONS
 * =================================
 */

/**
 * Format result badge
 */
function formatResultBadge(result) {
    const badges = {
        'MONITORING': 'requests-result-monitoring',
        'WIN': 'requests-result-win',
        'SUPER_WIN': 'requests-result-super-win',
        'LOSS': 'requests-result-loss',
        'TIMEOUT': 'requests-result-timeout'
    };
    
    const badgeClass = badges[result] || 'requests-result-monitoring';
    return `<span class="requests-result-badge ${badgeClass}">${result}</span>`;
}

/**
 * =================================
 * MESSAGE FUNCTIONS
 * =================================
 */

/**
 * Show Message to User
 */
function showRequestsMessage(message, type = 'info') {
    // Remove existing messages
    const existingMessages = document.querySelectorAll('.requests-message');
    existingMessages.forEach(msg => msg.remove());
    
    // Create new message
    const messageDiv = document.createElement('div');
    messageDiv.className = `requests-message requests-${type}`;
    messageDiv.textContent = message;
    
    // Insert at top of content
    const container = document.querySelector('.admin-content-container') || 
                     document.querySelector('.requests-container') ||
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
 * CONFIRMATION FUNCTIONS
 * =================================
 */

/**
 * Confirm Request Deletion
 */
function confirmRequestDeletion(event, requestId) {
    const confirmed = confirm(`Are you sure you want to delete this request? This action cannot be undone.`);
    if (!confirmed) {
        event.preventDefault();
        return false;
    }
    return true;
}

/**
 * =================================
 * ADVICE CONTENT FUNCTIONS
 * =================================
 */

/**
 * Toggle advice content expansion
 */
function toggleAdviceContent(requestId) {
    const adviceElement = document.getElementById(`advice-text-${requestId}`);
    if (!adviceElement) return;
    
    const isExpanded = adviceElement.style.maxHeight === 'none';
    
    if (isExpanded) {
        adviceElement.style.maxHeight = '60px';
        adviceElement.style.overflow = 'hidden';
    } else {
        adviceElement.style.maxHeight = 'none';
        adviceElement.style.overflow = 'visible';
    }
}

/**
 * =================================
 * UTILITY FUNCTIONS
 * =================================
 */

/**
 * Debounce Function
 */
function requestsDebounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Format Currency
 */
function formatCurrency(amount) {
    if (!amount) return '-';
    return 'IDR ' + parseFloat(amount).toLocaleString('id-ID');
}

/**
 * Format Date
 */
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * =================================
 * INITIALIZATION
 * =================================
 */

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initRequestsPage();
});

// Handle page visibility change
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        // Reset form state if user comes back to page
        requestsFormSubmitting = false;
        const submitBtn = document.querySelector('.requests-btn-primary');
        if (submitBtn) {
            hideRequestsLoading(submitBtn);
        }
    }
});

/**
 * =================================
 * MODAL FUNCTIONS
 * =================================
 */

/**
 * Show Request Modal
 */
function showRequestModal() {
    const modal = document.getElementById('request-modal-final');
    if (modal) {
        modal.classList.remove('requests-modal-hidden');
        modal.classList.add('requests-modal-active');
        // Remove overflow hidden to allow scrolling
        // document.body.style.overflow = 'hidden';
        
        // Initialize stock search when modal is shown
        initStockSearch();
        
        // Add form submit handler for immediate modal close
        const form = document.getElementById('newRequestForm');
        if (form) {
            form.onsubmit = function(e) {
                // Close modal immediately on form submit
                closeRequestModal();
                // Let the form submit naturally (no preventDefault)
                return true;
            };
        }
    }
}

/**
 * Close Request Modal
 */
function closeRequestModal() {
    const modal = document.getElementById('request-modal-final');
    if (modal) {
        modal.classList.remove('requests-modal-active');
        modal.classList.add('requests-modal-hidden');
        document.body.style.overflow = '';
    }
}

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

// Global function exports for HTML onclick handlers
window.showRequestModal = showRequestModal;
window.closeRequestModal = closeRequestModal;
window.toggleUserDropdown = toggleUserDropdown;
window.toggleMobileMenu = toggleMobileMenu;
window.closeMobileMenu = closeMobileMenu;
window.changeRequestsPerPage = changeRequestsPerPage;
window.confirmRequestDeletion = confirmRequestDeletion;
window.checkAdvice = checkAdvice;

// Export functions for testing and external access
// Global form handler
function handleFormSubmit(event) {
    console.log('🚀 Direct form submit handler called');
    return handleNewRequestSubmit(event);
}

window.handleFormSubmit = handleFormSubmit;
window.requestsPageFunctions = {
    initRequestsPage,
    showRequestsMessage,
    searchStocks,
    formatResultBadge,
    toggleAdviceContent
};