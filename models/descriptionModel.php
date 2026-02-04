<?php

    class descriptionModel{
        /* =========================
            GET DESCRIPTION
        ========================= */
        public function getAllDescriptions($con){
            try {
                $query = "SELECT description_id, description_name FROM description ORDER BY description_id DESC";
                $stmt = $con->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();
    
                $descriptions = [];
                while ($row = $result->fetch_assoc()) {
                    $descriptions[] = $row;
                }
    
                $stmt->close();
                return $descriptions;
            } catch (Exception $e) {
                throw new Exception("Error fetching descriptions: " . $e->getMessage());
            }
        }

        /* =========================
        ADD DESCRIPTION
        ========================= */
        public function addDescription($con, $description_name){
            try {
                $stmt = $con->prepare(
                    "INSERT INTO description (description_name) VALUES (?)"
                );

                if (!$stmt) {
                    throw new Exception('Prepare failed: ' . $con->error);
                }

                $stmt->bind_param('s', $description_name);

                if ($stmt->execute()) {
                    return true;
                } else {
                    throw new Exception('Execute failed: ' . $stmt->error);
                }

            } catch (Exception $e) {
                throw new Exception('Error: ' . $e->getMessage());
            }
        }

        /* =========================
            UPDATE DESCRIPTION
        ========================= */
        public function updateDescription($con, $description_id, $description){
            try {
                $stmt = $con->prepare(
                    "UPDATE description SET description_name = ? WHERE description_id = ?"
                );

                if (!$stmt) {
                    throw new Exception('Prepare failed: ' . $con->error);
                }

                $stmt->bind_param('si', $description, $description_id);

                if ($stmt->execute()) {
                    return true;
                } else {
                    throw new Exception('Execute failed: ' . $stmt->error);
                }

            } catch (Exception $e) {
                throw new Exception('Error: ' . $e->getMessage());
            }
        }

    }

?>