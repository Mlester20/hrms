<?php

    class BaseModel{
        protected $con;

        public function __construct($con){
            $this->con = $con;
        }
    }

    class bookingsModel extends BaseModel{
        protected $bookings = 'bookings';
        protected $users = 'users';
        protected $rooms = 'rooms';
        protected $room_type = 'room_type';

        public function getUserBookings($user_id){
            try{
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
                    FROM {$this->bookings} b
                    LEFT JOIN {$this->users} u ON b.user_id = u.user_id
                    LEFT JOIN {$this->rooms} r ON b.room_id = r.id
                    LEFT JOIN {$this->room_type} rt ON r.room_type_id = rt.id
                    WHERE b.user_id = ?
                    ORDER BY b.created_at DESC";

                // Prepare statement
                $stmt = mysqli_prepare($this->con, $query);
                mysqli_stmt_bind_param($stmt, 'i', $user_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                return $result; 
            }catch(Exception $e){
                throw new Exception("Error getting bookings " . $e->getMessage(), 500);
            }
        }

        public function cancelBooking($booking_id, $user_id){
            try{
                $cancel_query = "UPDATE {$this->bookings} SET status = 'cancelled' WHERE booking_id = ? AND user_id = ?";
                $cancel_stmt = mysqli_prepare($this->con, $cancel_query);
                mysqli_stmt_bind_param($cancel_stmt, 'ii', $booking_id, $user_id);

                $success = mysqli_stmt_execute($cancel_stmt);
                mysqli_stmt_close($cancel_stmt);

                if (!$success) {
                    throw new Exception("Failed to cancel booking. Error: " . mysqli_error($this->con));
                }

                return true;
            }catch(Exception $e){
                throw new Exception("Error cancelling booking: " . $e->getMessage(), 500);
            }
        }

    }

?>