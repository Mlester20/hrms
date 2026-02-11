<?php
session_start();

require_once '../components/connection.php';
require_once '../models/tablesModel.php';
require_once '../includes/flash.php';

        $tables = new tablesModel();
        $allTables = $tables->getAllTables($con);

        // Add new table
        if (isset($_POST['add_table'])) {
            $table_number = mysqli_real_escape_string($con, $_POST['table_number']);
            $capacity = mysqli_real_escape_string($con, $_POST['capacity']);
            $position_x = mysqli_real_escape_string($con, $_POST['position_x']);
            $position_y = mysqli_real_escape_string($con, $_POST['position_y']);
            $location = mysqli_real_escape_string($con, $_POST['location']);

            $stmt = $tables->addTable($con, $table_number, $capacity, $position_x, $position_y, $location);
            
            if ($stmt) {
                setFlash("Table added successfully!", "success");
            } else {
                setFlash("Error adding table: " . mysqli_error($con), "error");
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

            $stmt = $tables->updateTable($con, $table_id, $table_number, $capacity, $position_x, $position_y, $location);
            
            if ($stmt) {
                setFlash("Table updated successfully!", "success");
            } else {
                setFlash("Error updating table: " . mysqli_error($con), "error");
            }
            header('Location: ../admin/manageTables.php');
            exit();
        }

        // Delete table
        if (isset($_POST['delete_table'])) {
            $table_id = mysqli_real_escape_string($con, $_POST['table_id']);
            
            $stmt = $tables->deleteTable($con, $table_id);
            
            if ($stmt) {
                setFlash("Table deleted successfully!", "success");
            } else {
                setFlash("Error deleting table: " . mysqli_error($con), "error");
            }
            header('Location: ../admin/manageTables.php');
            exit();
        }
?>