<?php

    class bookingsModel{
        public function getUserBookings($con, $user_id){
            try{
                // Query to fetch only bookings for the current user
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
                        r.images as room_images,
                        rt.title as room_type
                    FROM bookings b
                    LEFT JOIN users u ON b.user_id = u.user_id
                    LEFT JOIN rooms r ON b.room_id = r.id
                    LEFT JOIN room_type rt ON r.room_type_id = rt.id
                    WHERE b.user_id = ?
                    ORDER BY b.created_at DESC";

                // Prepare statement
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, 'i', $user_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                return $result; 
            }catch(Exception $e){
                throw new Exception("Error getting bookings " . $e->getMessage(), 500);
            }
        }

        public function cancelBooking($con, $booking_id, $user_id){
            try{
                $cancel_query = "UPDATE bookings SET status = 'cancelled' WHERE booking_id = ? AND user_id = ?";
                $cancel_stmt = mysqli_prepare($con, $cancel_query);
                mysqli_stmt_bind_param($cancel_stmt, 'ii', $booking_id, $user_id);

                $success = mysqli_stmt_execute($cancel_stmt);
                mysqli_stmt_close($cancel_stmt);

                if (!$success) {
                    throw new Exception("Failed to cancel booking. Error: " . mysqli_error($con));
                }

                return true;
            }catch(Exception $e){
                throw new Exception("Error cancelling booking: " . $e->getMessage(), 500);
            }
        }

    }

?>