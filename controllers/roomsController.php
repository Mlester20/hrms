<?php
session_start();
include '../components/config.php';

// Handle Add Room
if (isset($_POST['addRoom'])) {
    $title = $_POST['title'];
    $room_type_id = $_POST['room_type_id'];
    $price = $_POST['price'];

    // Handle multiple image uploads
    $imageNames = [];
    
    if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
        // Multiple images uploaded
        $totalFiles = count($_FILES['images']['name']);
        
        for ($i = 0; $i < $totalFiles; $i++) {
            if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                $imageTmpPath = $_FILES['images']['tmp_name'][$i];
                $imageName = $_FILES['images']['name'][$i];
                $imageSize = $_FILES['images']['size'][$i];
                $imageType = $_FILES['images']['type'][$i];
                $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);

                // Define allowed file types
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array(strtolower($imageExtension), $allowedExtensions)) {
                    $uploadFolder = '../uploads/';
                    $newImageName = uniqid('room_', true) . '.' . $imageExtension;
                    $destinationPath = $uploadFolder . $newImageName;

                    if (move_uploaded_file($imageTmpPath, $destinationPath)) {
                        $imageNames[] = $newImageName; // Save the file name to array
                    } else {
                        $_SESSION['error'] = "Failed to upload image #" . ($i + 1);
                        header('Location: ../admin/manageRooms.php');
                        exit();
                    }
                } else {
                    $_SESSION['error'] = "Invalid image file type for image #" . ($i + 1);
                    header('Location: ../admin/manageRooms.php');
                    exit();
                }
            } else if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                $_SESSION['error'] = "Error uploading image #" . ($i + 1);
                header('Location: ../admin/manageRooms.php');
                exit();
            }
        }
    }

    if (empty($imageNames)) {
        $_SESSION['error'] = "No images uploaded.";
        header('Location: ../admin/manageRooms.php');
        exit();
    }

    // Convert array of image names to JSON string for storage
    $imagesJson = json_encode($imageNames);

    $query = "INSERT INTO rooms (title, room_type_id, images, price) VALUES (?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("sisd", $title, $room_type_id, $imagesJson, $price);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Room added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add room: " . $stmt->error;
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
    $price = $_POST['price'];
    
    // Get existing image data
    $existingImages = [];
    $query = "SELECT images FROM rooms WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($currentImages);
    $stmt->fetch();
    $stmt->close();
    
    if ($currentImages) {
        $existingImages = json_decode($currentImages, true) ?: [];
    }
    
    // Handle multiple image uploads for update
    $newImages = [];
    $updateImages = false;
    
    if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
        // Multiple images uploaded
        $totalFiles = count($_FILES['images']['name']);
        $hasNewImages = false;
        
        for ($i = 0; $i < $totalFiles; $i++) {
            if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                $hasNewImages = true;
                $imageTmpPath = $_FILES['images']['tmp_name'][$i];
                $imageName = $_FILES['images']['name'][$i];
                $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);

                // Define allowed file types
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array(strtolower($imageExtension), $allowedExtensions)) {
                    $uploadFolder = '../uploads/';
                    $newImageName = uniqid('room_', true) . '.' . $imageExtension;
                    $destinationPath = $uploadFolder . $newImageName;

                    if (move_uploaded_file($imageTmpPath, $destinationPath)) {
                        $newImages[] = $newImageName; // Save the file name to array
                    } else {
                        $_SESSION['error'] = "Failed to upload image #" . ($i + 1);
                        header('Location: ../admin/manageRooms.php');
                        exit();
                    }
                } else {
                    $_SESSION['error'] = "Invalid image file type for image #" . ($i + 1);
                    header('Location: ../admin/manageRooms.php');
                    exit();
                }
            }
        }
        
        if ($hasNewImages) {
            $updateImages = true;
            
            // Delete old image files if we're replacing them
            foreach ($existingImages as $oldImage) {
                $imagePath = '../uploads/' . $oldImage;
                if (file_exists($imagePath)) {
                    unlink($imagePath); // Delete the old image file
                }
            }
        }
    }
    
    if ($updateImages) {
        $imagesJson = json_encode($newImages);
        $query = "UPDATE rooms SET title = ?, room_type_id = ?, images = ?, price = ? WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sisdi", $title, $room_type_id, $imagesJson, $price, $id);
    } else {
        $query = "UPDATE rooms SET title = ?, room_type_id = ?, price = ? WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sidi", $title, $room_type_id, $price, $id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Room updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update room: " . $stmt->error;
    }

    $stmt->close();
    header('Location: ../admin/manageRooms.php');
    exit();
}

// Handle Delete Room
if (isset($_GET['deleteRoom'])) {
    $id = $_GET['deleteRoom'];

    // Fetch the image file names to delete them from the server
    $query = "SELECT images FROM rooms WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($imagesJson);
    $stmt->fetch();
    $stmt->close();

    if ($imagesJson) {
        $imageFileNames = json_decode($imagesJson, true) ?: [];
        
        foreach ($imageFileNames as $imageFileName) {
            $imagePath = '../uploads/' . $imageFileName;
            if (file_exists($imagePath)) {
                unlink($imagePath); // Delete the image file
            }
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