<?php
require_once '../components/connection.php';
require_once '../models/client/checkAvailabilityModel.php';

header('Content-Type: application/json');

$availabilityModel = new CheckAvailabilityModel($con);
$today    = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

$f_check_in  = trim($_GET['check_in']  ?? '');
$f_check_out = trim($_GET['check_out'] ?? '');
$f_room_type = trim($_GET['room_type'] ?? '');

// Validation
if (empty($f_check_in) || empty($f_check_out)) {
    echo json_encode(['error' => 'Please select both a check-in and check-out date.']);
    exit;
}
if ($f_check_in < $today) {
    echo json_encode(['error' => 'Check-in date cannot be in the past.']);
    exit;
}
if ($f_check_out <= $f_check_in) {
    echo json_encode(['error' => 'Check-out date must be after the check-in date.']);
    exit;
}

$rooms = $availabilityModel->getAvailableRooms(
    $f_check_in,
    $f_check_out,
    !empty($f_room_type) ? $f_room_type : null
);

echo json_encode([
    'rooms'      => $rooms,
    'check_in'   => $f_check_in,
    'check_out'  => $f_check_out,
    'nights'     => (int)(( strtotime($f_check_out) - strtotime($f_check_in)) / 86400),
]);