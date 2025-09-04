// Request Detail Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    console.log('Request Detail page loaded');
});

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

// Make function globally available
window.confirmRequestDeletion = confirmRequestDeletion;