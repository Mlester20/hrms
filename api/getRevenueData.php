<?php
session_start();
require_once '../components/connection.php';
require_once '../models/dashboardModel.php';
require_once '../middleware/authMiddleware.php';

requireAdmin();

header('Content-Type: application/json');

    if (!isset($_GET['period'])) {
        echo json_encode(['error' => 'Period not specified']);
        exit;
    }

    $period = $_GET['period'];
    $allowedPeriods = ['year', 'month', 'week'];

    if (!in_array($period, $allowedPeriods)) {
        echo json_encode(['error' => 'Invalid period']);
        exit;
    }

    try {
        $dashboardModel = new dashboardModel();
        $revenueData = $dashboardModel->getRevenueData($con, $period);
        
        echo json_encode([
            'success' => true,
            'labels' => $revenueData['labels'],
            'data' => $revenueData['data']
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }
?>