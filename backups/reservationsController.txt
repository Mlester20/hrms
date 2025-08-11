<?php
function getReservations($con) {
    $query = "SELECT 
        b.booking_id,
        b.check_in_date,
        b.check_out_date,
        b.total_price,
        b.status as booking_status,
        b.payment_status,
        b.special_requests,
        b.created_at,
        u.name as guest_name,
        u.email as guest_email,
        r.title as room_title,
        rt.title as room_type
    FROM bookings b
    LEFT JOIN users u ON b.user_id = u.user_id
    LEFT JOIN rooms r ON b.room_id = r.id
    LEFT JOIN room_type rt ON r.room_type_id = rt.id
    ORDER BY b.created_at DESC";
    
    return mysqli_query($con, $query);
}

function updateBookingStatus($con, $booking_id, $status, $type) {
    $valid_booking_status = ['pending', 'confirmed', 'cancelled', 'completed'];
    $valid_payment_status = ['unpaid', 'partially_paid', 'paid'];
    
    if ($type === 'booking' && in_array($status, $valid_booking_status)) {
        // Get current booking details including user_id and room info
        $result = mysqli_query($con, "
            SELECT b.status, b.user_id, b.check_in_date, b.check_out_date, 
                   r.title as room_title, r.id as room_id
            FROM bookings b 
            LEFT JOIN rooms r ON b.room_id = r.id 
            WHERE b.booking_id = $booking_id
        ");
        
        if ($result && $row = mysqli_fetch_assoc($result)) {
            $current_status = $row['status'];
            $user_id = $row['user_id'];
            $room_title = $row['room_title'];
            $room_id = $row['room_id'];
            $check_in_date = $row['check_in_date'];
            $check_out_date = $row['check_out_date'];
            
            // Prevent changing from confirmed or later back to pending
            if ($current_status !== 'pending' && $status === 'pending') {
                return false; // reject the update
            }
            
            // Update booking status
            $query = "UPDATE bookings SET status = ? WHERE booking_id = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "si", $status, $booking_id);
            $update_success = mysqli_stmt_execute($stmt);
            
            // If update successful and status changed, create notification
            if ($update_success && $current_status !== $status) {
                createBookingNotification($con, $user_id, $booking_id, $current_status, $status, $room_title, $room_id, $check_in_date, $check_out_date);
            }
            
            return $update_success;
        }
        return false;
        
    } elseif ($type === 'payment' && in_array($status, $valid_payment_status)) {
        // Get booking details for payment notifications
        $result = mysqli_query($con, "
            SELECT b.payment_status, b.user_id, b.total_price, 
                   r.title as room_title
            FROM bookings b 
            LEFT JOIN rooms r ON b.room_id = r.id 
            WHERE b.booking_id = $booking_id
        ");
        
        if ($result && $row = mysqli_fetch_assoc($result)) {
            $current_payment_status = $row['payment_status'];
            $user_id = $row['user_id'];
            $total_price = $row['total_price'];
            $room_title = $row['room_title'];
            
            // Update payment status
            $query = "UPDATE bookings SET payment_status = ? WHERE booking_id = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "si", $status, $booking_id);
            $update_success = mysqli_stmt_execute($stmt);
            
            // If update successful and status changed, create payment notification
            if ($update_success && $current_payment_status !== $status) {
                createPaymentNotification($con, $user_id, $booking_id, $current_payment_status, $status, $room_title, $total_price);
            }
            
            return $update_success;
        }
        return false;
    } else {
        return false;
    }
}

function createBookingNotification($con, $user_id, $booking_id, $old_status, $new_status, $room_title, $room_id, $check_in_date, $check_out_date) {
    $title = '';
    $message = '';
    $type = '';
    
    // Format dates for display
    $check_in_formatted = date('F j, Y', strtotime($check_in_date));
    $check_out_formatted = date('F j, Y', strtotime($check_out_date));
    $room_display = $room_title ? $room_title : "Room #$room_id";
    
    switch ($new_status) {
        case 'confirmed':
            $title = 'Booking Confirmed! ✅';
            $message = "Great news! Your booking for {$room_display} from {$check_in_formatted} to {$check_out_formatted} has been confirmed. We look forward to welcoming you!";
            $type = 'booking_confirmed';
            break;
            
        case 'cancelled':
            $title = 'Booking Cancelled ❌';
            $message = "Your booking for {$room_display} from {$check_in_formatted} to {$check_out_formatted} has been cancelled. If you have any questions, please contact us.";
            $type = 'booking_cancelled';
            break;
            
        case 'completed':
            $title = 'Stay Completed 🏨';
            $message = "Thank you for staying with us! Your booking for {$room_display} has been completed. We hope you had a wonderful experience.";
            $type = 'booking_completed';
            break;
            
        default:
            return; // Don't create notification for other status changes
    }
    
    // Insert notification
    $insert_query = "INSERT INTO notifications (user_id, booking_id, title, message, type, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($con, $insert_query);
    mysqli_stmt_bind_param($stmt, "iisss", $user_id, $booking_id, $title, $message, $type);
    mysqli_stmt_execute($stmt);
}

function createPaymentNotification($con, $user_id, $booking_id, $old_status, $new_status, $room_title, $total_price) {
    $title = '';
    $message = '';
    $type = 'payment_received';
    
    $formatted_price = number_format($total_price, 2);
    $room_display = $room_title ? $room_title : "your booking";
    
    switch ($new_status) {
        case 'paid':
            $title = 'Payment Received! 💳';
            $message = "We have received your full payment of ₱{$formatted_price} for {$room_display}. Thank you for your payment!";
            break;
            
        case 'partially_paid':
            $title = 'Partial Payment Received 💰';
            $message = "We have received a partial payment for {$room_display}. Remaining balance: ₱{$formatted_price}. Please complete your payment before check-in.";
            break;
            
        default:
            return; // Don't create notification for unpaid status
    }
    
    // Insert notification
    $insert_query = "INSERT INTO notifications (user_id, booking_id, title, message, type, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($con, $insert_query);
    mysqli_stmt_bind_param($stmt, "iisss", $user_id, $booking_id, $title, $message, $type);
    mysqli_stmt_execute($stmt);
}

// Function to create notifications table if it doesn't exist
function createNotificationsTable($con) {
    $query = "CREATE TABLE IF NOT EXISTS notifications (
        notification_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        booking_id INT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        type ENUM('booking_confirmed', 'booking_cancelled', 'booking_completed', 'payment_received', 'general') DEFAULT 'general',
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_booking_id (booking_id),
        INDEX idx_is_read (is_read),
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE SET NULL
    )";
    
    return mysqli_query($con, $query);
}

// Function to manually create notification (for admin use)
function createCustomNotification($con, $user_id, $title, $message, $type = 'general', $booking_id = null) {
    $insert_query = "INSERT INTO notifications (user_id, booking_id, title, message, type, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($con, $insert_query);
    mysqli_stmt_bind_param($stmt, "iisss", $user_id, $booking_id, $title, $message, $type);
    return mysqli_stmt_execute($stmt);
}

?>