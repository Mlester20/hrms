<?php

require_once '../components/connection.php'; 
require_once '../models/client/checkAvailabilityModel.php';

$availabilityModel = new CheckAvailabilityModel();

$availableRooms  = [];
$roomTypes       = $availabilityModel->getRoomTypes($con);
$searchPerformed = false;
$availError      = '';

// ── Shared date defaults ──────────────────────────────────────────────────────
$today    = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

$f_check_in    = $today;
$f_check_out   = $tomorrow;
$f_room_type   = '';

// ── Handle form submission (GET so the URL is shareable / back-button safe) ───
if (isset($_GET['check_availability'])) {

    $f_check_in  = trim($_GET['check_in']  ?? '');
    $f_check_out = trim($_GET['check_out'] ?? '');
    $f_room_type = trim($_GET['room_type'] ?? '');

    // ── Validation ────────────────────────────────────────────────────────────
    if (empty($f_check_in) || empty($f_check_out)) {
        $availError = 'Please select both a check-in and check-out date.';

    } elseif ($f_check_in < $today) {
        $availError = 'Check-in date cannot be in the past.';

    } elseif ($f_check_out <= $f_check_in) {
        $availError = 'Check-out date must be after the check-in date.';

    } else {
        $searchPerformed = true;
        $availableRooms  = $availabilityModel->getAvailableRooms(
            $con,
            $f_check_in,
            $f_check_out,
            !empty($f_room_type) ? $f_room_type : null
        );
    }
}