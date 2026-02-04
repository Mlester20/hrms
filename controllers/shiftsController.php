<?php
session_start();

require_once '../components/connection.php';
require_once '../models/shiftsModel.php';
require_once '../includes/flash.php';

$shiftsModel = new shiftsModel();
$shiftsData = $shiftsModel->getAllShifts($con);
$shifts = $shiftsData['shifts'];
$staffResult = $shiftsData['staffs'];

// Handle Add Shift
if (isset($_POST['addShift'])) {
    $staff_id = $_POST['staff_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $date_start = $_POST['date_start'];
    $date_end = $_POST['date_end'];
    $status = 'pending'; // Default status

    if($shiftsModel->addShifts($con, $staff_id, $start_time, $end_time, $date_start, $date_end, $status)) {
        setFlash("success", "Shift added successfully!");
    } else {
        setFlash("error", "Failed to add shift.");
    }

    header('Location: ../admin/shifts.php');
    exit();
}

// Handle Update Shift
if (isset($_POST['updateShift'])) {
    $shift_id = $_POST['shift_id'];
    $staff_id = $_POST['staff_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $date_start = $_POST['date_start'];
    $date_end = $_POST['date_end'];
    $status = $_POST['status'];

    if($shiftsModel->updateShift($con, $shift_id, $staff_id, $start_time, $end_time, $date_start, $date_end, $status)) {
        setFlash("success", "Shift updated successfully!");
    } else {
        setFlash("error", "Failed to update shift.");
    }

    header('Location: ../admin/shifts.php');
    exit();
}

// Handle Delete Shift
if (isset($_GET['deleteShift'])) {
    $shift_id = $_GET['deleteShift'];

    if($shiftsModel->deleteShift($con, $shift_id)) {
        setFlash("success", "Shift deleted successfully!");
    } else {
        setFlash("error", "Failed to delete shift.");
    }

    header('Location: ../admin/shifts.php');
    exit();
}

// Handle Mark as Done
if (isset($_GET['markDone'])) {
    $shift_id = $_GET['markDone'];

    if($shiftsModel->markShiftAsDone($con, $shift_id)) {
        setFlash("success", "Shift marked as done!");
    } else {
        setFlash("error", "Failed to mark shift as done.");
    }

    header('Location: ../admin/shifts.php');
    exit();
}

$con->close();
?>