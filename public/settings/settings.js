// Setting Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    console.log('Settings page initialized');
    
    // Get the settings form and submit button
    const settingsForm = document.getElementById('settings-update-form');
    const submitButton = document.getElementById('settings-update-btn');
    
    if (submitButton && settingsForm) {
        console.log('Settings form and button found');
        
        // Add click event listener to submit button
        submitButton.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default button behavior
            console.log('Submit button clicked');
            
            // Manually submit the form
            settingsForm.submit();
        });
        
        console.log('Submit handler attached');
    } else {
        console.log('Settings form or button not found');
        console.log('Form found:', !!settingsForm);
        console.log('Button found:', !!submitButton);
    }
});