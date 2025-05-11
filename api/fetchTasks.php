<?php
session_start();
require_once '../components/config.php'; // Make sure this path is correct

header('Content-Type: application/json');

if (!isset($_SESSION['staff_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access'
    ]);
    exit();
}

$staff_id = $_SESSION['staff_id'];

// Set error handling for debugging
ini_set('display_errors', 0); // Don't print errors in the response
ini_set('log_errors', 1);     // Log errors to your server logs
error_reporting(E_ALL);       // Report all errors

// Perform query to fetch tasks
try {
    $query = "SELECT t.*, s.name as staff_name 
              FROM tasks t
              LEFT JOIN staffs s ON t.staff_id = s.staff_id 
              WHERE t.staff_id = ?
              ORDER BY t.deadline ASC";
              
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $tasks = array();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Format dates for display
            $row['deadline'] = date('M d, Y h:i A', strtotime($row['deadline']));
            $row['created_at'] = date('M d, Y', strtotime($row['created_at']));
            $row['status_class'] = match($row['status']) {
                'Completed' => 'success',
                'In Progress' => 'warning',
                default => 'danger'
            };
            
            $tasks[] = $row;
        }
        
        // Return success response with tasks
        echo json_encode([
            'status' => 'success',
            'data' => $tasks
        ]);
    } else {
        // Return empty response
        echo json_encode([
            'status' => 'success',
            'data' => [],
            'message' => 'No tasks found'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

// Close connections
$stmt->close();
$con->close();
?>