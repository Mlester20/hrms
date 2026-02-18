<?php
session_start();

require_once '../includes/connection.php';
require_once '../models/client/roomsModel.php';

$roomsModel = new roomsModel();

// Check filter
if (isset($_GET['type']) && !empty($_GET['type'])) {
    $rooms = $roomsModel->getRoomsByType($con, $_GET['type']);
} else {
    $rooms = $roomsModel->getAllRooms($con);
}

$roomTypes = $roomsModel->getRoomTypes($con);