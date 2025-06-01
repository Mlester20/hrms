<?php
session_start();
include '../components/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_booking_id'])) {
    $booking_id = intval($_POST['delete_booking_id']);
    if (deleteCanceledBooking($con, $booking_id)) {
        header('Location: ../admin/canceledBooks.php?deleted=1');
        exit();
    } else {
        header('Location: ../admin/canceledBooks.php?deleted=0');
        exit();
    }
}

// Query to fetch only cancelled bookings
$query = "SELECT 
        b.booking_id,
        b.check_in_date,
        b.check_out_date,
        b.total_price,
        b.status as booking_status,
        b.payment_status,
        b.special_requests,
        b.created_at,
        u.name as guest_name,
        u.email as guest_email,
        r.title as room_title,
        r.images as room_images,
        rt.title as room_type
    FROM bookings b
    LEFT JOIN users u ON b.user_id = u.user_id
    LEFT JOIN rooms r ON b.room_id = r.id
    LEFT JOIN room_type rt ON r.room_type_id = rt.id
    WHERE b.status = 'canceled'
    ORDER BY b.created_at DESC";

// Prepare statement
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Function to get badge class based on payment status
function getPaymentBadgeClass($status) {
    switch(strtolower($status)) {
        case 'paid':
            return 'bg-success';
        case 'pending':
            return 'bg-warning text-dark';
        case 'refunded':
            return 'bg-info';
        case 'failed':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}

// Calculate number of nights between check-in and check-out
function calculateNights($checkin, $checkout) {
    $checkin_date = new DateTime($checkin);
    $checkout_date = new DateTime($checkout);
    $interval = $checkin_date->diff($checkout_date);
    return $interval->days;
}

//function to delete booking
function deleteCanceledBooking($con, $booking_id) {
    $query = "DELETE FROM bookings WHERE booking_id = ? AND status = 'canceled'";
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) return false;
    mysqli_stmt_bind_param($stmt, 'i', $booking_id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}

?>