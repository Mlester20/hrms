<?php

class roomReservationsModel {
    
    private $con;
    
    public function __construct($con) {
        $this->con = $con;
    }
    
    /**
     * Get all reservations with pagination
     */
    public function getReservations($page = 1, $perPage = 5) {
        try {
            // Calculate the offset
            $offset = ($page - 1) * $perPage;
            
            // Get total number of records for pagination
            $total_query = "SELECT COUNT(*) as total FROM bookings";
            $total_result = mysqli_query($this->con, $total_query);
            $total_row = mysqli_fetch_assoc($total_result);
            $total_records = $total_row['total'];
            
            // Calculate total pages
            $total_pages = ceil($total_records / $perPage);
            
            // Main query with LIMIT and OFFSET
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
            ORDER BY b.created_at DESC
            LIMIT $perPage OFFSET $offset";
            
            $result = mysqli_query($this->con, $query);
            
            return [
                'data' => $result,
                'total_pages' => $total_pages,
                'current_page' => $page,
                'total_records' => $total_records,
                'per_page' => $perPage
            ];
        } catch (Exception $e) {
            throw new Exception("Error fetching reservations: " . $e->getMessage());
        }
    }
    
    /**
     * Update booking status
     */
    public function updateBookingStatus($booking_id, $status, $type) {
        try {
            $valid_booking_status = ['pending', 'confirmed', 'cancelled', 'completed'];
            $valid_payment_status = ['unpaid', 'partially_paid', 'paid'];
            
            if ($type === 'booking' && in_array($status, $valid_booking_status)) {
                // Get current booking details including user_id and room info
                $result = mysqli_query($this->con, "
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
                        return false;
                    }
                    
                    // Only proceed if status is actually changing
                    if ($current_status === $status) {
                        return true;
                    }
                    
                    // Update booking status
                    $query = "UPDATE bookings SET status = ? WHERE booking_id = ?";
                    $stmt = mysqli_prepare($this->con, $query);
                    mysqli_stmt_bind_param($stmt, "si", $status, $booking_id);
                    $update_success = mysqli_stmt_execute($stmt);
                    
                    // Only create notification if update was successful and status actually changed
                    if ($update_success) {
                        $this->createBookingNotification($user_id, $booking_id, $current_status, $status, $room_title, $room_id, $check_in_date, $check_out_date);
                    }
                    
                    return $update_success;
                }
                return false;
                
            } elseif ($type === 'payment' && in_array($status, $valid_payment_status)) {
                // Get booking details for payment notifications
                $result = mysqli_query($this->con, "
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
                    
                    // Only proceed if status is actually changing
                    if ($current_payment_status === $status) {
                        return true;
                    }
                    
                    // Update payment status
                    $query = "UPDATE bookings SET payment_status = ? WHERE booking_id = ?";
                    $stmt = mysqli_prepare($this->con, $query);
                    mysqli_stmt_bind_param($stmt, "si", $status, $booking_id);
                    $update_success = mysqli_stmt_execute($stmt);
                    
                    // Only create notification if update was successful and status actually changed
                    if ($update_success) {
                        $this->createPaymentNotification($user_id, $booking_id, $current_payment_status, $status, $room_title, $total_price);
                    }
                    
                    return $update_success;
                }
                return false;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception("Error updating booking status: " . $e->getMessage());
        }
    }
    
    /**
     * Create booking notification
     */
    private function createBookingNotification($user_id, $booking_id, $old_status, $new_status, $room_title, $room_id, $check_in_date, $check_out_date) {
        // Check if notification already exists for this booking and status change
        $check_query = "SELECT COUNT(*) as count FROM notifications 
                       WHERE user_id = ? AND booking_id = ? AND type = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
        $notification_type = 'booking_' . $new_status;
        $check_stmt = mysqli_prepare($this->con, $check_query);
        mysqli_stmt_bind_param($check_stmt, "iis", $user_id, $booking_id, $notification_type);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        $check_row = mysqli_fetch_assoc($check_result);
        
        // If notification already exists within the last minute, don't create another one
        if ($check_row['count'] > 0) {
            error_log("Notification already exists for booking $booking_id, user $user_id, type $notification_type");
            return false;
        }
        
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
                return false;
        }
        
