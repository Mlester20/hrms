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
    $status = 'pending'; // Default status

    $query = "INSERT INTO shifts (staff_id, start_time, end_time, date_start, date_end, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("isssss", $staff_id, $start_time, $end_time, $date_start, $date_end, $status);

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
    $status = $_POST['status'];

    $query = "UPDATE shifts SET staff_id = ?, start_time = ?, end_time = ?, date_start = ?, date_end = ?, status = ? WHERE shift_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("isssssi", $staff_id, $start_time, $end_time, $date_start, $date_end, $status, $shift_id);

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

// Handle Mark as Done
if (isset($_GET['markDone'])) {
    $shift_id = $_GET['markDone'];

    $query = "UPDATE shifts SET status = 'done' WHERE shift_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $shift_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Shift marked as done!";
    } else {
        $_SESSION['error'] = "Failed to mark shift as done.";
    }

    $stmt->close();
    header('Location: ../admin/shifts.php');
    exit();
}

$con->close();
?>