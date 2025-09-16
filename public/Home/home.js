// Home Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    console.log('Home page loaded');
    
    // Add any home page specific functionality here
    const buttons = document.querySelectorAll('.homepage-btn');
    
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.05)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});