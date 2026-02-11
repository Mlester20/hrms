<?php

class roomTypeModel {

    public function getRoomTypes($con, $page, $limit) {
        try {
            $offset = ($page - 1) * $limit;
            $query = "SELECT * FROM room_type LIMIT ?, ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ii", $offset, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $roomTypes = [];
            while($row = $result->fetch_assoc()) {
                $roomTypes[] = $row;
            }
            $stmt->close();

            // Get total count for pagination
            $countQuery = "SELECT COUNT(*) as total FROM room_type";
            $countStmt = $con->prepare($countQuery);
            $countStmt->execute();
            $countResult = $countStmt->get_result();
            $totalCount = $countResult->fetch_assoc()['total'];
            $totalPages = ceil($totalCount / $limit);
            $countStmt->close();

            return [
                'roomTypes' => $roomTypes,
                'total_pages' => $totalPages,
                'current_page' => $page
            ];
        } catch(Exception $e) {
            throw new Exception("Error fetching room types: " . $e->getMessage());
        }
    }

    public function addRoomType($con, $title, $detail) {
        try {
            $query = "INSERT INTO room_type (title, detail) VALUES (?, ?)";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ss", $title, $detail);
            
            if($stmt->execute()) {
                $stmt->close();
                return true;
            }
            throw new Exception("Failed to execute query");
        } catch(Exception $e) {
            throw new Exception("Error adding room type: " . $e->getMessage());
        }
    }

    public function updateRoomType($con, $id, $title, $detail) {
        try {
            $query = "UPDATE room_type SET title = ?, detail = ? WHERE id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ssi", $title, $detail, $id);
            
            if($stmt->execute()) {
                $stmt->close();
                return true;
            }
            throw new Exception("Failed to execute query");
        } catch(Exception $e) {
            throw new Exception("Error updating room type: " . $e->getMessage());
        }
    }

    public function deleteRoomType($con, $id) {
        try {
            $query = "DELETE FROM room_type WHERE id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("i", $id);
            
            if($stmt->execute()) {
                $stmt->close();
                return true;
            }
            throw new Exception("Failed to execute query");
        } catch(Exception $e) {
            throw new Exception("Error deleting room type: " . $e->getMessage());
        }
    }

}

?>