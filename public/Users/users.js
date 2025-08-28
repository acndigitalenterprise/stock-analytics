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
    initNewUserForm();
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
 * Show New User Modal
 */
function showNewUserModal() {
    console.log('showNewUserModal called');
    const modal = document.getElementById('new-user-popup');
    if (modal) {
        modal.classList.add('users-modal-active');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        usersCurrentPopup = modal;
    } else {
        console.error('Modal not found');
    }
}

/**
 * Close User Popup
 */
function closeUserPopup() {
    closeUsersPopup('new-user');
    resetNewUserForm();
}

/**
 * Show popup by ID
 */
function showUsersPopup(popupId) {
    try {
        const popup = document.getElementById(popupId + '-popup');
        if (popup) {
            popup.classList.add('users-modal-active');
            usersCurrentPopup = popup;
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
function closeUsersPopup(popupId) {
    const popup = document.getElementById(popupId + '-popup');
    if (popup) {
        popup.classList.remove('users-modal-active');
        usersCurrentPopup = null;
        document.body.style.overflow = '';
    }
}

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
 * Initialize New User Form
 */
function initNewUserForm() {
    const form = document.getElementById('newUserForm');
    if (!form) return;

    form.addEventListener('submit', handleNewUserSubmit);
}

/**
 * Handle New User Form Submit
 */
function handleNewUserSubmit(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitUserBtn');
    const originalText = submitBtn.textContent;
    
    // Disable button and show loading
    submitBtn.textContent = 'Creating...';
    submitBtn.disabled = true;
    
    const formData = new FormData(e.target);
    
    // Get CSRF token
    const csrfToken = document.querySelector('input[name="_token"]').value;
    
    fetch(getCreateUserUrl(), {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeUserPopup();
            showUsersMessage('User created successfully!', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showUsersMessage('Error: ' + (data.error || 'Failed to create user'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showUsersMessage('An error occurred while creating the user.', 'error');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

/**
 * Get Create User URL (to be defined in blade template)
 */
function getCreateUserUrl() {
    return window.createUserUrl || '/stock-analytics/admin/users';
}

/**
 * Reset New User Form
 */
function resetNewUserForm() {
    const form = document.getElementById('newUserForm');
    if (form) {
        form.reset();
        
        // Reset password visibility
        const passwordFields = form.querySelectorAll('input[type="password"]');
        passwordFields.forEach(field => {
            field.type = 'password';
            const toggle = field.parentNode.querySelector('.users-password-toggle');
            if (toggle) {
                updatePasswordToggleIcon(field.id, false);
            }
        });
    }
}

/**
 * =================================
 * PASSWORD TOGGLE FUNCTIONS
 * =================================
 */

/**
 * Initialize Password Toggles
 */
function initPasswordToggles() {
    const toggles = document.querySelectorAll('.users-password-toggle');
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const fieldId = this.getAttribute('data-field') || 
                          this.previousElementSibling?.id ||
                          this.parentNode.querySelector('input')?.id;
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
    const field = document.getElementById(fieldId);
    if (!field) return;
    
    const isPassword = field.type === 'password';
    field.type = isPassword ? 'text' : 'password';
    
    updatePasswordToggleIcon(fieldId, !isPassword);
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

/**
 * Toggle Modal Password Function (moved from blade)
 */
function toggleModalPassword(inputId, iconElement) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById('icon-' + inputId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.05 8.05m0 0a5.971 5.971 0 00-.908 1.563M8.05 8.05l2.828 2.828m0 0A3.01 3.01 0 0010 12c0 .483.115.934.32 1.336M10.88 13.12l2.828 2.828M12.95 11.95l2.828-2.828m0 0a3.01 3.01 0 00.08-4.243m-2.828 2.828L12.95 11.95m0 0a5.971 5.971 0 011.563-.908m-1.563.908l2.828 2.828M15 12a3 3 0 11-6 0 3 3 0 616 0z"/>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}

// Legacy function names for backward compatibility
window.showNewUserModal = showNewUserModal;
window.closeUserPopup = closeUserPopup;
window.changePerPage = changeUsersPerPage; // Keep original name for existing usage
window.togglePassword = toggleUsersPassword; // Keep original name for existing usage
window.toggleModalPassword = toggleModalPassword;