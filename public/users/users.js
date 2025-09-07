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
    initDeleteForms();
    initPerPageSelector();
    initPaginationButtons();
    initNewUserForm();
    initUserDropdown();
    initSidebarAccordion();
    initMobileMenu();
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
    console.log('🚀 showNewUserModal called');
    const modal = document.getElementById('user-modal-final');
    console.log('Modal element:', modal);
    if (modal) {
        resetNewUserForm(); // Reset form when opening
        console.log('Setting modal class to users-modal-show');
        modal.className = 'users-modal-show';
        usersCurrentPopup = modal;
        console.log('Modal should now be visible');
        
        // Focus on first input
        setTimeout(() => {
            const firstInput = modal.querySelector('input[name="full_name"]');
            if (firstInput) firstInput.focus();
        }, 100);
        
        // FORCE button to be clickable by adding better handlers
        setTimeout(() => {
            const submitBtn = modal.querySelector('button[onclick="submitUserForm()"]');
            if (submitBtn) {
                // Remove any overlays that might interfere
                submitBtn.style.position = 'relative';
                submitBtn.style.zIndex = '999999999';
                submitBtn.style.pointerEvents = 'auto';
                
                // Add multiple fallback handlers
                submitBtn.addEventListener('click', function(e) {
                    console.log('🎯 FORCED CLICK HANDLER TRIGGERED!');
                    e.preventDefault();
                    e.stopPropagation();
                    submitUserForm();
                }, true); // Use capture phase
                
                console.log('✅ Forced button handlers added');
            }
        }, 500);
    } else {
        console.error('Modal element not found!');
    }
}

/**
 * Hide New User Modal
 */
function hideNewUserModal() {
    const modal = document.getElementById('user-modal-final');
    if (modal) {
        modal.className = 'users-modal-hide';
        usersCurrentPopup = null;
        resetNewUserForm(); // Reset form when closing
    }
}


/**
 * Close popup when clicking outside
 */
document.addEventListener('click', function(e) {
    if (usersCurrentPopup && e.target === usersCurrentPopup) {
        hideNewUserModal();
    }
});

/**
 * Close popup on Escape key
 */
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && usersCurrentPopup) {
        hideNewUserModal();
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
    
    // Add backup button click handler for cases where normal click doesn't work
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        // Alternative 1: Direct button click handler
        submitBtn.addEventListener('click', function(e) {
            console.log('🎯 Direct button click handler triggered');
            e.preventDefault(); // Prevent default to control submission
            
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirmation');
            
            if (password && passwordConfirm) {
                if (password.value !== passwordConfirm.value) {
                    alert('Password and Confirm Password must match!');
                    passwordConfirm.focus();
                    return;
                }
            }
            
            // Get fresh CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const csrfInput = form.querySelector('input[name="_token"]');
            if (csrfInput && csrfToken) {
                csrfInput.value = csrfToken;
                console.log('✅ CSRF token updated in direct handler');
            }
            
            console.log('✅ Validation passed, submitting form programatically');
            form.submit(); // Direct form submission
        });
        
        // Alternative 2: Double click handler as backup
        submitBtn.addEventListener('dblclick', function(e) {
            console.log('🔄 Double click backup triggered');
            e.preventDefault();
            form.submit();
        });
    }
    
    form.addEventListener('submit', function(e) {
        console.log('🚀 Form submit event fired!');
        const password = document.getElementById('password');
        const passwordConfirm = document.getElementById('password_confirmation');
        
        console.log('Password field:', password);
        console.log('Password confirm field:', passwordConfirm);
        
        if (password && passwordConfirm) {
            console.log('Password value:', password.value);
            console.log('Password confirm value:', passwordConfirm.value);
            
            if (password.value !== passwordConfirm.value) {
                console.log('❌ Passwords do not match, preventing submission');
                e.preventDefault();
                alert('Password and Confirm Password must match!');
                passwordConfirm.focus();
                return false;
            }
        }
        
        console.log('✅ Form validation passed, allowing submission');
        // Do not prevent default - allow natural form submission
    });
    
    // Password match validation on typing
    const passwordConfirm = document.getElementById('password_confirmation');
    if (passwordConfirm) {
        passwordConfirm.addEventListener('input', function() {
            const password = document.getElementById('password');
            if (password && this.value && password.value !== this.value) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    }
}

/**
 * Reset New User Form
 */
function resetNewUserForm() {
    const form = document.getElementById('newUserForm');
    if (form) {
        form.reset();
        // Clear custom validation messages
        const inputs = form.querySelectorAll('input');
        inputs.forEach(input => input.setCustomValidity(''));
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
 * Simple password toggle function for auth-style fields
 */
function toggleAuthPassword(fieldId) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    field.type = field.type === 'password' ? 'text' : 'password';
}

/**
 * =================================
 * PAGINATION FUNCTIONS
 * =================================
 */

/**
 * Initialize Per Page Selector
 */
function initPerPageSelector() {
    const perPageSelect = document.getElementById('perPageUsers');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            changeUsersPerPage(this.value);
        });
    }
}

/**
 * Initialize Pagination Buttons
 */
function initPaginationButtons() {
    const paginationButtons = document.querySelectorAll('.users-pagination-btn[data-url]');
    paginationButtons.forEach(button => {
        button.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            if (url) {
                window.location.href = url;
            }
        });
    });
}

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
 * Initialize Delete Forms
 */
function initDeleteForms() {
    const deleteForms = document.querySelectorAll('.users-delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const userName = this.getAttribute('data-user-name');
            if (!confirmUserDeletion(e, userName)) {
                e.preventDefault();
                return false;
            }
        });
    });
}

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
 * Submit User Form (Global function for onclick handler)
 */
function submitUserForm() {
    console.log('🎯 submitUserForm() called via button handler');
    const form = document.getElementById('newUserForm');
    if (!form) {
        console.error('Form not found');
        return;
    }
    
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirmation');
    
    if (password && passwordConfirm) {
        if (password.value !== passwordConfirm.value) {
            alert('Password and Confirm Password must match!');
            passwordConfirm.focus();
            return;
        }
    }
    
    // Get fresh CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    console.log('CSRF Token:', csrfToken);
    
    // Update CSRF token in form if found
    const csrfInput = form.querySelector('input[name="_token"]');
    if (csrfInput && csrfToken) {
        csrfInput.value = csrfToken;
        console.log('✅ CSRF token updated');
    }
    
    console.log('✅ Validation passed, submitting form programatically');
    form.submit();
}


/**
 * =================================
 * ADMIN FUNCTIONALITY - COPIED FROM REQUESTS
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

// Keep only actively used global functions
window.changePerPage = changeUsersPerPage; // Keep original name for existing usage
window.togglePassword = function(fieldId, el) {
    toggleUsersPassword(fieldId, el);
};
window.showNewUserModal = showNewUserModal;
window.hideNewUserModal = hideNewUserModal;
window.submitUserForm = submitUserForm;
window.toggleUserDropdown = toggleUserDropdown;
window.toggleMobileMenu = toggleMobileMenu;
window.closeMobileMenu = closeMobileMenu;