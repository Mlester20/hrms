// Get the form and submit button elements
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.querySelector('.contact-us form');
    const submitButton = contactForm.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;
    
    // Add event listener to the form
    contactForm.addEventListener('submit', function(e) {
        // Disable the button to prevent multiple submissions
        submitButton.disabled = true;
        
        // Show the loading animation
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
        
        // Add the pulsing effect class
        submitButton.classList.add('btn-pulse');
        
        // No need for timeout - the form will submit naturally and the PHP will handle the alert
        // The animation will show during the network request processing time
        
        // We don't prevent default - let the form submit normally
        // Your PHP code will handle the redirect and alert
    });
});