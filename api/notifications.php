<?php
// Ensure we have no output before our JSON
ob_start();

include '../components/config.php';

// Error handling to prevent HTML errors in JSON response
try {
    // Query with joins to get full booking context
    $query = "SELECT 
        b.booking_id,
        b.check_in_date,
        b.check_out_date,
        b.total_price,
        b.status as booking_status,
        b.payment_status,
        b.special_requests,
        b.is_read,
        b.created_at,
        u.name as guest_name,
        u.email as guest_email,
        r.title as room_title,
        rt.title as room_type
    FROM bookings b
    LEFT JOIN users u ON b.user_id = u.user_id
    LEFT JOIN rooms r ON b.room_id = r.id
    LEFT JOIN room_type rt ON r.room_type_id = rt.id
    ORDER BY b.created_at DESC
    LIMIT 5";

    $result = mysqli_query($con, $query);
    
    if (!$result) {
        throw new Exception("Database query failed: " . mysqli_error($con));
    }

    $notifications = [];

    while ($row = mysqli_fetch_assoc($result)) {
        // Format dates
        $checkIn = date("M d, Y", strtotime($row['check_in_date']));
        $checkOut = date("M d, Y", strtotime($row['check_out_date']));

        // Compose notification message
        $message = "New booking from {$row['guest_name']} ({$row['guest_email']}) for a {$row['room_type']} - {$row['room_title']}. ";
        $message .= "Stay from $checkIn to $checkOut. Status: {$row['booking_status']}, Payment: {$row['payment_status']}.";

        if (!empty($row['special_requests'])) {
            $message .= " Request: {$row['special_requests']}.";
        }

        $notifications[] = [    
            'id' => $row['booking_id'],
            'message' => $message,
            'is_read' => (int)$row['is_read'],
            'created_at' => $row['created_at']
        ];
    }

    // Clear any potential output before sending JSON
    ob_clean();
    
    // Set proper headers
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    
    // Return JSON response
    echo json_encode($notifications);
    
} catch (Exception $e) {
    // Clear potential output
    ob_clean();
    
    // Return proper JSON error response
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
?>