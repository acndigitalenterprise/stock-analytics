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
    console.log('✅ Requests page initialized');
}

/**
 * =================================
 * NEW REQUEST MODAL FUNCTIONS
 * =================================
 */

/**
 * Show New Request Modal
 */
function showNewRequestModal() {
    console.log('showNewRequestModal called');
    const modal = document.getElementById('new-request-popup');
    if (modal) {
        modal.classList.add('requests-modal-active');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        requestsCurrentPopup = modal;
    } else {
        console.error('Request modal not found');
    }
}

/**
 * Close Request Popup
 */
function closeRequestPopup() {
    closeRequestsPopup('new-request');
    resetNewRequestForm();
}

/**
 * Show popup by ID
 */
function showRequestsPopup(popupId) {
    try {
        const popup = document.getElementById(popupId + '-popup');
        if (popup) {
            popup.classList.add('requests-modal-active');
            requestsCurrentPopup = popup;
            document.body.style.overflow = 'hidden';
        } else {
            console.error('Modal element not found:', popupId + '-popup');
        }
    } catch (error) {
        console.error('Error showing modal:', error);
    }
}

/**
 * Close popup by ID
 */
function closeRequestsPopup(popupId) {
    const popup = document.getElementById(popupId + '-popup');
    if (popup) {
        popup.classList.remove('requests-modal-active');
        requestsCurrentPopup = null;
        document.body.style.overflow = '';
    }
}

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
    const form = document.getElementById('newRequestForm');
    if (!form) return;

    form.addEventListener('submit', handleNewRequestSubmit);
    
    // Initialize stock search functionality
    initStockSearch();
}

/**
 * Handle New Request Form Submit
 */
function handleNewRequestSubmit(e) {
    if (requestsFormSubmitting) {
        e.preventDefault();
        return false;
    }
    
    const form = e.target;
    const submitBtn = form.querySelector('.requests-btn-primary');
    
    if (submitBtn && !requestsFormSubmitting) {
        showRequestsLoading(submitBtn);
        requestsFormSubmitting = true;
        
        // Reset loading state after timeout
        setTimeout(() => {
            hideRequestsLoading(submitBtn);
            requestsFormSubmitting = false;
        }, 10000);
    }
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
    const searchInput = document.getElementById('stock_search');
    if (!searchInput) return;

    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length >= 2) {
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
        html += `
            <div class="requests-search-result-item" onclick="selectStock('${stock.symbol}', '${stock.name}')">
                <div class="requests-search-symbol">${stock.symbol}</div>
                <div class="requests-search-name">${stock.name}</div>
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
    // Implement advice checking logic
    console.log('Checking advice for request:', requestId);
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

// Legacy function names for backward compatibility
window.showPopup = showRequestsPopup; // Keep original name for existing usage
window.closePopup = closeRequestsPopup; // Keep original name for existing usage
window.changePerPage = changeRequestsPerPage; // Keep original name for existing usage
window.showNewRequestModal = showNewRequestModal;
window.closeRequestPopup = closeRequestPopup;
window.checkAdvice = checkAdvice;

// Export functions for testing and external access
window.requestsPageFunctions = {
    initRequestsPage,
    showRequestsMessage,
    searchStocks,
    formatResultBadge,
    toggleAdviceContent
};