<?php
session_start();
include '../components/config.php';

if (!isset($user_id)) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    } else {
        die("User not authenticated");
    }
}

$user_id = $_SESSION['user_id'];

// Process booking cancellation if requested
if (isset($_POST['cancel_booking']) && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    
    // Update booking status to canceled (match your enum value)
    $cancel_query = "UPDATE bookings SET status = 'canceled' WHERE booking_id = ? AND user_id = ?";
    $cancel_stmt = mysqli_prepare($con, $cancel_query);
    mysqli_stmt_bind_param($cancel_stmt, 'ii', $booking_id, $user_id);
    
    if (mysqli_stmt_execute($cancel_stmt)) {
        $success_message = "Booking #" . $booking_id . " has been cancelled successfully.";
    } else {
        $error_message = "Failed to cancel booking. Please try again. Error: " . mysqli_error($con);
    }
    
    mysqli_stmt_close($cancel_stmt);

    header('location: ../public/bookings.php');
    exit();
}

// Query to fetch only bookings for the current user
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
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC";

// Prepare statement
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Function to get badge class based on booking status
function getStatusBadgeClass($status) {
    switch(strtolower($status)) {
        case 'confirmed':
            return 'bg-success';
        case 'pending':
            return 'bg-warning text-dark';
        case 'canceled': // Note: changed from 'cancelled' to match your enum
            return 'bg-danger';
        case 'completed':
            return 'bg-info';
        default:
            return 'bg-secondary';
    }
}

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
?>