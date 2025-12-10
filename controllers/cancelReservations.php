<?php
session_start();
include '../components/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['reservation_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$reservation_id = intval($_POST['reservation_id']);
$user_id = $_SESSION['user_id'];

try {
    $checkQuery = "SELECT reservation_id, status FROM table_reservations WHERE reservation_id = ? AND user_id = ?";
    $checkStmt = mysqli_prepare($con, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, "ii", $reservation_id, $user_id);
    mysqli_stmt_execute($checkStmt);
    $result = mysqli_stmt_get_result($checkStmt);
    
    if (mysqli_num_rows($result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Reservation not found or access denied']);
        exit();
    }
    
    $reservation = mysqli_fetch_assoc($result);
    
    // Check if reservation can be cancelled (only pending reservations)
    if ($reservation['status'] !== 'pending') {
        echo json_encode(['success' => false, 'message' => 'Only pending reservations can be cancelled']);
        exit();
    }
    
    // Update the reservation status to 'cancelled'
    $cancelQuery = "UPDATE table_reservations SET status = 'cancelled' WHERE reservation_id = ? AND user_id = ?";
    $cancelStmt = mysqli_prepare($con, $cancelQuery);
    mysqli_stmt_bind_param($cancelStmt, "ii", $reservation_id, $user_id);
    
    if (mysqli_stmt_execute($cancelStmt)) {
        if (mysqli_affected_rows($con) > 0) {
            echo json_encode(['success' => true, 'message' => 'Reservation cancelled successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to cancel reservation']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($con)]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}finally{
    $db->closeConnection();
}

mysqli_close($con);
?>