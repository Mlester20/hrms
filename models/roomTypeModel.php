<?php

    class BaseModel{
        protected $con;

        public function __construct($con){
            $this->con = $con;
        }
    }

    class roomTypeModel extends BaseModel {

        protected $table = 'room_type';

        public function getRoomTypes($page, $limit) {
            try {
                $offset = ($page - 1) * $limit;
                $query = "SELECT * FROM {$this->table} LIMIT ?, ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("ii", $offset, $limit);
                $stmt->execute();
                $result = $stmt->get_result();
                
                $roomTypes = [];
                while($row = $result->fetch_assoc()) {
                    $roomTypes[] = $row;
                }
                $stmt->close();

                // Get total count for pagination
                $countQuery = "SELECT COUNT(*) as total FROM {$this->table}";
                $countStmt = $this->con->prepare($countQuery);
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

        public function addRoomType($title, $detail) {
            try {
                $query = "INSERT INTO {$this->table} (title, detail) VALUES (?, ?)";
                $stmt = $this->con->prepare($query);
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

        public function updateRoomType($id, $title, $detail) {
            try {
                $query = "UPDATE {$this->table} SET title = ?, detail = ? WHERE id = ?";
                $stmt = $this->con->prepare($query);
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

        public function deleteRoomType($id) {
            try {
                $query = "DELETE FROM {$this->table} WHERE id = ?";
                $stmt = $this->con->prepare($query);
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