<?php

    class BaseModel{
        protected $con;

        public function __construct($con){
            $this->con = $con;
        }
    }

    class canceledBooksModel extends BaseModel {
        protected $table = 'bookings';
        protected $usersTable = 'users';
        protected $roomsTable = 'rooms';
        protected $roomTypeTable = 'room_type';

        public function getCanceledBookings() {
            try {
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
                        FROM {$this->table} b
                        LEFT JOIN {$this->usersTable} u ON b.user_id = u.user_id
                        LEFT JOIN {$this->roomsTable} r ON b.room_id = r.id
                        LEFT JOIN {$this->roomTypeTable} rt ON r.room_type_id = rt.id
                        WHERE b.status = 'cancelled'
                        ORDER BY b.created_at DESC";

                $stmt = $this->con->prepare($query);
                
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $this->con->error);
                }

                $stmt->execute();
                $result = $stmt->get_result();
                return $result;

            } catch (Exception $e) {
                throw new Exception("Error fetching cancelled books: " . $e->getMessage());
            }
        }

        public function deleteCancelledBooking($booking_id) {
            $query = "DELETE FROM {$this->table} WHERE booking_id = ? AND status = 'cancelled'";
            
            $stmt = $this->con->prepare($query);
            if (!$stmt) return false;

            $stmt->bind_param('i', $booking_id);
            $success = $stmt->execute();
            $stmt->close();
            
            return $success;
        }

        public function getPaymentBadgeClass($status) {
            switch(strtolower($status)) {
                case 'paid': return 'bg-success';
                case 'pending': return 'bg-warning text-dark';
                case 'refunded': return 'bg-info';
                case 'failed': return 'bg-danger';
                default: return 'bg-secondary';
            }
        }

        public function calculateNights($checkin, $checkout) {
            $checkin_date = new DateTime($checkin);
            $checkout_date = new DateTime($checkout);
            $interval = $checkin_date->diff($checkout_date);
            return $interval->days;
        }
    }

?>