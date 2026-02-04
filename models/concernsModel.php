<?php

    class concernsModel{
        public function getAllConcerns($con){
            try {
                $query = "SELECT c.id, c.subject, c.message, c.created_at, u.name, u.email, u.address 
                          FROM concerns c
                          JOIN users u ON c.user_id = u.user_id
                          ORDER BY c.created_at DESC";
                $stmt = $con->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();
    
                $concerns = [];
                while ($row = $result->fetch_assoc()) {
                    $concerns[] = $row;
                }
    
                $stmt->close();
                return $concerns;
            } catch (Exception $e) {
                throw new Exception("Error fetching concerns: " . $e->getMessage());
            }
        }
    }

?>