<?php
function getDashboardStats($con) {
    $stats = array();
    
    // Get today's bookings (excluding canceled ones)
    $today = date('Y-m-d');
    $today_query = "SELECT COUNT(*) as count 
                    FROM bookings 
                    WHERE DATE(created_at) = ? 
                    AND status NOT IN ('canceled', 'cancelled')";
    $stmt = mysqli_prepare($con, $today_query);
    mysqli_stmt_bind_param($stmt, 's', $today);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $stats['today_bookings'] = $row['count'];

    // Get total revenue (excluding canceled bookings)
    $revenue_query = "SELECT COALESCE(SUM(total_price), 0) as total 
                     FROM bookings 
                     WHERE status NOT IN ('canceled', 'cancelled')";
    $result = mysqli_query($con, $revenue_query);
    $row = mysqli_fetch_assoc($result);
    $stats['total_revenue'] = $row['total'];

    // Get pending table reservations
    $pending_tables_query = "SELECT COUNT(*) as count 
                           FROM table_reservations 
                           WHERE status = 'pending'";
    $result = mysqli_query($con, $pending_tables_query);
    $row = mysqli_fetch_assoc($result);
    $stats['pending_tables'] = $row['count'];

    // Get active bookings (excluding canceled ones)
    $active_query = "SELECT COUNT(*) as count 
                    FROM bookings 
                    WHERE status NOT IN ('canceled', 'cancelled', 'completed')";
    $result = mysqli_query($con, $active_query);
    $row = mysqli_fetch_assoc($result);
    $stats['active_bookings'] = $row['count'];

    return $stats;
}

function getBookingsData($con) {
    // Get bookings data for the last 7 days (excluding canceled bookings)
    $query = "SELECT 
                DATE(created_at) as date,
                COUNT(*) as count,
                COALESCE(SUM(total_price), 0) as revenue
            FROM bookings 
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            AND status NOT IN ('canceled', 'cancelled')
            GROUP BY DATE(created_at)
            ORDER BY date ASC";
            
    $result = mysqli_query($con, $query);
    $data = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = array(
            'date' => date('M d', strtotime($row['date'])),
            'count' => (int)$row['count'],
            'revenue' => (float)$row['revenue']
        );
    }
    
    return $data;
}

function getTableReservationsStats($con) {
    $query = "SELECT 
                status,
                COUNT(*) as count
            FROM table_reservations
            GROUP BY status";
            
    $result = mysqli_query($con, $query);
    $data = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    return $data;
}