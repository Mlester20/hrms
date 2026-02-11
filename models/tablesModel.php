<?php

    class tablesModel{
        public function getAllTables($con) {
            try{
                $query = "SELECT * FROM restaurant_tables ORDER BY table_number";
                $result = mysqli_query($con, $query);
                return mysqli_fetch_all($result, MYSQLI_ASSOC);
            }catch(Exception $e){
                echo "Error: " . $e->getMessage();
            }
        }
        public function addTable($con, $table_number, $capacity, $position_x, $position_y, $location) {
            try{
                $query = "INSERT INTO restaurant_tables (table_number, capacity, position_x, position_y, location) 
                    VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "iiids", $table_number, $capacity, $position_x, $position_y, $location);
                return mysqli_stmt_execute($stmt);
            }catch(Exception $e){
                echo "Error: " . $e->getMessage();
            }
        }
        public function updateTable($con, $table_id, $table_number, $capacity, $position_x, $position_y, $location) {
            try{
                $query = "UPDATE restaurant_tables 
                    SET table_number=?, capacity=?, position_x=?, position_y=?, location=? 
                    WHERE table_id=?";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "iiidsi", $table_number, $capacity, $position_x, $position_y, $location, $table_id);
                return mysqli_stmt_execute($stmt);
            }catch(Exception $e){
                echo "Error: " . $e->getMessage();
            }
        }
        public function deleteTable($con, $table_id) {
            try{
                $query = "DELETE FROM restaurant_tables WHERE table_id = ?";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "i", $table_id);
                return mysqli_stmt_execute($stmt);
            }catch(Exception $e){
                echo "Error: " . $e->getMessage();
            }
        }
    }

?>