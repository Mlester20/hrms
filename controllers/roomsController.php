<?php
session_start();
include '../components/config.php';

// Handle Add Room
if (isset($_POST['addRoom'])) {
    $title = $_POST['title'];
    $room_type_id = $_POST['room_type_id'];

    // Handle image upload
    if (isset($_FILES['images']) && $_FILES['images']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['images']['tmp_name'];
        $imageName = $_FILES['images']['name'];
        $imageSize = $_FILES['images']['size'];
        $imageType = $_FILES['images']['type'];
        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);

        // Define allowed file types
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array(strtolower($imageExtension), $allowedExtensions)) {
            $uploadFolder = '../uploads/';
            $newImageName = uniqid('room_', true) . '.' . $imageExtension;
            $destinationPath = $uploadFolder . $newImageName;

            if (move_uploaded_file($imageTmpPath, $destinationPath)) {
                $images = $newImageName; // Save the file name to the database
            } else {
                $_SESSION['error'] = "Failed to upload image.";
                header('Location: ../admin/manageRooms.php');
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid image file type.";
            header('Location: ../admin/manageRooms.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "No image uploaded.";
        header('Location: ../admin/manageRooms.php');
        exit();
    }

    $query = "INSERT INTO rooms (title, room_type_id, images) VALUES (?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("sis", $title, $room_type_id, $images);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Room added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add room.";
    }

    $stmt->close();
    header('Location: ../admin/manageRooms.php');
    exit();
}

// Handle Update Room
if (isset($_POST['updateRoom'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $room_type_id = $_POST['room_type_id'];
    $images = null;

    // Handle image upload
    if (isset($_FILES['images']) && $_FILES['images']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['images']['tmp_name'];
        $imageName = $_FILES['images']['name'];
        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);

        // Define allowed file types
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array(strtolower($imageExtension), $allowedExtensions)) {
            $uploadFolder = '../uploads/';
            $newImageName = uniqid('room_', true) . '.' . $imageExtension;
            $destinationPath = $uploadFolder . $newImageName;

            if (move_uploaded_file($imageTmpPath, $destinationPath)) {
                $images = $newImageName; // Save the file name to the database
            } else {
                $_SESSION['error'] = "Failed to upload image.";
                header('Location: ../admin/manageRooms.php');
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid image file type.";
            header('Location: ../admin/manageRooms.php');
            exit();
        }
    }

    if ($images) {
        $query = "UPDATE rooms SET title = ?, room_type_id = ?, images = ? WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sisi", $title, $room_type_id, $images, $id);
    } else {
        $query = "UPDATE rooms SET title = ?, room_type_id = ? WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sii", $title, $room_type_id, $id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Room updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update room.";
    }

    $stmt->close();
    header('Location: ../admin/manageRooms.php');
    exit();
}

// Handle Delete Room
if (isset($_GET['deleteRoom'])) {
    $id = $_GET['deleteRoom'];

    // Fetch the image file name to delete it from the server
    $query = "SELECT images FROM rooms WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($imageFileName);
    $stmt->fetch();
    $stmt->close();

    if ($imageFileName) {
        $imagePath = '../uploads/' . $imageFileName;
        if (file_exists($imagePath)) {
            unlink($imagePath); // Delete the image file
        }
    }

    $query = "DELETE FROM rooms WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Room deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete room.";
    }

    $stmt->close();
    header('Location: ../admin/manageRooms.php');
    exit();
}

$con->close();
?>