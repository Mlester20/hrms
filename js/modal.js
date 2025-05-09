document.addEventListener('DOMContentLoaded', function() {
    // Function to handle the cancel booking buttons
    const cancelButtons = document.querySelectorAll('.cancel-btn');
    cancelButtons.forEach(button => {
        button.addEventListener('click', function() {
            const bookingId = this.getAttribute('data-bs-booking-id');
            // Here you would typically make an AJAX request to cancel the booking
            console.log('Cancelling booking ID:', bookingId);
            
            // Create a form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = window.location.href; // Current URL
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'booking_id';
            input.value = bookingId;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        });
    });
    
    // Function to properly open cancel modal from details modal
    window.openCancelModal = function(bookingId) {
        // First hide the current modal
        const detailsModal = bootstrap.Modal.getInstance(document.getElementById('detailsModal' + bookingId));
        if (detailsModal) {
            detailsModal.hide();
        }
        
        // Wait a bit for the previous modal to close
        setTimeout(() => {
            // Then show the cancel modal
            const cancelModal = new bootstrap.Modal(document.getElementById('cancelModal' + bookingId));
            cancelModal.show();
        }, 500);
    };
});