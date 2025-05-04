<?php
include '../components/config.php';

// Error handling - prevent PHP errors from being displayed in the output
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set content type header for api endpoint responses
header('Content-Type: application/json');

function returnError($message) {
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

function checkUserLoggedIn() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_SESSION['user_id'])) {
            returnError('User not logged in');
        }
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($con) || mysqli_connect_errno()) {
    returnError("Database connection failed: " . mysqli_connect_error());
}

checkUserLoggedIn();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $currentDate = date('Y-m-d');
        
        // Use prepared statement to prevent SQL injection
        $query = "
            SELECT t.table_id, t.table_number, t.capacity, t.position_x, t.position_y, t.location,
                   IF(r.reservation_id IS NULL, 'available', 'reserved') AS status
            FROM restaurant_tables t
            LEFT JOIN table_reservations r
            ON t.table_id = r.table_id AND r.reservation_date = ?
        ";
        
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "s", $currentDate);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result) {
            $tables = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $tables[] = $row;
            }
            echo json_encode(['success' => true, 'tables' => $tables]);
        } else {
            returnError(mysqli_error($con));
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Add a new reservation
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required_fields = ['table_id', 'reservation_date', 'time_slot', 'guest_count'];
        foreach ($required_fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                returnError("Missing required field: $field");
            }
        }
        
        $table_id = $data['table_id'];
        $reservation_date = $data['reservation_date'];
        $time_slot = $data['time_slot'];
        $guest_count = $data['guest_count'];
        $special_requests = isset($data['special_requests']) ? $data['special_requests'] : '';
        $user_id = $_SESSION['user_id'];
        
        $check_query = "
            SELECT reservation_id FROM table_reservations 
            WHERE table_id = ? AND reservation_date = ? AND time_slot = ?
        ";
        
        $check_stmt = mysqli_prepare($con, $check_query);
        mysqli_stmt_bind_param($check_stmt, "iss", $table_id, $reservation_date, $time_slot);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            returnError("This table is already reserved for the selected date and time.");
        }
        $insert_query = "
            INSERT INTO table_reservations (table_id, reservation_date, time_slot, guest_count, special_requests, user_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ";
        
        $insert_stmt = mysqli_prepare($con, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "issisi", $table_id, $reservation_date, $time_slot, $guest_count, $special_requests, $user_id);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            echo json_encode(['success' => true, 'message' => 'Reservation added successfully']);
        } else {
            returnError(mysqli_error($con));
        }
    } else {
        returnError("Unsupported request method");
    }
} catch (Exception $e) {
    returnError("An error occurred: " . $e->getMessage());
}
?>