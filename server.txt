<?php
function getReservations($con) {
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
        rt.title as room_type
    FROM bookings b
    LEFT JOIN users u ON b.user_id = u.user_id
    LEFT JOIN rooms r ON b.room_id = r.id
    LEFT JOIN room_type rt ON r.room_type_id = rt.id
    ORDER BY b.created_at DESC";
    
    return mysqli_query($con, $query);
}

function updateBookingStatus($con, $booking_id, $status, $type) {
    $valid_booking_status = ['pending', 'confirmed', 'cancelled', 'completed'];
    $valid_payment_status = ['unpaid', 'partially_paid', 'paid'];
    
    if ($type === 'booking' && in_array($status, $valid_booking_status)) {
        $query = "UPDATE bookings SET status = ? WHERE booking_id = ?";
    } elseif ($type === 'payment' && in_array($status, $valid_payment_status)) {
        $query = "UPDATE bookings SET payment_status = ? WHERE booking_id = ?";
    } else {
        return false;
    }
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "si", $status, $booking_id);
    return mysqli_stmt_execute($stmt);
}
?>