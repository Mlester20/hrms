<?php

    class TablesModel {
        public function getReservations($con) {
            try{
                $query = "
                    SELECT 
                        r.reservation_id,
                        r.reservation_date,
                        r.time_slot,
                        r.guest_count,
                        r.special_requests,
                        r.status, -- Include the status column
                        t.table_number,
                        t.capacity,
                        u.name,
                        u.email
                    FROM table_reservations r
                    INNER JOIN restaurant_tables t ON r.table_id = t.table_id
                    INNER JOIN users u ON r.user_id = u.user_id
                    ORDER BY r.reservation_date, r.time_slot
                ";

                $result = mysqli_query($con, $query);

                if (!$result) {
                    die('Error fetching reservations: ' . mysqli_error($con));
                }

                $reservations = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $reservations[] = $row;
                }
                return $reservations;
            }catch( Exception $e ){
                throw new Exception("Error fetching Reservations: " . $e->getMessage(), 500);
            }
        }
    }

?>