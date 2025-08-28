/**
 * =================================
 * SETTINGS PAGE JAVASCRIPT
 * =================================
 */

// Global variables
let settingsFormSubmitting = false;

/**
 * Initialize Settings Page
 */
function initSettingsPage() {
    initSettingsForm();
    initPasswordToggle();
    initFormValidation();
    console.log('✅ Settings page initialized');
}

/**
 * =================================
 * FORM HANDLING FUNCTIONS
 * =================================
 */

/**
 * Initialize Settings Form
 */
function initSettingsForm() {
    const form = document.querySelector('.settings-form');
    if (!form) return;

    form.addEventListener('submit', handleSettingsFormSubmit);
    
    // Add loading state management
    const submitBtn = form.querySelector('.settings-btn-primary');
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            if (settingsFormSubmitting) {
                e.preventDefault();
                return false;
            }
        });
    }
}

/**
 * Handle Settings Form Submit
 */
function handleSettingsFormSubmit(e) {
    // Don't prevent default - let Laravel handle the form submission
    // Just add loading state
    
    if (settingsFormSubmitting) {
        e.preventDefault();
        return false;
    }
    
    const form = e.target;
    const submitBtn = form.querySelector('.settings-btn-primary');
    
    if (submitBtn && !settingsFormSubmitting) {
        showSettingsLoading(submitBtn);
        settingsFormSubmitting = true;
        
        // Reset loading state after timeout in case of errors
        setTimeout(() => {
            hideSettingsLoading(submitBtn);
            settingsFormSubmitting = false;
        }, 10000); // 10 seconds timeout
    }
}

/**
 * Show Loading State
 */
function showSettingsLoading(button) {
    if (!button) return;
    
    button.disabled = true;
    button.classList.add('settings-loading');
    
    const originalText = button.textContent;
    button.setAttribute('data-original-text', originalText);
    
    button.innerHTML = '<span class="settings-spinner"></span>Updating...';
}

/**
 * Hide Loading State
 */
function hideSettingsLoading(button) {
    if (!button) return;
    
    button.disabled = false;
    button.classList.remove('settings-loading');
    
    const originalText = button.getAttribute('data-original-text') || 'Update';
    button.textContent = originalText;
    button.removeAttribute('data-original-text');
}

/**
 * =================================
 * PASSWORD TOGGLE FUNCTIONS
 * =================================
 */

/**
 * Initialize Password Toggle
 */
function initPasswordToggle() {
    const passwordFields = document.querySelectorAll('.settings-password-field');
    
    passwordFields.forEach(field => {
        const container = field.closest('.settings-password-container');
        if (!container) return;
        
        // Create toggle button if not exists
        let toggle = container.querySelector('.settings-password-toggle');
        if (!toggle) {
            toggle = createPasswordToggle(field.id);
            container.appendChild(toggle);
        }
        
        toggle.addEventListener('click', function() {
            toggleSettingsPassword(field.id);
        });
    });
}

/**
 * Create Password Toggle Button
 */
