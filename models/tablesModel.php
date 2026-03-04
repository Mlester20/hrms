<?php

    class BaseModel{
        protected $con;

        public function __construct($con) {
            $this->con = $con;
        }
    }

    class tablesModel extends BaseModel {
        protected $table = 'restaurant_tables';

        public function getAllTables() {
            try{
                $query = "SELECT * FROM {$this->table} ORDER BY table_number";
                $result = mysqli_query($this->con, $query);
                return mysqli_fetch_all($result, MYSQLI_ASSOC);
            }catch(Exception $e){
                echo "Error: " . $e->getMessage();
            }
        }
        public function addTable($table_number, $capacity, $position_x, $position_y, $location) {
            try{
                $query = "INSERT INTO {$this->table} (table_number, capacity, position_x, position_y, location) 
                    VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($this->con, $query);
                mysqli_stmt_bind_param($stmt, "iiids", $table_number, $capacity, $position_x, $position_y, $location);
                return mysqli_stmt_execute($stmt);
            }catch(Exception $e){
                echo "Error: " . $e->getMessage();
            }
        }
        public function updateTable($table_id, $table_number, $capacity, $position_x, $position_y, $location) {
            try{
                $query = "UPDATE {$this->table} 
                    SET table_number=?, capacity=?, position_x=?, position_y=?, location=? 
                    WHERE table_id=?";
                $stmt = mysqli_prepare($this->con, $query);
                mysqli_stmt_bind_param($stmt, "iiidsi", $table_number, $capacity, $position_x, $position_y, $location, $table_id);
                return mysqli_stmt_execute($stmt);
            }catch(Exception $e){
                echo "Error: " . $e->getMessage();
            }
        }
        public function deleteTable($table_id) {
            try{
                $query = "DELETE FROM {$this->table} WHERE table_id = ?";
                $stmt = mysqli_prepare($this->con, $query);
                mysqli_stmt_bind_param($stmt, "i", $table_id);
                return mysqli_stmt_execute($stmt);
            }catch(Exception $e){
                echo "Error: " . $e->getMessage();
            }
        }
    }

?>