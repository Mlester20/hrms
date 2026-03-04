<?php
session_start();

require_once '../components/connection.php';
require_once '../models/client/roomsModel.php';

$roomsModel = new roomsModel($con);

// Check filter
if (isset($_GET['type']) && !empty($_GET['type'])) {
    $rooms = $roomsModel->getRoomsByType($_GET['type']);
} else {
    $rooms = $roomsModel->getAllRooms();
}

$roomTypes = $roomsModel->getRoomTypes();