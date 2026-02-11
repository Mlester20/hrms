<?php
session_start();

require_once '../components/config.php';
require_once '../models/roomReservationsModel.php';
require_once '../includes/flash.php';

// Initialize the model
$reservationModel = new roomReservationsModel($con);

// Run auto-completion check
$auto_completed = $reservationModel->runAutoCompletionCheck();

// Get current page from URL parameter
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, $current_page);

// Fetch reservations with pagination
$reservations_data = $reservationModel->getReservations($current_page, 5);

// Handle status updates
if (isset($_POST['update_status'])) {
    try {
        $booking_id = $_POST['booking_id'];
        $new_status = $_POST['new_status'];
        $type = $_POST['status_type'];
        
        if ($reservationModel->updateBookingStatus($booking_id, $new_status, $type)) {
            setFlash("success", "Status updated successfully!");
        } else {
            setFlash("error", "Failed to update status!");
        }
    } catch (Exception $e) {
        setFlash("error", $e->getMessage());
    }
    
    header('Location: ../admin/room_reservations.php');
    exit();
}

?>