/**
 * =================================
 * USERS PAGE JAVASCRIPT
 * =================================
 */

// Global variables
let usersCurrentPopup = null;

/**
 * Initialize Users Page
 */
function initUsersPage() {
    console.log('🚀 Initializing Users Page...');
    initPasswordToggles();
    initRowLinks();
    console.log('✅ Users Page initialized');
}

/**
 * =================================
 * NEW USER MODAL FUNCTIONS
 * =================================
 */


/**
 * Close popup when clicking outside
 */
document.addEventListener('click', function(e) {
    if (usersCurrentPopup && e.target === usersCurrentPopup) {
        const popupId = usersCurrentPopup.id.replace('-popup', '');
        closeUsersPopup(popupId);
    }
});

/**
 * Close popup on Escape key
 */
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && usersCurrentPopup) {
        const popupId = usersCurrentPopup.id.replace('-popup', '');
        closeUsersPopup(popupId);
    }
});

/**
 * =================================
 * NEW USER FORM FUNCTIONS
 * =================================
 */


/**
 * =================================
 * PASSWORD TOGGLE FUNCTIONS
 * =================================
 */

/**
 * Initialize Password Toggles
 */
function initPasswordToggles() {
    console.log('initPasswordToggles called');
    const toggles = document.querySelectorAll('.users-password-toggle');
    console.log('Found toggles:', toggles.length);
    
    toggles.forEach(toggle => {
        console.log('Adding event listener to toggle:', toggle);
        toggle.addEventListener('click', function() {
            console.log('Toggle clicked!');
            const fieldId = this.getAttribute('data-field') || 
                          this.previousElementSibling?.id ||
                          this.parentNode.querySelector('input')?.id;
            console.log('Found fieldId:', fieldId);
            if (fieldId) {
                toggleUsersPassword(fieldId, this);
            }
        });
    });
}

/**
 * Toggle Password Visibility
 */
function toggleUsersPassword(fieldId, toggleElement) {
    console.log('toggleUsersPassword called with fieldId:', fieldId);
    const field = document.getElementById(fieldId);
    if (!field) {
        console.log('Field not found:', fieldId);
        return;
    }
    
    console.log('Current field type:', field.type);
    // Toggle between password and text
    const isCurrentlyPassword = field.type === 'password';
    field.type = isCurrentlyPassword ? 'text' : 'password';
    console.log('New field type:', field.type);
    
    // Update the icon
    updatePasswordToggleIcon(fieldId, !isCurrentlyPassword);
}

/**
 * Update Password Toggle Icon
 */
function updatePasswordToggleIcon(fieldId, isVisible) {
    const toggle = document.querySelector(`[data-field="${fieldId}"]`) ||
                  document.getElementById(fieldId)?.parentNode?.querySelector('.users-password-toggle');
    
    if (!toggle) return;
    
    const icon = toggle.querySelector('svg');
    if (!icon) return;
    
    if (isVisible) {
        // Show "eye-off" icon (password is visible)
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
        `;
    } else {
        // Show "eye" icon (password is hidden)
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        `;
    }
}

/**
 * =================================
 * PAGINATION FUNCTIONS
 * =================================
 */

/**
 * Change Per Page Value
 */
function changeUsersPerPage(value) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', value);
    url.searchParams.delete('page'); // Reset to first page
    window.location.href = url.toString();
}

/**
 * =================================
 * SORTING FUNCTIONS
 * =================================
 */

/**
 * Initialize Table Sorting
 */
function initUsersTableSorting() {
    const sortableHeaders = document.querySelectorAll('.users-table .sortable');
    
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const sortField = this.getAttribute('data-sort');
            if (sortField) {
                handleUsersSort(sortField);
            }
        });
    });
}

/**
 * Handle Table Sort
 */
