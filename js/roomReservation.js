// roomReservation.js - Client-side booking logic

document.addEventListener('DOMContentLoaded', function() {
    // Initialize date pickers
    const datePickerOptions = {
        minDate: "today",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "F j, Y",
        disableMobile: "true"
    };
    
    flatpickr("#check_in_date", {
        ...datePickerOptions,
        onChange: function(selectedDates, dateStr) {
            // Update check-out date minimum to be the day after check-in
            if (selectedDates[0]) {
                const nextDay = new Date(selectedDates[0]);
                nextDay.setDate(nextDay.getDate() + 1);
                
                const checkOutPicker = document.getElementById("check_out_date")._flatpickr;
                checkOutPicker.set("minDate", nextDay);
                
                // If current check-out date is before new check-in date, update it
                if (checkOutPicker.selectedDates[0] && checkOutPicker.selectedDates[0] <= selectedDates[0]) {
                    checkOutPicker.setDate(nextDay);
                }
            }
        }
    });
    
    flatpickr("#check_out_date", {
        ...datePickerOptions,
        minDate: new Date().fp_incr(1) // Default to tomorrow
    });
    
    // Handle search form submission
    const searchForm = document.getElementById('bookingSearchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            searchAvailableRooms();
        });
    }
    
    // Handle payment method selection
    const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');
    paymentMethodRadios.forEach(radio => {
        radio.addEventListener('change', togglePaymentDetails);
    });
    
    // Handle booking form submission
    const submitBookingBtn = document.getElementById('submitBooking');
    if (submitBookingBtn) {
        submitBookingBtn.addEventListener('click', submitBooking);
    }
    
    // If there are search parameters in the URL, load rooms
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('check_in_date') && urlParams.has('check_out_date')) {
        document.getElementById('check_in_date').value = urlParams.get('check_in_date');
        document.getElementById('check_out_date').value = urlParams.get('check_out_date');
        if (urlParams.has('room_type')) {
            document.getElementById('room_type').value = urlParams.get('room_type');
        }
        searchAvailableRooms();
    }
});

/**
 * Search for available rooms based on form inputs
 */
function searchAvailableRooms() {
    const checkInDate = document.getElementById('check_in_date').value;
    const checkOutDate = document.getElementById('check_out_date').value;
    const roomType = document.getElementById('room_type').value;
    
    // Show loading spinner
    document.getElementById('available-rooms-container').innerHTML = `
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Searching for available rooms...</p>
        </div>
    `;
    
    // Clear any previous status messages
    const statusMessageContainer = document.querySelector('.booking-status-message');
    statusMessageContainer.classList.remove('error', 'success');
    statusMessageContainer.style.display = 'none';
    statusMessageContainer.textContent = '';
    
    // Build the API URL
    const apiUrl = `../api/get_available_rooms.php?check_in_date=${checkInDate}&check_out_date=${checkOutDate}&room_type=${roomType}`;
    
    // Fetch available rooms
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displayAvailableRooms(data.data, data.search);
                
                // Update URL parameters for bookmarking/sharing
                const searchParams = new URLSearchParams(window.location.search);
                searchParams.set('check_in_date', checkInDate);
                searchParams.set('check_out_date', checkOutDate);
                if (roomType) {
                    searchParams.set('room_type', roomType);
                } else {
                    searchParams.delete('room_type');
                }
                const newUrl = `${window.location.pathname}?${searchParams.toString()}`;
                history.pushState({}, '', newUrl);
                
                // Show success message if specified
                if (data.message) {
                    statusMessageContainer.textContent = data.message;
                    statusMessageContainer.classList.add('success');
                    statusMessageContainer.style.display = 'block';
                }
            } else {
                // Show error message
                statusMessageContainer.textContent = data.message || 'An error occurred while searching for rooms.';
                statusMessageContainer.classList.add('error');
                statusMessageContainer.style.display = 'block';
                
                // Clear rooms container
                document.getElementById('available-rooms-container').innerHTML = '';
            }
        })
        .catch(error => {
            console.error('Error fetching available rooms:', error);
            statusMessageContainer.textContent = 'An error occurred while searching for rooms. Please try again.';
            statusMessageContainer.classList.add('error');
            statusMessageContainer.style.display = 'block';
            
            // Clear rooms container
            document.getElementById('available-rooms-container').innerHTML = '';
        });
}

/**
 * Display available rooms in the UI
 */
