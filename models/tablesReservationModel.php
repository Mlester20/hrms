<?php

    class BaseModel{
        protected $con;

        public function __construct($con){
            $this->con = $con;
        }
    }

    class TablesModel extends BaseModel {
        protected $table_reservations = 'table_reservations';
        protected $restaurant_tables = 'restaurant_tables';
        protected $users = 'users';

        public function getReservations() {
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
                    FROM {$this->table_reservations} r
                    INNER JOIN {$this->restaurant_tables} t ON r.table_id = t.table_id
                    INNER JOIN {$this->users} u ON r.user_id = u.user_id
                    ORDER BY r.reservation_date, r.time_slot
                ";

                $result = mysqli_query($this->con, $query);

                if (!$result) {
                    die('Error fetching reservations: ' . mysqli_error($this->con));
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