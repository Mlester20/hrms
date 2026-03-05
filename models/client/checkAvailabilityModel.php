<?php


    class CheckAvailabilityModel{
        protected $rooms = 'rooms';
        protected $room_type = 'room_type';
        protected $bookings = 'bookings';
        private $con;
        
        public function __construct($con){
            $this->con = $con;
        }

        public function getAvailableRooms($check_in, $check_out, $room_type_id = null) {

            if (!empty($room_type_id)) {
                $sql = "
                    SELECT r.*, rt.title AS room_type_title
                    FROM {$this->rooms} r
                    LEFT JOIN {$this->room_type} rt ON rt.id = r.room_type_id
                    WHERE r.id NOT IN (
                        SELECT b.room_id
                        FROM {$this->bookings} b
                        WHERE b.status NOT IN ('cancelled', 'rejected')
                        AND b.check_in_date  < ?
                        AND b.check_out_date > ?
                    )
                    AND r.room_type_id = ?
                    ORDER BY r.price ASC
                ";
                $stmt = mysqli_prepare($this->con, $sql);
                mysqli_stmt_bind_param($stmt, 'sss', $check_out, $check_in, $room_type_id);
            } else {
                $sql = "
                    SELECT r.*, rt.title AS room_type_title
                    FROM {$this->rooms} r
                    LEFT JOIN {$this->room_type} rt ON rt.id = r.room_type_id
                    WHERE r.id NOT IN (
                        SELECT b.room_id
                        FROM {$this->bookings} b
                        WHERE b.status NOT IN ('cancelled', 'rejected')
                        AND b.check_in_date  < ?
                        AND b.check_out_date > ?
                    )
                    ORDER BY r.price ASC
                ";
                $stmt = mysqli_prepare($this->con, $sql);
                mysqli_stmt_bind_param($stmt, 'ss', $check_out, $check_in);
            }

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $rooms = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $rooms[] = $row;
            }

            mysqli_stmt_close($stmt);
            return $rooms;
        }

        public function getRoomTypes() {
            $sql = "
                SELECT rt.id, rt.title
                FROM {$this->room_type} rt
                INNER JOIN {$this->rooms} r ON r.room_type_id = rt.id
                GROUP BY rt.id, rt.title
                ORDER BY rt.title ASC
            ";
            $result = mysqli_query($this->con, $sql);

            $types = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $types[] = $row;
            }
            return $types;
        }

        /**
         * Quick single-room availability check. Returns true if the room is free.
         */
        public function isRoomAvailable($room_id, $check_in, $check_out) {
            $sql = "
                SELECT COUNT(*) AS cnt
                FROM {$this->bookings}
                WHERE room_id = ?
                AND status NOT IN ('cancelled', 'rejected')
                AND check_in_date  < ?
                AND check_out_date > ?
            ";
            $stmt = mysqli_prepare($this->con, $sql);
            mysqli_stmt_bind_param($stmt, 'iss', $room_id, $check_out, $check_in);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $row    = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            return (int)$row['cnt'] === 0;
        }
    }

?>