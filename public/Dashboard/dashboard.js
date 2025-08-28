/**
 * Dashboard Page JavaScript
 * Handles market data refresh functionality
 */

/**
 * Refresh market data
 * Handles the refresh button click and page reload
 */
function refreshMarketData() {
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = '🔄 Loading...';
    button.disabled = true;
    
    // Create a form to trigger refresh
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = window.location.href;
    form.style.display = 'none';
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    // Add refresh parameter
    const refreshInput = document.createElement('input');
    refreshInput.type = 'hidden';
    refreshInput.name = 'refresh_market';
    refreshInput.value = '1';
    
    form.appendChild(csrfInput);
    form.appendChild(refreshInput);
    document.body.appendChild(form);
    
    // Reload the page after a short delay
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}