// Password toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    // Find all eye icons in input groups
    const eyeIcons = document.querySelectorAll('.input-group .fa-eye, .input-group .fa-eye-slash');
    
    eyeIcons.forEach(function(eyeIcon) {
        // Add click event to the parent span
        eyeIcon.parentElement.addEventListener('click', function() {
            // Find the password input in the same input-group
            const inputGroup = this.closest('.input-group');
            const passwordInput = inputGroup.querySelector('input[type="password"], input[type="text"]');
            const icon = this.querySelector('i');
            
            // Toggle password visibility
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Add cursor pointer style
        eyeIcon.parentElement.style.cursor = 'pointer';
    });
});