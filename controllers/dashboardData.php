<?php
function getBookingsData($con) {
    $query = "SELECT DATE(created_at) as date, COUNT(*) as count, SUM(total_price) as revenue,
              COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed,
              COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
              COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled
              FROM bookings 
              GROUP BY DATE(created_at)
              ORDER BY date DESC LIMIT 7";
    $result = mysqli_query($con, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getTableReservationsStats($con) {
    $query = "SELECT status, COUNT(*) as count 
              FROM table_reservations 
              GROUP BY status";
    $result = mysqli_query($con, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getDashboardStats($con) {
    // Total Bookings Today
    $today_bookings = mysqli_fetch_assoc(mysqli_query($con, 
        "SELECT COUNT(*) as count FROM bookings WHERE DATE(created_at) = CURDATE()"))['count'];
    
    // Total Revenue
    $total_revenue = mysqli_fetch_assoc(mysqli_query($con, 
        "SELECT SUM(total_price) as total FROM bookings WHERE status = 'confirmed'"))['total'];
    
    // Pending Table Reservations
    $pending_tables = mysqli_fetch_assoc(mysqli_query($con, 
        "SELECT COUNT(*) as count FROM table_reservations WHERE status = 'pending'"))['count'];
    
    // Total Active Bookings
    $active_bookings = mysqli_fetch_assoc(mysqli_query($con, 
        "SELECT COUNT(*) as count FROM bookings WHERE status = 'confirmed' 
         AND check_out_date >= CURDATE()"))['count'];

    return [
        'today_bookings' => $today_bookings,
        'total_revenue' => $total_revenue,
        'pending_tables' => $pending_tables,
        'active_bookings' => $active_bookings
    ];
}
?>