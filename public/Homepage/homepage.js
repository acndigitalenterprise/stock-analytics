/**
 * Homepage JavaScript
 * Handles tab switching, password toggle, and form validation
 */

/**
 * Tab switching functionality
 * @param {string} tabName - The name of the tab to switch to
 */
function switchTab(tabName) {
    // Hide all tab panels
    const panels = document.querySelectorAll('.homepage-tab-panel');
    panels.forEach(panel => panel.classList.remove('homepage-active'));
    
    // Remove active class from all tab buttons
    const tabs = document.querySelectorAll('.homepage-tab-btn');
    tabs.forEach(tab => tab.classList.remove('homepage-active'));
    
    // Show selected panel and activate corresponding tab
    document.getElementById(tabName + '-panel').classList.add('homepage-active');
    document.getElementById(tabName + '-tab').classList.add('homepage-active');
}

/**
 * Password toggle functionality
 * @param {string} inputId - The ID of the password input
 * @param {HTMLElement} toggleElement - The toggle button element
 */
function togglePassword(inputId, toggleElement) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById('icon-' + inputId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464m1.414 1.414L19.071 19.07m-9.193-9.193l4.242 4.242"/>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}

/**
 * Initialize homepage functionality
 */
function initializeHomepage() {
    // Terms agreement checkbox functionality
    const termsCheckbox = document.getElementById('terms_agreement');
    const signupBtn = document.getElementById('signup-btn');
    
    if (termsCheckbox && signupBtn) {
        // Enable/disable button based on checkbox state
        termsCheckbox.addEventListener('change', function() {
            signupBtn.disabled = !this.checked;
        });
    }
}

/**
 * Check for errors and show appropriate tab
 */
function handleErrorDisplay() {
    // Check if there are any signup validation errors
    const hasSignupErrors = document.querySelector('#signup-panel .homepage-error-message') !== null;
    const hasEmailError = window.homepageErrors && window.homepageErrors.email;
    const hasFullNameError = window.homepageErrors && window.homepageErrors.full_name;
    const hasPasswordError = window.homepageErrors && window.homepageErrors.password;
    const hasSignupError = window.homepageErrors && window.homepageErrors.signup_error;
    
    // If there are signup-related errors, show signup tab
    if (hasSignupErrors || hasEmailError || hasFullNameError || hasPasswordError || hasSignupError) {
        switchTab('signup');
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeHomepage();
    handleErrorDisplay();
});