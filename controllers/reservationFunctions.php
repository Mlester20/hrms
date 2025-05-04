<?php
include '../components/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $reservation_id = $_POST['reservation_id'];

    if ($action === 'mark_done') {
        // Mark reservation as done
        $query = "UPDATE table_reservations SET status = 'done' WHERE reservation_id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('i', $reservation_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Reservation marked as done']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update reservation status']);
        }
    } elseif ($action === 'delete') {
        // Delete reservation
        $query = "DELETE FROM table_reservations WHERE reservation_id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('i', $reservation_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Reservation deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete reservation']);
        }
    } elseif ($action === 'print_receipt') {
        // Fetch reservation details for receipt
        $query = "
            SELECT r.*, t.table_number, t.capacity, u.name, u.email
            FROM table_reservations r
            INNER JOIN restaurant_tables t ON r.table_id = t.table_id
            INNER JOIN users u ON r.user_id = u.user_id
            WHERE r.reservation_id = ?
        ";
        $stmt = $con->prepare($query);
        $stmt->bind_param('i', $reservation_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $reservation = $result->fetch_assoc();

        if ($reservation) {
            echo json_encode(['success' => true, 'reservation' => $reservation]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Reservation not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
}
?>