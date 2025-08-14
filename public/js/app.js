// ========================================
// STOCK ANALYTICS - COMMON JAVASCRIPT
// ========================================

// Global variables
let searchTimeout;
let currentSearchResults = [];
let selectedIndex = -1;

// ========================================
// POPUP FUNCTIONS
// ========================================

/**
 * Show popup modal
 * @param {string} type - Popup type (signin, signup, forgot-password, new-request)
 */
function showPopup(type) {
    document.getElementById(type + '-popup').style.display = 'flex';
}

/**
 * Close popup modal
 * @param {string} type - Popup type
 */
function closePopup(type) {
    document.getElementById(type + '-popup').style.display = 'none';
}

// ========================================
// PASSWORD TOGGLE FUNCTIONS
// ========================================

/**
 * Toggle password visibility
 * @param {string} fieldId - Input field ID
 * @param {HTMLElement} el - Toggle button element
 */
function togglePassword(fieldId, el) {
    const input = document.getElementById(fieldId);
    const icon = document.getElementById('icon-' + fieldId);
    
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
            // SVG version - change to hide icon (eye with slash)
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464m1.414 1.414l-1.414-1.414M14.12 14.12l1.415 1.415m-1.415-1.415L8.464 8.464m5.656 5.656l1.415 1.415"/>';
        } else {
            // Emoji version
            el.textContent = '👁';
        }
    } else {
        input.type = 'password';
        if (icon) {
            // SVG version - change back to show icon (normal eye)
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
        } else {
            // Emoji version
            el.textContent = '🙈';
        }
    }
}

/**
 * Toggle password visibility (alternative version)
 * @param {string} inputId - Input field ID
 */
function togglePasswordAlt(inputId) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}

// ========================================
// STOCK SEARCH FUNCTIONS
// ========================================

/**
 * Handle stock search with debouncing
 * @param {string} inputId - Input field ID
 * @param {string} listId - Autocomplete list ID
 * @param {string} placeholder - Placeholder text
 */
function handleStockSearch(inputId, listId, placeholder = 'Type to search stocks...') {
    const input = document.getElementById(inputId);
    const query = input.value.trim();
    const list = document.getElementById(listId);
    
    // Clear previous timeout
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    // Hide autocomplete if query is empty
    if (!query) {
        hideAutocomplete(listId);
        return;
    }
    
    // Show loading indicator
    showLoadingIndicator(listId);
    
    // Debounce the search (wait 300ms after user stops typing)
    searchTimeout = setTimeout(() => {
        searchStocks(query, listId, inputId);
    }, 300);
}

/**
 * Show loading indicator
 * @param {string} listId - Autocomplete list ID
 */
function showLoadingIndicator(listId) {
    const list = document.getElementById(listId);
    list.innerHTML = '<div class="loading-indicator">Searching stocks...</div>';
    list.style.display = 'block';
}

/**
 * Search stocks via API
 * @param {string} query - Search query
 * @param {string} listId - Autocomplete list ID
 */
function searchStocks(query, listId, inputId) {
    fetch(`/api/stocks/search?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            currentSearchResults = data;
            displaySearchResults(data, listId, inputId);
        })
        .catch(error => {
            showNoResults(listId);
        });
}

/**
 * Display search results
 * @param {Array} results - Search results
 * @param {string} listId - Autocomplete list ID
 * @param {string} inputId - Input field ID
 */
function displaySearchResults(results, listId, inputId) {
    const list = document.getElementById(listId);
    list.innerHTML = '';
    
    if (results.length === 0) {
        showNoResults(listId);
        return;
    }
    
    results.forEach((stock, index) => {
        const div = document.createElement('div');
        div.className = 'autocomplete-item';
        div.innerHTML = `
            <div class="stock-code">${stock.code}</div>
            <div class="stock-name">${stock.name}</div>
        `;
        div.onclick = function() {
            selectStock(stock, inputId, listId);
        };
        div.onmouseover = function() {
            setSelectedIndex(index, listId);
        };
        list.appendChild(div);
    });
    
    list.style.display = 'block';
    selectedIndex = -1;
}

/**
 * Show no results message
 * @param {string} listId - Autocomplete list ID
 */
function showNoResults(listId) {
    const list = document.getElementById(listId);
    list.innerHTML = '<div class="no-results">No stocks found</div>';
    list.style.display = 'block';
}

/**
 * Select stock from autocomplete
 * @param {Object} stock - Stock object
 * @param {string} inputId - Input field ID
 * @param {string} listId - Autocomplete list ID
 */
function selectStock(stock, inputId, listId) {
    document.getElementById(inputId).value = stock.code;
    hideAutocomplete(listId);
}

/**
 * Hide autocomplete list
 * @param {string} listId - Autocomplete list ID
 */
function hideAutocomplete(listId) {
    document.getElementById(listId).style.display = 'none';
    selectedIndex = -1;
}

/**
 * Set selected index for keyboard navigation
 * @param {number} index - Selected index
 * @param {string} listId - Autocomplete list ID
 */
function setSelectedIndex(index, listId) {
    const items = document.querySelectorAll(`#${listId} .autocomplete-item`);
    items.forEach((item, i) => {
        if (i === index) {
            item.classList.add('selected');
        } else {
            item.classList.remove('selected');
        }
    });
    selectedIndex = index;
}

/**
 * Handle keyboard navigation for autocomplete
 * @param {Event} event - Keyboard event
 * @param {string} listId - Autocomplete list ID
 * @param {string} inputId - Input field ID
 */
function handleKeyDown(event, listId, inputId) {
    const list = document.getElementById(listId);
    const items = document.querySelectorAll(`#${listId} .autocomplete-item`);
    
    if (list.style.display === 'none') return;
    
    switch(event.key) {
        case 'ArrowDown':
            event.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
            setSelectedIndex(selectedIndex, listId);
            break;
        case 'ArrowUp':
            event.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, -1);
            if (selectedIndex === -1) {
                items.forEach(item => item.classList.remove('selected'));
            } else {
                setSelectedIndex(selectedIndex, listId);
            }
            break;
        case 'Enter':
            event.preventDefault();
            if (selectedIndex >= 0 && currentSearchResults[selectedIndex]) {
                selectStock(currentSearchResults[selectedIndex], inputId, listId);
            }
            break;
        case 'Escape':
            hideAutocomplete(listId);
            break;
    }
}

