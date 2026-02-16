<?php
session_start();
require_once '../components/connection.php';
require_once '../models/dashboardModel.php';
require_once '../middleware/authMiddleware.php';

requireAdmin();

try {
    $dashboardModel = new dashboardModel();
    $statsData = $dashboardModel->getStatistics($con);
    
    $stats = $statsData['stats'];
    $recent_bookings = $statsData['recent_bookings'];
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Dashboard_Report_' . date('Y-m-d_His') . '.csv');
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Report header
    fputcsv($output, ['DASHBOARD ANALYTICS REPORT']);
    fputcsv($output, ['Generated:', date('F j, Y g:i A')]);
    fputcsv($output, []);
    
    // Statistics
    fputcsv($output, ['KEY PERFORMANCE INDICATORS']);
    fputcsv($output, ['Metric', 'Value']);
    fputcsv($output, ["Today's Bookings", $stats['today_bookings']]);
    fputcsv($output, ['Total Customers', $stats['total_users']]);
    fputcsv($output, ['Pending Bookings', $stats['pending_tables']]);
    fputcsv($output, ['Total Revenue', '₱' . number_format($stats['total_revenue'] / 100, 2)]);
    fputcsv($output, ['Total Bookings', $stats['total_bookings']]);
    fputcsv($output, []);
    
    // Recent bookings
    fputcsv($output, ['RECENT BOOKINGS']);
    fputcsv($output, ['Booking ID', 'Customer', 'Room', 'Check-in', 'Check-out', 'Total Price', 'Status']);
    
    foreach ($recent_bookings as $booking) {
        fputcsv($output, [
            $booking['booking_id'],
            $booking['customer_name'] ?: 'Guest',
            $booking['room_id'] ?: 'N/A',
            date('Y-m-d', strtotime($booking['check_in_date'])),
            date('Y-m-d', strtotime($booking['check_out_date'])),
            '₱' . number_format($booking['total_price'], 2),
            ucfirst($booking['status'])
        ]);
    }
    
    fclose($output);
    exit();
    
} catch (Exception $e) {
    http_response_code(500);
    echo 'Error: ' . $e->getMessage();
}
?>