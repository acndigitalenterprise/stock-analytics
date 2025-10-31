/**
 * =================================
 * REQUEST DETAIL PAGE JAVASCRIPT
 * =================================
 */

/**
 * Initialize Request Detail Page
 */
function initRequestDetailPage() {
    initUserDropdown();
    initSidebarAccordion();
    initMobileMenu();
    initAITabs();
    console.log('✅ Request Detail page initialized');
}

/**
 * =================================
 * REQUEST DETAIL FUNCTIONS
 * =================================
 */

/**
 * AI Tabs Functionality
 */
function initAITabs() {
    const tabButtons = document.querySelectorAll('.ai-tab-btn');
    const tabPanels = document.querySelectorAll('.ai-tab-panel');

    if (tabButtons.length === 0) return;

    tabButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');

            // Remove active class from all buttons
            tabButtons.forEach(function(btn) {
                btn.classList.remove('active');
            });

            // Remove active class from all panels
            tabPanels.forEach(function(panel) {
                panel.classList.remove('active');
            });

            // Add active class to clicked button
            this.classList.add('active');

            // Show corresponding panel
            const targetPanel = document.getElementById('tab-' + targetTab);
            if (targetPanel) {
                targetPanel.classList.add('active');
            }

            console.log('✅ Switched to ' + targetTab + ' tab');
        });
    });

    console.log('✅ AI Tabs initialized with ' + tabButtons.length + ' tabs');
}

/**
 * Confirm Request Deletion
 */
function confirmRequestDeletion(event, stockCode) {
    const confirmed = confirm(`Are you sure you want to delete request for stock "${stockCode}"? This action cannot be undone.`);
    if (!confirmed) {
        event.preventDefault();
        return false;
    }
    return true;
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

/**
 * =================================
 * INITIALIZATION
 * =================================
 */

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 Request Detail.js: Initializing...');
    initRequestDetailPage();
});

// Global function exports for HTML onclick handlers
window.confirmRequestDeletion = confirmRequestDeletion;
window.toggleUserDropdown = toggleUserDropdown;
window.toggleMobileMenu = toggleMobileMenu;
window.closeMobileMenu = closeMobileMenu;