        // Insert notification
        $insert_query = "INSERT INTO notifications (user_id, booking_id, title, message, type, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($this->con, $insert_query);
        mysqli_stmt_bind_param($stmt, "iisss", $user_id, $booking_id, $title, $message, $type);
        $success = mysqli_stmt_execute($stmt);
        
        if ($success) {
            error_log("Created notification for booking $booking_id, user $user_id, type $type");
        } else {
            error_log("Failed to create notification for booking $booking_id: " . mysqli_error($this->con));
        }
        
        return $success;
    }
    
    /**
     * Create payment notification
     */
    private function createPaymentNotification($user_id, $booking_id, $old_status, $new_status, $room_title, $total_price) {
        // Check if notification already exists for this booking and payment status
        $check_query = "SELECT COUNT(*) as count FROM notifications 
                       WHERE user_id = ? AND booking_id = ? AND type = 'payment_received' AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
        $check_stmt = mysqli_prepare($this->con, $check_query);
        mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $booking_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        $check_row = mysqli_fetch_assoc($check_result);
        
        // If notification already exists within the last minute, don't create another one
        if ($check_row['count'] > 0) {
            error_log("Payment notification already exists for booking $booking_id, user $user_id");
            return false;
        }
        
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
                return false;
        }
        
        // Insert notification
        $insert_query = "INSERT INTO notifications (user_id, booking_id, title, message, type, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($this->con, $insert_query);
        mysqli_stmt_bind_param($stmt, "iisss", $user_id, $booking_id, $title, $message, $type);
        $success = mysqli_stmt_execute($stmt);
        
        if ($success) {
            error_log("Created payment notification for booking $booking_id, user $user_id");
        } else {
            error_log("Failed to create payment notification for booking $booking_id: " . mysqli_error($this->con));
        }
        
        return $success;
    }
    
    /**
     * Automatically mark bookings as completed when check-out date has passed
     */
    public function autoCompleteBookings() {
        try {
            // Get all confirmed bookings where check_out_date has passed and status is not already completed
            $query = "SELECT 
                b.booking_id,
                b.user_id,
                b.check_in_date,
                b.check_out_date,
                b.status,
                r.title as room_title,
                r.id as room_id
            FROM bookings b
            LEFT JOIN rooms r ON b.room_id = r.id
            WHERE b.status = 'confirmed' 
            AND DATE(b.check_out_date) < CURDATE()";
            
            $result = mysqli_query($this->con, $query);
            
            if (!$result) {
                error_log("Error fetching bookings for auto-completion: " . mysqli_error($this->con));
                return false;
            }
            
            $completed_count = 0;
            
            while ($booking = mysqli_fetch_assoc($result)) {
                // Update booking status to completed
                $update_query = "UPDATE bookings SET 
                    status = 'completed',
                    updated_at = NOW()
                    WHERE booking_id = ?";
                
                $stmt = mysqli_prepare($this->con, $update_query);
                mysqli_stmt_bind_param($stmt, "i", $booking['booking_id']);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Create auto-completion notification
                    $this->createAutoCompletionNotification(
                        $booking['user_id'],
                        $booking['booking_id'],
                        'confirmed',
                        'completed',
                        $booking['room_title'] ?: "Room #" . $booking['room_id'],
                        $booking['room_id'],
                        $booking['check_in_date'],
                        $booking['check_out_date']
                    );
                    
                    $completed_count++;
                    error_log("Auto-completed booking ID: " . $booking['booking_id']);
                } else {
                    error_log("Failed to auto-complete booking ID: " . $booking['booking_id'] . " - " . mysqli_error($this->con));
                }
                
                mysqli_stmt_close($stmt);
            }
            
            if ($completed_count > 0) {
                error_log("Auto-completed $completed_count bookings");
            }
            
            return $completed_count;
        } catch (Exception $e) {
            throw new Exception("Error auto-completing bookings: " . $e->getMessage());
        }
    }
    
    /**
     * Create notification for auto-completed bookings
     */
    private function createAutoCompletionNotification($user_id, $booking_id, $old_status, $new_status, $room_title, $room_id, $check_in_date, $check_out_date) {
        // Check if completion notification already exists for this booking
        $check_query = "SELECT COUNT(*) as count FROM notifications 
                       WHERE user_id = ? AND booking_id = ? AND type = 'booking_completed'";
        $check_stmt = mysqli_prepare($this->con, $check_query);
        mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $booking_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        $check_row = mysqli_fetch_assoc($check_result);
        
        // If notification already exists, don't create another one
        if ($check_row['count'] > 0) {
            return false;
        }
        
        $check_in_formatted = date('F j, Y', strtotime($check_in_date));
        $check_out_formatted = date('F j, Y', strtotime($check_out_date));
        $room_display = $room_title ? $room_title : "Room #$room_id";
        
        $title = 'Stay Completed - Thank You! 🏨';
        $message = "Thank you for staying with us! Your booking for {$room_display} (from {$check_in_formatted} to {$check_out_formatted}) has been automatically completed. We hope you had a wonderful experience and look forward to welcoming you back soon!";
        $type = 'booking_completed';
        
        // Insert notification
        $insert_query = "INSERT INTO notifications (user_id, booking_id, title, message, type, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($this->con, $insert_query);
        mysqli_stmt_bind_param($stmt, "iisss", $user_id, $booking_id, $title, $message, $type);
        $success = mysqli_stmt_execute($stmt);
        
        if ($success) {
            error_log("Created auto-completion notification for booking $booking_id, user $user_id");
        } else {
            error_log("Failed to create auto-completion notification for booking $booking_id: " . mysqli_error($this->con));
        }
        
        return $success;
    }
    
    /**
     * Run auto-completion check with throttling to prevent too frequent runs
     */
    public function runAutoCompletionCheck() {
        // Check if we've run this recently (within the last hour)
        $last_run_file = dirname(__FILE__) . '/last_auto_completion_check.txt';
        $current_time = time();
        
        if (file_exists($last_run_file)) {
            $last_run = (int)file_get_contents($last_run_file);
            // If last run was less than 1 hour ago, skip
            if (($current_time - $last_run) < 3600) {
                return 0; // No check performed
            }
        }
        
        // Run the auto-completion
        $completed_count = $this->autoCompleteBookings();
        
        // Update last run time
        file_put_contents($last_run_file, $current_time);
        
        return $completed_count;
    }
    
    /**
     * Create custom notification (for admin use)
     */
    public function createCustomNotification($user_id, $title, $message, $type = 'general', $booking_id = null) {
        try {
            $insert_query = "INSERT INTO notifications (user_id, booking_id, title, message, type, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = mysqli_prepare($this->con, $insert_query);
            mysqli_stmt_bind_param($stmt, "iisss", $user_id, $booking_id, $title, $message, $type);
            return mysqli_stmt_execute($stmt);
        } catch (Exception $e) {
            throw new Exception("Error creating notification: " . $e->getMessage());
        }
    }
    
    /**
     * Cleanup duplicate notifications
     */
    public function cleanupDuplicateNotifications() {
        try {
            $query = "
            DELETE n1 FROM notifications n1
            INNER JOIN notifications n2 
            WHERE n1.notification_id > n2.notification_id
            AND n1.user_id = n2.user_id
            AND n1.booking_id = n2.booking_id
            AND n1.type = n2.type
            AND n1.title = n2.title
            AND ABS(TIMESTAMPDIFF(SECOND, n1.created_at, n2.created_at)) < 10
            ";
            
            $result = mysqli_query($this->con, $query);
            if ($result) {
                $affected_rows = mysqli_affected_rows($this->con);
                error_log("Cleaned up $affected_rows duplicate notifications");
                return $affected_rows;
            }
            return false;
        } catch (Exception $e) {
            throw new Exception("Error cleaning up notifications: " . $e->getMessage());
        }
    }
}

?>