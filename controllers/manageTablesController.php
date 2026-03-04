<?php
session_start();

require_once '../components/connection.php';
require_once '../models/tablesModel.php';
require_once '../includes/flash.php';

        $tables = new tablesModel($con);
        $allTables = $tables->getAllTables();

        // Add new table
        if (isset($_POST['add_table'])) {
            $table_number = $_POST['table_number'];
            $capacity = $_POST['capacity'];
            $position_x = $_POST['position_x'];
            $position_y = $_POST['position_y'];
            $location = $_POST['location'];

            $stmt = $tables->addTable($table_number, $capacity, $position_x, $position_y, $location);
            
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
            $table_id = $_POST['table_id'];
            $table_number = $_POST['table_number'];
            $capacity = $_POST['capacity'];
            $position_x = $_POST['position_x'];
            $position_y = $_POST['position_y'];
            $location = $_POST['location'];

            $stmt = $tables->updateTable($table_id, $table_number, $capacity, $position_x, $position_y, $location);
            
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
            $table_id = $_POST['table_id'];
            
            $stmt = $tables->deleteTable($table_id);
            
            if ($stmt) {
                setFlash("Table deleted successfully!", "success");
            } else {
                setFlash("Error deleting table: " . mysqli_error($con), "error");
            }
            header('Location: ../admin/manageTables.php');
            exit();
        }
?>