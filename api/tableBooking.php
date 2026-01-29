<?php
include '../components/config.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set content type header for api endpoint responses
header('Content-Type: application/json');

function returnError($message) {
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

function returnSuccess($data = [], $message = '') {
    $response = ['success' => true];
    if (!empty($message)) {
        $response['message'] = $message;
    }
    if (!empty($data)) {
        $response = array_merge($response, $data);
    }
    echo json_encode($response);
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
        // Check if we're getting availability for a specific date and time
        if (isset($_GET['check_availability'])) {
            $date = $_GET['date'] ?? date('Y-m-d');
            $table_id = $_GET['table_id'] ?? null;
            
            if ($table_id) {
                // Get availability for a specific table
                $query = "
                    SELECT time_slot, status 
                    FROM table_reservations 
                    WHERE table_id = ? 
                    AND reservation_date = ? 
                    AND status IN ('pending', 'confirmed')
                    ORDER BY time_slot ASC
                ";
                
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "is", $table_id, $date);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                $reservedSlots = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $reservedSlots[] = $row['time_slot'];
                }
                
                returnSuccess(['reserved_slots' => $reservedSlots]);
            } else {
                // Get overall availability summary for the day
                $query = "
                    SELECT 
                        time_slot,
                        COUNT(*) as reserved_count,
                        (SELECT COUNT(*) FROM restaurant_tables) as total_tables
                    FROM table_reservations 
                    WHERE reservation_date = ? 
                    AND status IN ('pending', 'confirmed')
                    GROUP BY time_slot
                    ORDER BY time_slot ASC
                ";
                
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "s", $date);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                $availability = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $total = $row['total_tables'];
                    $reserved = $row['reserved_count'];
                    $available = $total - $reserved;
                    
                    $status = 'available';
                    if ($available == 0) {
                        $status = 'full';
                    } elseif ($available <= 2) {
                        $status = 'few_left';
                    }
                    
                    $availability[] = [
                        'time_slot' => $row['time_slot'],
                        'available_tables' => $available,
                        'total_tables' => $total,
                        'status' => $status
                    ];
                }
                
                returnSuccess(['availability' => $availability]);
            }
        } else {
            // Get all tables (default behavior)
            $currentDate = $_GET['date'] ?? date('Y-m-d');
            
            $query = "SELECT * FROM restaurant_tables ORDER BY table_id ASC";
            
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($result) {
                $tables = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $tables[] = $row;
                }
                returnSuccess(['tables' => $tables]);
            } else {
                returnError(mysqli_error($con));
            }
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
        
        $table_id = intval($data['table_id']);
        $reservation_date = $data['reservation_date'];
        $time_slot = $data['time_slot'];
        $guest_count = intval($data['guest_count']);
        $special_requests = isset($data['special_requests']) ? $data['special_requests'] : '';
        $user_id = $_SESSION['user_id'];
        
        // Validate date is not in the past
        if (strtotime($reservation_date) < strtotime(date('Y-m-d'))) {
            returnError("Cannot make reservations for past dates");
        }
        
        // Check if this specific table is available at this specific time slot
        $check_query = "
            SELECT reservation_id, status 
            FROM table_reservations 
            WHERE table_id = ? 
            AND reservation_date = ? 
            AND time_slot = ?
            AND status IN ('pending', 'confirmed')
        ";
        
        $check_stmt = mysqli_prepare($con, $check_query);
        mysqli_stmt_bind_param($check_stmt, "iss", $table_id, $reservation_date, $time_slot);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            returnError("This table is already reserved for the selected time slot. Please choose a different table or time.");
        }
        
        mysqli_stmt_close($check_stmt);
        
        // Verify table capacity matches guest count
        $capacity_query = "SELECT capacity FROM restaurant_tables WHERE table_id = ?";
        $capacity_stmt = mysqli_prepare($con, $capacity_query);
        mysqli_stmt_bind_param($capacity_stmt, "i", $table_id);
        mysqli_stmt_execute($capacity_stmt);
        $capacity_result = mysqli_stmt_get_result($capacity_stmt);
        
        if ($capacity_row = mysqli_fetch_assoc($capacity_result)) {
            if ($guest_count > $capacity_row['capacity']) {
                returnError("Guest count exceeds table capacity. This table can accommodate up to " . $capacity_row['capacity'] . " guests.");
            }
        } else {
            returnError("Invalid table selected");
        }
        
        mysqli_stmt_close($capacity_stmt);
        
        // Insert the reservation
        $insert_query = "
            INSERT INTO table_reservations 
            (table_id, reservation_date, time_slot, guest_count, special_requests, user_id, status)
            VALUES (?, ?, ?, ?, ?, ?, 'pending')
        ";
        
        $insert_stmt = mysqli_prepare($con, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "issisi", 
            $table_id, 
            $reservation_date, 
            $time_slot, 
            $guest_count, 
            $special_requests, 
            $user_id
        );
        
        if (mysqli_stmt_execute($insert_stmt)) {
            $reservation_id = mysqli_insert_id($con);
            returnSuccess([
                'reservation_id' => $reservation_id
            ], 'Reservation created successfully');
        } else {
            returnError("Failed to create reservation: " . mysqli_error($con));
        }
        
        mysqli_stmt_close($insert_stmt);
        
    } else {
        returnError("Unsupported request method");
    }
} catch (Exception $e) {
    returnError("An error occurred: " . $e->getMessage());
}
?>