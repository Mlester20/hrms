<?php
session_start();

include '../components/connection.php';
require_once '../models/client/bookingsModel.php';

if (!isset($user_id)) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    } else {
        http_response_code(401);
        echo "User not authenticated";
        exit;
    }
}

$user_id = $_SESSION['user_id'];

$bookings = new bookingsModel($con);
$result = $bookings->getUserBookings($con, $user_id);

try{
    // Process booking cancellation if requested
    if (isset($_POST['cancel_booking']) && isset($_POST['booking_id'])) {
        $booking_id = $_POST['booking_id'];

        $bookings->cancelBooking($con, $booking_id, $user_id);
        $success_message = "Booking #" . $booking_id . " has been cancelled successfully.";

        header('location: ../views/bookings.php');
        exit();
    }

}catch(Exception $e){
    $error_message = $e->getMessage();
}


// Function to get badge class based on booking status
function getStatusBadgeClass($status) {
    switch(strtolower($status)) {
        case 'confirmed':
            return 'bg-success';
        case 'pending':
            return 'bg-warning text-dark';
        case 'canceled':
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