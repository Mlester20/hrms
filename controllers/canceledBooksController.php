<?php
    
require_once '../models/canceledBooksModel.php';

    $canceledBooksModel = new canceledBooksModel();

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

    // Fetch canceled bookings
    $canceledBookingsResult = $canceledBooksModel->getCanceledBookings($con);
    $canceledBookings = [];

?>