<?php
// create_booking.php - API endpoint to create a new booking
header('Content-Type: application/json');
session_start();
include '../components/config.php';

// Set default response
$response = [
    'status' => 'error',
    'message' => 'An unexpected error occurred',
    'data' => null
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

try {
    // Get form data
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }
    
    // Required fields
    $required_fields = ['room_id', 'check_in_date', 'check_out_date', 'total_price'];
    
    // If user is not logged in, these fields are also required
    if (!isset($_SESSION['user_id'])) {
        $required_fields = array_merge($required_fields, ['name', 'email', 'phone', 'address']);
    }
    
    // Validate required fields
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("$field is required");
        }
    }
    
    // Extract data
    $room_id = mysqli_real_escape_string($con, $data['room_id']);
    $check_in_date = mysqli_real_escape_string($con, $data['check_in_date']);
    $check_out_date = mysqli_real_escape_string($con, $data['check_out_date']);
    $total_price = mysqli_real_escape_string($con, $data['total_price']);
    $special_requests = isset($data['special_requests']) ? mysqli_real_escape_string($con, $data['special_requests']) : '';
    $payment_method = isset($data['payment_method']) ? mysqli_real_escape_string($con, $data['payment_method']) : 'pay_later';
    
    // Validate dates
    $today = date('Y-m-d');
    
    if ($check_in_date < $today) {
        throw new Exception("Check-in date cannot be in the past.");
    }
    
    if ($check_out_date <= $check_in_date) {
        throw new Exception("Check-out date must be after check-in date.");
    }
    
    // Check if the room is still available for the selected dates
    $availability_query = "SELECT * FROM bookings 
                          WHERE room_id = '$room_id' 
                          AND status != 'cancelled'
                          AND (
                              (check_in_date <= '$check_in_date' AND check_out_date > '$check_in_date')
                              OR 
                              (check_in_date < '$check_out_date' AND check_out_date >= '$check_out_date')
                              OR 
                              (check_in_date >= '$check_in_date' AND check_out_date <= '$check_out_date')
                          )";
    
    $availability_result = mysqli_query($con, $availability_query);
    
    if (!$availability_result) {
        throw new Exception("Database error: " . mysqli_error($con));
    }
    
    if (mysqli_num_rows($availability_result) > 0) {
        throw new Exception("Sorry, this room is no longer available for the selected dates.");
    }
    
    // User handling
    $user_id = null;
    
    if (isset($_SESSION['user_id'])) {
        // User is logged in, use their ID
        $user_id = $_SESSION['user_id'];
    } else {
        // Check if user with this email already exists
        $email = mysqli_real_escape_string($con, $data['email']);
        $user_query = "SELECT user_id FROM users WHERE email = '$email'";
        $user_result = mysqli_query($con, $user_query);
        
        if ($user_result && mysqli_num_rows($user_result) > 0) {
            // User exists, get ID
            $user_data = mysqli_fetch_assoc($user_result);
            $user_id = $user_data['user_id'];
        } else {
            // Create new user
            $name = mysqli_real_escape_string($con, $data['name']);
            $phone = mysqli_real_escape_string($con, $data['phone']);
            $address = mysqli_real_escape_string($con, $data['address']);
            
            // Generate a random password (user can reset later if needed)
            $temp_password = bin2hex(random_bytes(8));
            $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);
            
            $insert_user_query = "INSERT INTO users (name, email, password, address, phone, role) 
                                 VALUES ('$name', '$email', '$hashed_password', '$address', '$phone', 'customer')";
            
            if (!mysqli_query($con, $insert_user_query)) {
                throw new Exception("Failed to create user: " . mysqli_error($con));
            }
            
            $user_id = mysqli_insert_id($con);
        }
    }
    
    // Set initial status based on payment method
    $status = 'pending';
    $payment_status = $payment_method === 'pay_online' ? 'paid' : 'pending';
    
    // Create booking
    $insert_booking_query = "INSERT INTO bookings (
                               user_id, room_id, check_in_date, check_out_date, 
                               total_price, status, payment_status, special_requests, 
                               created_at, updated_at
                             ) VALUES (
                               '$user_id', '$room_id', '$check_in_date', '$check_out_date',
                               '$total_price', '$status', '$payment_status', '$special_requests',
                               NOW(), NOW()
                             )";
    
    if (!mysqli_query($con, $insert_booking_query)) {
        throw new Exception("Failed to create booking: " . mysqli_error($con));
    }
    
    $booking_id = mysqli_insert_id($con);
    
    // Generate a booking reference number (you can customize this format)
    $booking_reference = 'BK' . str_pad($booking_id, 6, '0', STR_PAD_LEFT);
    
    // Return success response
    $response = [
        'status' => 'success',
        'message' => 'Booking created successfully',
        'data' => [
            'booking_id' => $booking_id,
            'booking_reference' => $booking_reference,
            'user_id' => $user_id,
            'room_id' => $room_id,
            'check_in_date' => $check_in_date,
            'check_out_date' => $check_out_date,
            'total_price' => $total_price,
            'status' => $status,
            'payment_status' => $payment_status
        ]
    ];
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Return response
echo json_encode($response);
?>