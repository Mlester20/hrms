<?php

header('Content-Type: application/json');
session_start();
include '../components/config.php';

// Set default response
$response = [
    'status' => 'error',
    'message' => 'An unexpected error occurred',
    'data' => []
];

try {
    // Get search parameters
    $check_in_date = isset($_GET['check_in_date']) ? $_GET['check_in_date'] : '';
    $check_out_date = isset($_GET['check_out_date']) ? $_GET['check_out_date'] : '';
    $room_type_id = isset($_GET['room_type']) ? $_GET['room_type'] : '';

    // Validate dates
    $today = date('Y-m-d');
    
    if (empty($check_in_date) || empty($check_out_date)) {
        throw new Exception("Please select both check-in and check-out dates.");
    }

    if ($check_in_date < $today) {
        throw new Exception("Check-in date cannot be in the past.");
    }

    if ($check_out_date <= $check_in_date) {
        throw new Exception("Check-out date must be after check-in date.");
    }

    // Base query to get rooms
    $query = "SELECT r.id, r.title, r.room_type_id, r.images, r.price, rt.title as room_type, rt.detail as room_type_detail
              FROM rooms r
              JOIN room_type rt ON r.room_type_id = rt.id
              WHERE 1=1";
    
    // Add room type filter if specified
    if (!empty($room_type_id)) {
        $query .= " AND r.room_type_id = " . mysqli_real_escape_string($con, $room_type_id);
    }
    
    // Execute query to get all rooms matching the criteria
    $result = mysqli_query($con, $query);
    
    if (!$result) {
        throw new Exception("Database error: " . mysqli_error($con));
    }
    
    $rooms = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rooms[] = $row;
    }

    // For each room, check if it's available during the selected dates
    $available_rooms = [];
    
    foreach ($rooms as $room) {
        // Check if the room is already booked for the selected dates
        $booking_query = "SELECT * FROM bookings 
                          WHERE room_id = " . $room['id'] . " 
                          AND status != 'cancelled'
                          AND (
                              (check_in_date <= '" . mysqli_real_escape_string($con, $check_in_date) . "' AND check_out_date > '" . mysqli_real_escape_string($con, $check_in_date) . "')
                              OR 
                              (check_in_date < '" . mysqli_real_escape_string($con, $check_out_date) . "' AND check_out_date >= '" . mysqli_real_escape_string($con, $check_out_date) . "')
                              OR 
                              (check_in_date >= '" . mysqli_real_escape_string($con, $check_in_date) . "' AND check_out_date <= '" . mysqli_real_escape_string($con, $check_out_date) . "')
                          )";
        
        $booking_result = mysqli_query($con, $booking_query);
        
        if (!$booking_result) {
            throw new Exception("Database error: " . mysqli_error($con));
        }
        
        // If no bookings found, the room is available
        if (mysqli_num_rows($booking_result) == 0) {
            // Process images
            $images = $room['images'];
            $image_base_path = '../uploads';
            $decoded_images = json_decode($images, true);
            $room['images_array'] = is_array($decoded_images) ? array_map('trim', $decoded_images) : [];
            
            // Calculate number of nights
            $check_in = new DateTime($check_in_date);
            $check_out = new DateTime($check_out_date);
            $interval = $check_in->diff($check_out);
            $nights = $interval->days;
            
            // Calculate total price
            $room['total_price'] = $room['price'] * $nights;
            $room['nights'] = $nights;
            
            $available_rooms[] = $room;
        }
    }
    
    // Prepare response
    $response = [
        'status' => 'success',
        'message' => count($available_rooms) > 0 ? count($available_rooms) . ' available rooms found' : 'No available rooms for the selected dates',
        'data' => $available_rooms,
        'search' => [
            'check_in_date' => $check_in_date,
            'check_out_date' => $check_out_date,
            'room_type_id' => $room_type_id
        ]
    ];
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Return response
echo json_encode($response);
?>