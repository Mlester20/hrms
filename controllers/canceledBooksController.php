<?php
session_start();

require_once '../includes/flash.php';
require_once '../components/connection.php';
require_once '../models/canceledBooksModel.php';


    $model = new canceledBooksModel($con);
    $cancelledBookings = $model->getCanceledBookings();
    

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_booking_id'])) {
        $booking_id = intval($_POST['delete_booking_id']);
        if ($model->deleteCancelledBooking($booking_id)) {
            setFlash("success", "Cancelled Booking Deleted Successfully!");
            header('Location: ../admin/canceledBooks.php?deleted=1');
            exit();
        } else {
            setFlash("error", "Something Went Wrong Try Again!");
            header('Location: ../admin/canceledBooks.php?deleted=0');
            exit();
        }
    }

?>