<?php

    class BaseModel{
        protected $con;

        public function __construct($con) {
            $this->con = $con;
        }
    }

    class roomsModel extends BaseModel {
        protected $rooms = 'rooms';
        protected $room_type = 'room_type';

        public function getAllRooms() {
            $sql = "SELECT rooms.*, room_type.title AS room_type_title, room_type.detail
                    FROM {$this->rooms}
                    INNER JOIN {$this->room_type} ON rooms.room_type_id = {$this->room_type}.id
                    ORDER BY rooms.id DESC";

            return mysqli_query($this->con, $sql);
        }

        // Get rooms by room type (filter)
        public function getRoomsByType($typeId) {
            $sql = "SELECT rooms.*, room_type.title AS room_type_title, room_type.detail
                    FROM {$this->rooms}
                    INNER JOIN {$this->room_type} ON rooms.room_type_id = {$this->room_type}.id
                    WHERE rooms.room_type_id = ?
                    ORDER BY rooms.id DESC";

            $stmt = mysqli_prepare($this->con, $sql);
            mysqli_stmt_bind_param($stmt, "i", $typeId);
            mysqli_stmt_execute($stmt);

            return mysqli_stmt_get_result($stmt);
        }

        // Get all room types (for filter buttons)
        public function getRoomTypes() {
            $sql = "SELECT * FROM room_type ORDER BY title ASC";
            return mysqli_query($this->con, $sql);
        }
    }
?>