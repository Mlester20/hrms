<?php

function getTableBookings($con, $user_id) {
    $db = new Database();
    $con = $db->getConnection();
    try {
        $query = "SELECT 
            tr.reservation_id,
            tr.reservation_date,
            tr.time_slot,
            tr.guest_count,
            tr.special_requests,
            tr.status,
            rt.table_number,
            rt.capacity,
            rt.location
        FROM table_reservations tr
        JOIN restaurant_tables rt ON tr.table_id = rt.table_id
        WHERE tr.user_id = ?
        ORDER BY tr.reservation_date DESC, tr.time_slot ASC";

        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } catch (Exception $e) {
        $_SESSION['error'] = "Error fetching bookings: " . $e->getMessage();
        return [];
    }finally{
        $db->closeConnection();
    }
}

// Helper function to get status badge class
function getStatusBadgeClass($status) {
    switch(strtolower($status)) {
        case 'confirmed':
            return 'bg-success';
        case 'pending':
            return 'bg-warning text-dark';
        case 'completed':
            return 'bg-info';
        case 'cancelled':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}
?>