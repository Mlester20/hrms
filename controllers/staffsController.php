<?php
session_start();

require_once '../components/config.php';
require_once '../models/staffsModel.php';
require_once '../includes/flash.php';
// Initialize the model
$staffsModel = new staffsModel($con);

// Handle Add Staff
if (isset($_POST['addStaff'])) {
    try {
        $staffsModel->addStaff($_POST);
        setFlash("success", "Staff added successfully!");
    } catch (Exception $e) {
        setFlash("error", $e->getMessage());
    }
    
    header('Location: ../admin/staffs.php');
    exit();
}

// Handle Update Staff
if (isset($_POST['updateStaff'])) {
    try {
        $staffsModel->updateStaff($_POST);
        setFlash("success", "Staff updated successfully!");
    } catch (Exception $e) {
        setFlash("error", $e->getMessage());
    }
    
    header('Location: ../admin/staffs.php');
    exit();
}

// Handle Delete Staff
if (isset($_GET['deleteStaff'])) {
    try {
        $staff_id = $_GET['deleteStaff'];
        $staffsModel->deleteStaff($staff_id);
        setFlash("success", "Staff deleted successfully!");
    } catch (Exception $e) {
        setFlash("error", $e->getMessage());
    }
    
    header('Location: ../admin/staffs.php');
    exit();
}

$con->close();
?>