function handleUsersSort(field) {
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
 * =================================
 * MESSAGE FUNCTIONS
 * =================================
 */

/**
 * Show Message to User
 */
function showUsersMessage(message, type = 'info') {
    // Remove existing messages
    const existingMessages = document.querySelectorAll('.users-message');
    existingMessages.forEach(msg => msg.remove());
    
    // Create new message
    const messageDiv = document.createElement('div');
    messageDiv.className = `users-message users-${type}`;
    messageDiv.textContent = message;
    
    // Insert at top of content
    const container = document.querySelector('.admin-content-container') || 
                     document.querySelector('.users-container') ||
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
 * Confirm User Deletion
 */
function confirmUserDeletion(event, userName) {
    const confirmed = confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`);
    if (!confirmed) {
        event.preventDefault();
        return false;
    }
    return true;
}

/**
 * =================================
 * ROW LINK FUNCTIONS
 * =================================
 */

/**
 * Initialize Row Links (same as Requests implementation)
 */
function initRowLinks() {
    console.log('🔍 Starting initRowLinks...');
    
    // Debug: Check if table exists
    const table = document.querySelector('.users-table');
    console.log('Table found:', table ? 'YES' : 'NO');
    
    // Debug: Check all tr elements
    const allTrs = document.querySelectorAll('tr');
    console.log('Total TR elements:', allTrs.length);
    
    // Debug: Check tr with data-href
    const trsWithHref = document.querySelectorAll('tr[data-href]');
    console.log('TRs with data-href:', trsWithHref.length);
    
    // Debug: Check row-link class
    const rowLinksAll = document.querySelectorAll('.row-link');
    console.log('Elements with .row-link class:', rowLinksAll.length);
    
    // Main selector
    const rowLinks = document.querySelectorAll('.users-table .row-link');
    console.log('🎯 Found', rowLinks.length, 'clickable rows with selector .users-table .row-link');
    
    // If no rows found, try alternative approach
    if (rowLinks.length === 0) {
        console.log('⚠️ No rows found with main selector, trying alternatives...');
        
        // Try just .row-link
        const altRows = document.querySelectorAll('.row-link');
        console.log('Alternative 1 (.row-link):', altRows.length);
        
        // Try tr[data-href]
        const altRows2 = document.querySelectorAll('tr[data-href]');
        console.log('Alternative 2 (tr[data-href]):', altRows2.length);
        
        if (altRows2.length > 0) {
            console.log('🔄 Using tr[data-href] selector instead');
            altRows2.forEach((row, index) => {
                console.log(`Adding click listener to row ${index}:`, row.getAttribute('data-href'));
                row.addEventListener('click', function(e) {
                    console.log('🖱️ Row clicked!', e.target, 'href:', this.getAttribute('data-href'));
                    
                    // Don't navigate if clicking on action buttons
                    if (e.target.closest('.users-action-container')) {
                        console.log('Click on action container, ignoring');
                        return;
                    }
                    
                    const href = this.getAttribute('data-href');
                    console.log('🚀 Navigating to:', href);
                    if (href) {
                        window.location.href = href;
                    }
                });
            });
            return;
        }
    }
    
    rowLinks.forEach((row, index) => {
        console.log(`Adding click listener to row ${index}:`, row.getAttribute('data-href'));
        row.addEventListener('click', function(e) {
            console.log('🖱️ Row clicked!', e.target, 'href:', this.getAttribute('data-href'));
            
            // Don't navigate if clicking on action buttons
            if (e.target.closest('.users-action-container')) {
                console.log('Click on action container, ignoring');
                return;
            }
            
            const href = this.getAttribute('data-href');
            console.log('🚀 Navigating to:', href);
            if (href) {
                window.location.href = href;
            }
        });
    });
}

/**
 * =================================
 * INITIALIZATION
 * =================================
 */

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM Content Loaded - Users.js starting...');
    initUsersPage();
    initUsersTableSorting();
});

// Also try window.onload as fallback
window.addEventListener('load', function() {
    console.log('🪟 Window Load - Users.js fallback init...');
    // Try again after a delay
    setTimeout(() => {
        console.log('⏰ Delayed init - Users.js...');
        initRowLinks();
    }, 1000);
});

// Immediate check
console.log('🔧 Users.js file loaded!');


// Keep only actively used global functions
window.changePerPage = changeUsersPerPage; // Keep original name for existing usage
window.togglePassword = function(fieldId, el) {
    toggleUsersPassword(fieldId, el);
};