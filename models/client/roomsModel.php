<?php

class roomsModel {

    // Get all rooms with room type
    public function getAllRooms($con) {
        $sql = "SELECT rooms.*, room_type.title AS room_type_title, room_type.detail
                FROM rooms
                INNER JOIN room_type ON rooms.room_type_id = room_type.id
                ORDER BY rooms.id DESC";

        return mysqli_query($con, $sql);
    }

    // Get rooms by room type (filter)
    public function getRoomsByType($con, $typeId) {
        $sql = "SELECT rooms.*, room_type.title AS room_type_title, room_type.detail
                FROM rooms
                INNER JOIN room_type ON rooms.room_type_id = room_type.id
                WHERE rooms.room_type_id = ?
                ORDER BY rooms.id DESC";

        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $typeId);
        mysqli_stmt_execute($stmt);

        return mysqli_stmt_get_result($stmt);
    }

    // Get all room types (for filter buttons)
    public function getRoomTypes($con) {
        $sql = "SELECT * FROM room_type ORDER BY title ASC";
        return mysqli_query($con, $sql);
    }
}
