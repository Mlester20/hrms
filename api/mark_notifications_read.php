<?php
include '../components/connection.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $stmt = $con->prepare("UPDATE bookings SET is_read = 1 WHERE booking_id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'No ID provided']);
}
?>