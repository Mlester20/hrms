<?php


ini_set('display_errors', 0); 
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Include database connection
include '../components/config.php';

// Check for database connection
if (!isset($con) || $con->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

try {
    // Perform query
    $query = "SELECT id, title, room_type_id, images, price FROM rooms ORDER BY id DESC limit 3";
    $result = $con->query($query);
    
    $rooms = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Handle the images JSON properly
            if ($row['images'] !== null) {
                $decodedImages = json_decode($row['images'], true);
                if ($decodedImages === null && json_last_error() !== JSON_ERROR_NONE) {
                    // If it's not valid JSON, treat it as a single image string
                    $row['images'] = [$row['images']];
                } else {
                    $row['images'] = $decodedImages;
                }
            } else {
                $row['images'] = []; // Empty array if null
            }
            
            $rooms[] = $row;
        }
    }
    
    // Set headers and return JSON data
    header('Content-Type: application/json');
    echo json_encode($rooms);
    exit();
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error fetching rooms: ' . $e->getMessage()]);
    exit();
}
?>