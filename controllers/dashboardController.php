<?php
require_once '../models/dashboardModel.php';

    if(!isset($con)) {
        require_once '../components/connection.php';
    }
    
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $dashboardModel = new dashboardModel();
    $statsData = $dashboardModel->getStatistics($con);
    list($pie_statuses, $pie_counts, $bar_data, $line_data) = $dashboardModel->getChartData($con);
    
    // Extract statistics
    $stats = $statsData['stats'] ?? [];
    $pie_statuses = $statsData['pie_statuses'] ?? [];
    $pie_counts = $statsData['pie_counts'] ?? [];
    $payment_statuses = $statsData['payment_statuses'] ?? [];
    $payment_counts = $statsData['payment_counts'] ?? [];
    $recent_bookings = $statsData['recent_bookings'] ?? [];

?>