<?php
session_start();
include '../components/connection.php';

    try{
        // Fetch all tables
        function getAllTables($con) {
            $db = new Database();
            $con = $db->getConnection();
            $query = "SELECT * FROM restaurant_tables ORDER BY table_number";
            $result = mysqli_query($con, $query);
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        // Add new table
        if (isset($_POST['add_table'])) {
            $table_number = mysqli_real_escape_string($con, $_POST['table_number']);
            $capacity = mysqli_real_escape_string($con, $_POST['capacity']);
            $position_x = mysqli_real_escape_string($con, $_POST['position_x']);
            $position_y = mysqli_real_escape_string($con, $_POST['position_y']);
            $location = mysqli_real_escape_string($con, $_POST['location']);

            $query = "INSERT INTO restaurant_tables (table_number, capacity, position_x, position_y, location) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "iiids", $table_number, $capacity, $position_x, $position_y, $location);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = "Table added successfully!";
            } else {
                $_SESSION['error'] = "Error adding table: " . mysqli_error($con);
            }
            header('Location: ../admin/manageTables.php');
            exit();
        }

        // Update table
        if (isset($_POST['update_table'])) {
            $table_id = mysqli_real_escape_string($con, $_POST['table_id']);
            $table_number = mysqli_real_escape_string($con, $_POST['table_number']);
            $capacity = mysqli_real_escape_string($con, $_POST['capacity']);
            $position_x = mysqli_real_escape_string($con, $_POST['position_x']);
            $position_y = mysqli_real_escape_string($con, $_POST['position_y']);
            $location = mysqli_real_escape_string($con, $_POST['location']);

            $query = "UPDATE restaurant_tables 
                    SET table_number=?, capacity=?, position_x=?, position_y=?, location=? 
                    WHERE table_id=?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "iiidsi", $table_number, $capacity, $position_x, $position_y, $location, $table_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = "Table updated successfully!";
            } else {
                $_SESSION['error'] = "Error updating table: " . mysqli_error($con);
            }
            header('Location: ../admin/manageTables.php');
            exit();
        }

        // Delete table
        if (isset($_POST['delete_table'])) {
            $table_id = mysqli_real_escape_string($con, $_POST['table_id']);
            
            $query = "DELETE FROM restaurant_tables WHERE table_id = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "i", $table_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = "Table deleted successfully!";
            } else {
                $_SESSION['error'] = "Error deleting table: " . mysqli_error($con);
            }
            header('Location: ../admin/manageTables.php');
            exit();
        }
    }catch( Exception $e ){
        throw new Exception("Error fetching tables". $e->getMessage(), 500);
    }finally{
        $db->closeConnection();
    }

?>