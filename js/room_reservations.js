$(document).ready(function () {
    $('#reservationsTable').DataTable({
        paging: false,
        ordering: true,
        info: false,
        searching: true
    });
});

/* ==========================
   STATUS COLOR HELPERS
========================== */

function getStatusColor(status) {
    switch (status) {
        case 'pending':
            return '#ffc107';
        case 'confirmed':
            return '#28a745';
        case 'canceled':
        case 'cancelled':
            return '#dc3545';
        case 'complete':
        case 'completed':
            return '#0dcaf0';
        default:
            return '#6c757d';
    }
}

function getPaymentStatusColor(status) {
    switch (status) {
        case 'unpaid':
            return '#dc3545';
        case 'partially_paid':
            return '#ffc107';
        case 'paid':
            return '#28a745';
        default:
            return '#6c757d';
    }
}

/* ==========================
   MODAL DETAILS HANDLER
========================== */

function showDetails(data) {
    // Guest information
    document.getElementById('guestName').textContent = data.guest_name;
    document.getElementById('guestEmail').textContent = data.guest_email;

    // Booking information
    document.getElementById('bookingId').textContent = data.booking_id;

    const bookingStatusBadge = document.getElementById('bookingStatus');
    bookingStatusBadge.textContent =
        data.booking_status.charAt(0).toUpperCase() +
        data.booking_status.slice(1);

    bookingStatusBadge.style.backgroundColor =
        getStatusColor(data.booking_status);
    bookingStatusBadge.style.color = '#ffffff';

    // Room details
    document.getElementById('roomTitle').textContent = data.room_title;
    document.getElementById('roomType').textContent = data.room_type;

    // Dates
    const checkInDate = new Date(data.check_in_date);
    const checkOutDate = new Date(data.check_out_date);

    document.getElementById('checkIn').textContent =
        checkInDate.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });

    document.getElementById('checkOut').textContent =
        checkOutDate.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });

    // Payment information
    document.getElementById('totalPrice').textContent =
        '₱' +
        parseFloat(data.total_price).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

    const paymentStatusBadge = document.getElementById('paymentStatus');
    paymentStatusBadge.textContent =
        data.payment_status === 'partially_paid'
            ? 'Partially Paid'
            : data.payment_status.charAt(0).toUpperCase() +
              data.payment_status.slice(1);

    paymentStatusBadge.style.backgroundColor =
        getPaymentStatusColor(data.payment_status);
    paymentStatusBadge.style.color = '#ffffff';
}
