<?php
ob_start();

include '../components/config.php';

// Error handling to prevent HTML errors in JSON response
try {
    // Query with joins to get full booking context, including recent status changes
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
        b.updated_at,
        u.name as guest_name,
        u.email as guest_email,
        r.title as room_title,
        rt.title as room_type
    FROM bookings b
    LEFT JOIN users u ON b.user_id = u.user_id
    LEFT JOIN rooms r ON b.room_id = r.id
    LEFT JOIN room_type rt ON r.room_type_id = rt.id
    WHERE b.status IN ('pending', 'confirmed', 'cancelled')
    ORDER BY b.updated_at DESC
    LIMIT 10";

    $result = mysqli_query($con, $query);
    
    if (!$result) {
        throw new Exception("Database query failed: " . mysqli_error($con));
    }

    $notifications = [];

    while ($row = mysqli_fetch_assoc($result)) {
        // Format dates
        $checkIn = date("M d, Y", strtotime($row['check_in_date']));
        $checkOut = date("M d, Y", strtotime($row['check_out_date']));

        // Compose notification message based on booking status
        $message = "";
        $notificationType = "";

        switch ($row['booking_status']) {
            case 'pending':
                $message = "New booking from {$row['guest_name']} ({$row['guest_email']}) for {$row['room_type']} - {$row['room_title']}. ";
                $message .= "Stay: $checkIn to $checkOut. Payment: {$row['payment_status']}.";
                $notificationType = "new_booking";
                break;
                
            case 'confirmed':
                $message = "Booking confirmed for {$row['guest_name']} - {$row['room_type']} ({$row['room_title']}). ";
                $message .= "Stay: $checkIn to $checkOut. Payment: {$row['payment_status']}.";
                $notificationType = "booking_confirmed";
                break;
                
            case 'cancelled':
                $message = "⚠️ CANCELLATION: {$row['guest_name']} cancelled their booking for {$row['room_type']} - {$row['room_title']}. ";
                $message .= "Original stay: $checkIn to $checkOut. Amount: ₱" . number_format($row['total_price'], 2) . ".";
                $notificationType = "booking_cancelled";
                break;
                
            default:
                continue 2;
        }

        // Add special requests if any
        if (!empty($row['special_requests'])) {
            $message .= " Special request: " . substr($row['special_requests'], 0, 50);
            if (strlen($row['special_requests']) > 50) {
                $message .= "...";
            }
        }

        $notifications[] = [
            'id' => $row['booking_id'],
            'message' => $message,
            'type' => $notificationType,
            'status' => $row['booking_status'],
            'is_read' => (int)$row['is_read'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
            'guest_name' => $row['guest_name'],
            'room_title' => $row['room_title'],
            'check_in' => $checkIn,
            'check_out' => $checkOut
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