// ========================================
// FORM VALIDATION FUNCTIONS
// ========================================

/**
 * Validate email format
 * @param {string} input - Email input
 * @returns {boolean} - Is valid email
 */
function validateEmail(input) {
    const email = input.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Check password strength
 * @param {string} password - Password to check
 * @returns {Object} - Strength object with score and message
 */
function checkPasswordStrength(password) {
    let score = 0;
    let message = '';
    
    if (password.length >= 8) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;
    
    switch(score) {
        case 0:
        case 1:
            message = 'Very Weak';
            break;
        case 2:
            message = 'Weak';
            break;
        case 3:
            message = 'Fair';
            break;
        case 4:
            message = 'Good';
            break;
        case 5:
            message = 'Strong';
            break;
    }
    
    return { score, message };
}

// ========================================
// UTILITY FUNCTIONS
// ========================================

/**
 * Auto-hide success messages
 * @param {number} delay - Delay in milliseconds (default: 5000)
 */
function autoHideMessages(delay = 5000) {
    const successMessages = document.querySelectorAll('.success-message');
    const errorMessages = document.querySelectorAll('.error-message');
    
    successMessages.forEach(function(message) {
        setTimeout(function() {
            message.style.opacity = '0';
            setTimeout(function() {
                message.style.display = 'none';
            }, 300);
        }, delay);
    });
    
    errorMessages.forEach(function(message) {
        setTimeout(function() {
            message.style.opacity = '0';
            setTimeout(function() {
                message.style.display = 'none';
            }, 300);
        }, delay);
    });
}

/**
 * Initialize common functionality
 */
function initCommon() {
    // Auto-hide messages
    autoHideMessages();
    
    // Close autocomplete when clicking outside
    document.addEventListener('click', function(e) {
        const autocompleteLists = document.querySelectorAll('[id$="-autocomplete-list"]');
        autocompleteLists.forEach(list => {
            const inputId = list.id.replace('-autocomplete-list', '');
            const input = document.getElementById(inputId);
            
            if (!e.target.closest(`#${inputId}`) && !e.target.closest(`#${list.id}`)) {
                hideAutocomplete(list.id);
            }
        });
    });
}

// ========================================
// SHARED PAGINATION AND SORTING FUNCTIONS
// ========================================

/**
 * Universal change per page function
 * @param {string|number} perPage - Number of items per page
 */
function changePerPage(perPage) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page'); // Reset to page 1
    window.location.href = url.toString();
}

/**
 * Initialize sortable headers functionality
 */
function initSortableHeaders() {
    const sortableHeaders = document.querySelectorAll('.sortable');
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const sortField = this.getAttribute('data-sort');
            
            // Get current sort values from server-side template variables
            const currentSort = document.querySelector('meta[name="current-sort"]')?.content || '';
            const currentOrder = document.querySelector('meta[name="current-order"]')?.content || 'desc';
            
            let newOrder = 'desc';
            if (currentSort === sortField && currentOrder === 'desc') {
                newOrder = 'asc';
            }
            
            const url = new URL(window.location);
            url.searchParams.set('sort', sortField);
            url.searchParams.set('order', newOrder);
            window.location.href = url.toString();
        });
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initCommon();
    initSortableHeaders();
}); 