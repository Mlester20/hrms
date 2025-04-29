<?php
session_start();
include '../components/config.php';

// Handle Add Shift
if (isset($_POST['addShift'])) {
    $staff_id = $_POST['staff_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $date_start = $_POST['date_start'];
    $date_end = $_POST['date_end'];

    $query = "INSERT INTO shifts (staff_id, start_time, end_time, date_start, date_end) VALUES (?, ?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("issss", $staff_id, $start_time, $end_time, $date_start, $date_end);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Shift added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add shift.";
    }

    $stmt->close();
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

    $query = "UPDATE shifts SET staff_id = ?, start_time = ?, end_time = ?, date_start = ?, date_end = ? WHERE shift_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("issssi", $staff_id, $start_time, $end_time, $date_start, $date_end, $shift_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Shift updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update shift.";
    }

    $stmt->close();
    header('Location: ../admin/shifts.php');
    exit();
}

// Handle Delete Shift
if (isset($_GET['deleteShift'])) {
    $shift_id = $_GET['deleteShift'];

    $query = "DELETE FROM shifts WHERE shift_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $shift_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Shift deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete shift.";
    }

    $stmt->close();
    header('Location: ../admin/shifts.php');
    exit();
}

$con->close();
?>