function displayAvailableRooms(rooms, searchParams) {
    const roomsContainer = document.getElementById('available-rooms-container');
    
    // Clear previous content
    roomsContainer.innerHTML = '';
    
    // DEBUG: Log the rooms data to console
    console.log('Rooms data:', rooms);
    
    if (rooms.length === 0) {
        // No rooms found
        roomsContainer.innerHTML = `
            <div class="col-12">
                <div class="no-rooms-message">
                    <i class="fas fa-bed"></i>
                    <h4>No Available Rooms</h4>
                    <p>We couldn't find any available rooms for your selected dates. Try different dates or room types.</p>
                    <button class="btn btn-outline-primary" onclick="resetSearch()">Reset Search</button>
                </div>
            </div>
        `;
        return;
    }
    
    // Display each room
    rooms.forEach((room, index) => {
        const roomCard = document.createElement('div');
        roomCard.className = 'col-md-6 col-lg-4 mb-4';
        
        const imagePath = '../uploads/';
        const firstImage = room.images_array.length > 0 ? 
            imagePath + room.images_array[0] : 
            'https://via.placeholder.com/300x200?text=No+Image';
        
        roomCard.innerHTML = `
            <div class="room-card">
                <div class="room-image">
                    <img src="${firstImage}" alt="${room.title}" onerror="this.onerror=null; this.src='https://via.placeholder.com/300x200?text=Image+Error'; console.error('Failed to load image: ' + this.src);">
                    <div class="room-type-badge">${room.room_type}</div>
                </div>
                <div class="room-body">
                    <h5 class="room-title">${room.title}</h5>
                    <div class="room-description">
                        ${room.room_type_detail || 'No description available'}
                    </div>
                    <div class="room-features">
                        <span><i class="fas fa-wifi"></i> Free WiFi</span>
                        <span class="ms-3"><i class="fas fa-air-freshener"></i> AC</span>
                        <span class="ms-3"><i class="fas fa-tv"></i> TV</span>
                    </div>
                    <div class="room-price">
                        $${room.price} <span class="room-price-unit">per night</span>
                    </div>
                    <div class="total-price mb-3">
                        <strong>Total for ${room.nights} nights:</strong> ₱${room.total_price}
                    </div>
                    <button class="btn btn-primary btn-book" data-room-id="${room.id}" data-room-title="${room.title}" 
                            data-room-type="${room.room_type}" data-room-price="${room.price}" data-total-price="${room.total_price}" 
                            data-nights="${room.nights}">
                        Book Now
                    </button>
                </div>
            </div>
        `;
        
        roomsContainer.appendChild(roomCard);
    });
    
    // Add event listeners for booking buttons
    document.querySelectorAll('.btn-book').forEach(button => {
        button.addEventListener('click', function() {
            openBookingModal(this.dataset, searchParams);
        });
    });
}

/**
 * Reset search form and results
 */
function resetSearch() {
    document.getElementById('bookingSearchForm').reset();
    document.getElementById('available-rooms-container').innerHTML = '';
    history.pushState({}, '', window.location.pathname);

    const statusMessageContainer = document.querySelector('.booking-status-message');
    statusMessageContainer.classList.remove('error', 'success');
    statusMessageContainer.style.display = 'none';
    statusMessageContainer.textContent = '';
    
    const datePickerOptions = {
        minDate: "today",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "F j, Y",
        disableMobile: "true"
    };
    
    flatpickr("#check_in_date", {
        ...datePickerOptions
    });
    
    flatpickr("#check_out_date", {
        ...datePickerOptions,
        minDate: new Date().fp_incr(1)
    });
}

/**
 * Open booking modal with room details
 */
function openBookingModal(roomData, searchParams) {
    const modal = document.getElementById('bookingModal');
    const modalInstance = new bootstrap.Modal(modal);
    
    document.getElementById('room_id').value = roomData.roomId;
    document.getElementById('modal_check_in_date').value = searchParams.check_in_date;
    document.getElementById('modal_check_out_date').value = searchParams.check_out_date;
    document.getElementById('total_price').value = roomData.totalPrice;
    
    // Update booking summary
    modal.querySelector('.room-title').textContent = `Room: ${roomData.roomTitle}`;
    modal.querySelector('.room-type').textContent = `Type: ${roomData.roomType}`;
    modal.querySelector('.booking-dates').textContent = `Dates: ${formatDate(searchParams.check_in_date)} - ${formatDate(searchParams.check_out_date)}`;
    modal.querySelector('.nights-count').textContent = `Duration: ${roomData.nights} night${roomData.nights > 1 ? 's' : ''}`;
    modal.querySelector('.room-price').textContent = `Price per night: $${roomData.roomPrice}`;
    modal.querySelector('.total-price-display').textContent = `Total: $${roomData.totalPrice}`;

    modalInstance.show();
}

/**
 * Toggle payment details visibility based on selected payment method
 */
function togglePaymentDetails() {
    const paymentDetails = document.getElementById('payment_details');
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    
    if (paymentMethod === 'pay_online') {
        paymentDetails.classList.remove('d-none');
        // Make card fields required
        document.querySelectorAll('#payment_details input').forEach(input => {
            input.setAttribute('required', '');
        });
    } else {
        paymentDetails.classList.add('d-none');
        // Remove required attribute from card fields
        document.querySelectorAll('#payment_details input').forEach(input => {
            input.removeAttribute('required');
        });
    }
}

/**
 * Submit booking form
 */
function submitBooking() {
    const form = document.getElementById('bookingForm');
    
    // Check if form is valid
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Get form data
    const formData = new FormData(form);
    const bookingData = {};
    
    // Convert FormData to object
    for (const [key, value] of formData.entries()) {
        bookingData[key] = value;
    }
    
    // Disable submit button and show loading state
    const submitButton = document.getElementById('submitBooking');
    const originalButtonText = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
    
    // Send AJAX request
    fetch('../controllers/createBooking.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(bookingData)
    })
    .then(response => response.json())
    .then(data => {
        // Re-enable submit button
        submitButton.disabled = false;
        submitButton.innerHTML = originalButtonText;
        
        if (data.status === 'success') {
            // Hide booking modal
            const bookingModal = bootstrap.Modal.getInstance(document.getElementById('bookingModal'));
            bookingModal.hide();
            
            // Update booking reference in confirmation modal
            document.getElementById('booking_reference').textContent = data.data.booking_reference;
            
            const confirmationModal = new bootstrap.Modal(document.getElementById('bookingConfirmationModal'));
            confirmationModal.show();
            form.reset();
            
            searchAvailableRooms();
        } else {
            alert(data.message || 'An error occurred while processing your booking.');
        }
    })
    .catch(error => {
        console.error('Error submitting booking:', error);
        alert('An error occurred while processing your booking. Please try again.');

        submitButton.disabled = false;
        submitButton.innerHTML = originalButtonText;
    });
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}