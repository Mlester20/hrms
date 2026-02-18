<?php
session_start();

require_once '../components/connection.php';
require_once '../models/roomModel.php';
require_once '../includes/flash.php';

// Initialize the model
$roomModel = new roomModel($con);


$records_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

$total_rows = $roomModel->getTotalRoomsCount();
$total_pages = ceil($total_rows / $records_per_page);


$result = $roomModel->getRoomsWithPagination($offset, $records_per_page);

$roomTypesResult = $roomModel->getAllRoomTypes();

// Handle Add Room
if (isset($_POST['addRoom'])) {
    try {
        $roomModel->addRoom($_POST, $_FILES);
        setFlash("success", "Room added successfully!");
    } catch (Exception $e) {
        setFlash("error", $e->getMessage());
    }
    
    header('Location: ../admin/manageRooms.php');
    exit();
}

// Handle Update Room
if (isset($_POST['updateRoom'])) {
    try {
        $roomModel->updateRoom($_POST, $_FILES);
        setFlash("success", "Room updated successfully!");
    } catch (Exception $e) {
        setFlash("error", $e->getMessage());
    }
    
    header('Location: ../admin/manageRooms.php');
    exit();
}

// Handle Delete Room
if (isset($_GET['deleteRoom'])) {
    try {
        $id = $_GET['deleteRoom'];
        $roomModel->deleteRoom($id);
        setFlash("success", "Room deleted successfully!");
    } catch (Exception $e) {
        setFlash("error", $e->getMessage());
    }
    
    header('Location: ../admin/manageRooms.php');
    exit();
}

?>