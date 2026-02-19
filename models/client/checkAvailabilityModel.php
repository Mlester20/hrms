<?php

class CheckAvailabilityModel {

    /**
     * Find all rooms NOT booked during the given date range.
     * Overlap: an existing booking conflicts if
     *   check_in_date < requested_checkout AND check_out_date > requested_checkin
     */
    public function getAvailableRooms($con, $check_in, $check_out, $room_type_id = null) {

        if (!empty($room_type_id)) {
            $sql = "
                SELECT r.*
                FROM rooms r
                WHERE r.id NOT IN (
                    SELECT b.room_id
                    FROM bookings b
                    WHERE b.status NOT IN ('cancelled', 'rejected')
                      AND b.check_in_date  < ?
                      AND b.check_out_date > ?
                )
                AND r.room_type_id = ?
                ORDER BY r.price ASC
            ";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, 'sss', $check_out, $check_in, $room_type_id);
        } else {
            $sql = "
                SELECT r.*
                FROM rooms r
                WHERE r.id NOT IN (
                    SELECT b.room_id
                    FROM bookings b
                    WHERE b.status NOT IN ('cancelled', 'rejected')
                      AND b.check_in_date  < ?
                      AND b.check_out_date > ?
                )
                ORDER BY r.price ASC
            ";
            $stmt = mysqli_prepare($con, $sql);
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

    /**
     * Fetch all distinct room types for the filter dropdown.
     */
    public function getRoomTypes($con) {
        $sql = "SELECT DISTINCT room_type_id FROM rooms ORDER BY room_type_id ASC";
        $result = mysqli_query($con, $sql);

        $types = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $types[] = $row;
        }
        return $types;
    }

    /**
     * Quick single-room availability check. Returns true if the room is free.
     */
    public function isRoomAvailable($con, $room_id, $check_in, $check_out) {
        $sql = "
            SELECT COUNT(*) AS cnt
            FROM bookings
            WHERE room_id = ?
              AND status NOT IN ('cancelled', 'rejected')
              AND check_in_date  < ?
              AND check_out_date > ?
        ";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'iss', $room_id, $check_out, $check_in);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return (int)$row['cnt'] === 0;
    }
}