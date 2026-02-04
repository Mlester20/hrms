<?php

    class roomTypeModel{

        public function getRoomTypes($con, $page, $limit){
            $offset = ($page - 1) * $limit;
            $query = "SELECT * FROM room_type LIMIT ?, ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ii", $offset, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            $roomTypes = [];
            while($row = $result->fetch_assoc()){
                $roomTypes[] = $row;
            }

            // Get total count for pagination
            $countQuery = "SELECT COUNT(*) as total FROM room_type";
            $countResult = $con->query($countQuery);
            $totalCount = $countResult->fetch_assoc()['total'];
            $totalPages = ceil($totalCount / $limit);

            return [
                'roomTypes' => $roomTypes,
                'total_pages' => $totalPages,
                'current_page' => $page
            ];
        }

        public function addRoomType($con, $title, $detail){
            $query = "INSERT INTO room_type (title, detail) VALUES (?, ?)";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ss", $title, $detail);
            return $stmt->execute();
        }

        public function updateRoomType($con, $id, $title, $detail){
            $query = "UPDATE room_type SET title = ?, detail = ? WHERE id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ssi", $title, $detail, $id);
            return $stmt->execute();
        }

        public function deleteRoomType($con, $id){
            $query = "DELETE FROM room_type WHERE id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        }

    }

?>