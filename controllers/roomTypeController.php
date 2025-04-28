<?php
session_start();
include '../components/config.php';

// Handle Add Room Type
if (isset($_POST['addRoomType'])) {
    $title = $_POST['title'];
    $detail = $_POST['detail'];

    $query = "INSERT INTO room_type (title, detail) VALUES (?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ss", $title, $detail);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Room type added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add room type.";
    }

    $stmt->close();
    header('Location: ../admin/roomType.php');
    exit();
}

// Handle Update Room Type
if (isset($_POST['updateRoomType'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $detail = $_POST['detail'];

    $query = "UPDATE room_type SET title = ?, detail = ? WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ssi", $title, $detail, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Room type updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update room type.";
    }

    $stmt->close();
    header('Location: ../admin/roomType.php');
    exit();
}

// Handle Delete Room Type
if (isset($_GET['deleteRoomType'])) {
    $id = $_GET['deleteRoomType'];

    $query = "DELETE FROM room_type WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Room type deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete room type.";
    }

    $stmt->close();
    header('Location: ../admin/roomType.php');
    exit();
}

$con->close();
?>