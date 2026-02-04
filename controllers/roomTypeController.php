<?php
session_start();
require_once '../components/connection.php';
require_once '../models/roomTypeModel.php';
require_once '../includes/flash.php';

$roomTypeModel = new roomTypeModel();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$data = $roomTypeModel->getRoomTypes($con, $page, $limit);
$roomTypes = $data['roomTypes'];
$total_pages = $data['total_pages'];

// ===== Handle Add Room Type =====
if(isset($_POST['addRoomType'])) {
    $title = $_POST['title'];
    $detail = $_POST['detail'];
    
    if ($roomTypeModel->addRoomType($con, $title, $detail)) {
        setFlash('success', 'Room type added successfully!');
    } else {
        setFlash('error', 'Failed to add room type. Please try again.');
    }

    header('Location: ../admin/roomType.php');
    exit();
}

// ===== Handle Update Room Type =====
if(isset($_POST['updateRoomType'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $detail = $_POST['detail'];
    
    if ($roomTypeModel->updateRoomType($con, $id, $title, $detail)) {
        setFlash('success', 'Room type updated successfully!');
    } else {
        setFlash('error', 'Failed to update room type. Please try again.');
    }

    header('Location: ../admin/roomType.php');
    exit();
}

// ===== Handle Delete Room Type =====
if(isset($_POST['deleteRoomType'])) {
    $id = $_POST['id'];
    
    if ($roomTypeModel->deleteRoomType($con, $id) === true) {
        setFlash('success', 'Room type deleted successfully!');
    } else {
        setFlash('error', 'Failed to delete room type. Please try again.');
    }

    header('Location: ../admin/roomType.php');
    exit();
}

?>