function createPasswordToggle(fieldId) {
    const toggle = document.createElement('span');
    toggle.className = 'settings-password-toggle';
    toggle.setAttribute('data-field', fieldId);
    toggle.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
    `;
    return toggle;
}

/**
 * Toggle Password Visibility
 */
function toggleSettingsPassword(fieldId) {
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
    const toggle = document.querySelector(`[data-field="${fieldId}"]`);
    if (!toggle) return;
    
    const svg = toggle.querySelector('svg');
    if (!svg) return;
    
    if (isVisible) {
        // Show "eye-off" icon (password is visible)
        svg.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
        `;
    } else {
        // Show "eye" icon (password is hidden)
        svg.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        `;
    }
}

/**
 * =================================
 * FORM VALIDATION FUNCTIONS
 * =================================
 */

/**
 * Initialize Form Validation
 */
function initFormValidation() {
    const form = document.querySelector('.settings-form');
    if (!form) return;

    const inputs = form.querySelectorAll('.settings-input');
    
    inputs.forEach(input => {
        // Real-time validation
        input.addEventListener('blur', function() {
            validateSettingsField(this);
        });
        
        input.addEventListener('input', function() {
            // Clear errors on input
            clearSettingsFieldError(this);
        });
    });
}

/**
 * Validate Individual Field
 */
function validateSettingsField(field) {
    const value = field.value.trim();
    const fieldName = field.name;
    let isValid = true;
    let errorMessage = '';

    // Clear previous errors
    clearSettingsFieldError(field);

    // Validation rules
    switch (fieldName) {
        case 'full_name':
            if (!value) {
                isValid = false;
                errorMessage = 'Full name is required';
            } else if (value.length < 2) {
                isValid = false;
                errorMessage = 'Full name must be at least 2 characters';
            }
            break;

        case 'email':
            if (!value) {
                isValid = false;
                errorMessage = 'Email is required';
            } else if (!isValidEmail(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid email address';
            }
            break;

        case 'mobile_number':
            if (value && !isValidPhoneNumber(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid mobile number';
            }
            break;

        case 'new_password':
            if (value && value.length < 8) {
                isValid = false;
                errorMessage = 'Password must be at least 8 characters';
            }
            break;
    }

    if (!isValid) {
        showSettingsFieldError(field, errorMessage);
    }

    return isValid;
}

/**
 * Show Field Error
 */
function showSettingsFieldError(field, message) {
    field.classList.add('is-invalid');
    
    let errorElement = field.parentNode.querySelector('.settings-error');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'settings-error';
        field.parentNode.appendChild(errorElement);
    }
    
    errorElement.textContent = message;
}

/**
 * Clear Field Error
 */
function clearSettingsFieldError(field) {
    field.classList.remove('is-invalid');
    
    const errorElement = field.parentNode.querySelector('.settings-error');
    if (errorElement && !errorElement.textContent.includes('Already Registered')) {
        errorElement.remove();
    }
}

/**
 * =================================
 * VALIDATION HELPER FUNCTIONS
 * =================================
 */

/**
 * Validate Email Format
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Validate Phone Number Format
 */
function isValidPhoneNumber(phone) {
    // Basic phone validation - adjust regex as needed
    const phoneRegex = /^[\+]?[0-9\-\(\)\s]{8,20}$/;
    return phoneRegex.test(phone);
}

/**
 * =================================
 * MESSAGE FUNCTIONS
 * =================================
 */

/**
 * Show Settings Message
 */
function showSettingsMessage(message, type = 'info') {
    // Remove existing messages
    const existingMessages = document.querySelectorAll('.settings-message');
    existingMessages.forEach(msg => msg.remove());
    
    // Create new message
    const messageDiv = document.createElement('div');
    messageDiv.className = `settings-message settings-${type} settings-fade-in`;
    messageDiv.textContent = message;
    
    // Insert at top of form or container
    const container = document.querySelector('.settings-form') || 
                     document.querySelector('.settings-container') ||
                     document.querySelector('.admin-content-container');
    
    if (container && container.firstChild) {
        container.insertBefore(messageDiv, container.firstChild);
    } else if (container) {
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
 * ACCESSIBILITY FUNCTIONS
 * =================================
 */

/**
 * Improve Form Accessibility
 */
function improveSettingsAccessibility() {
    const form = document.querySelector('.settings-form');
    if (!form) return;

    // Add ARIA labels and descriptions
    const inputs = form.querySelectorAll('.settings-input');
    inputs.forEach(input => {
        const label = form.querySelector(`label[for="${input.id}"]`) || 
                     input.parentNode.querySelector('.settings-label');
        
        if (label && !input.getAttribute('aria-labelledby')) {
            const labelId = `label-${input.id}`;
            label.id = labelId;
            input.setAttribute('aria-labelledby', labelId);
        }
    });

    // Add form validation ARIA attributes
    form.setAttribute('novalidate', ''); // Use custom validation
    form.setAttribute('aria-live', 'polite');
}

/**
 * =================================
 * UTILITY FUNCTIONS
 * =================================
 */

/**
 * Debounce Function
 */
function settingsDebounce(func, wait) {
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
 * =================================
 * INITIALIZATION
 * =================================
 */

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initSettingsPage();
    improveSettingsAccessibility();
});

// Handle page visibility change
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        // Reset form state if user comes back to page
        settingsFormSubmitting = false;
        const submitBtn = document.querySelector('.settings-btn-primary');
        if (submitBtn) {
            hideSettingsLoading(submitBtn);
        }
    }
});

// Export functions for backward compatibility and testing
window.settingsPageFunctions = {
    initSettingsPage,
    showSettingsMessage,
    toggleSettingsPassword,
    validateSettingsField
};