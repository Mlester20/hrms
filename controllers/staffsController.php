<?php
session_start();
include '../components/config.php';

// Handle Add Staff
if (isset($_POST['addStaff'])) {
    $name = $_POST['name'];
    $position = $_POST['position'];
    $address = $_POST['address'];
    $shift_type = $_POST['shift_type'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Encrypt password with MD5

    // Handle profile image upload
    $profile = null;
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['profile']['tmp_name'];
        $imageName = $_FILES['profile']['name'];
        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);

        // Define allowed file types
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($imageExtension), $allowedExtensions)) {
            $uploadFolder = '../uploads/';
            $newImageName = uniqid('staff_', true) . '.' . $imageExtension;
            $destinationPath = $uploadFolder . $newImageName;

            if (move_uploaded_file($imageTmpPath, $destinationPath)) {
                $profile = $newImageName; // Save the file name to the database
            } else {
                $_SESSION['error'] = "Failed to upload profile image.";
                header('Location: ../admin/staffs.php');
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid profile image file type.";
            header('Location: ../admin/staffs.php');
            exit();
        }
    }

    $query = "INSERT INTO staffs (name, position, address, shift_type, phone_number, email, password, profile) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ssssssss", $name, $position, $address, $shift_type, $phone_number, $email, $password, $profile);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Staff added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add staff.";
    }

    $stmt->close();
    header('Location: ../admin/staffs.php');
    exit();
}

// Handle Update Staff
if (isset($_POST['updateStaff'])) {
    $staff_id = $_POST['staff_id'];
    $name = $_POST['name'];
    $position = $_POST['position'];
    $address = $_POST['address'];
    $shift_type = $_POST['shift_type'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Handle profile image upload
    $profile = null;
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['profile']['tmp_name'];
        $imageName = $_FILES['profile']['name'];
        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);

        // Define allowed file types
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($imageExtension), $allowedExtensions)) {
            $uploadFolder = '../uploads/';
            $newImageName = uniqid('staff_', true) . '.' . $imageExtension;
            $destinationPath = $uploadFolder . $newImageName;

            if (move_uploaded_file($imageTmpPath, $destinationPath)) {
                $profile = $newImageName; // Save the file name to the database

                // Delete the old profile image
                $oldImageQuery = "SELECT profile FROM staffs WHERE staff_id = ?";
                $stmt = $con->prepare($oldImageQuery);
                $stmt->bind_param("i", $staff_id);
                $stmt->execute();
                $stmt->bind_result($oldImage);
                $stmt->fetch();
                $stmt->close();

                if ($oldImage) {
                    $oldImagePath = '../uploads/' . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath); // Delete the old image file
                    }
                }
            } else {
                $_SESSION['error'] = "Failed to upload profile image.";
                header('Location: ../admin/staffs.php');
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid profile image file type.";
            header('Location: ../admin/staffs.php');
            exit();
        }
    }

    if (!empty($password)) {
        $password = md5($password); // Encrypt password with MD5
        $query = "UPDATE staffs SET name = ?, position = ?, address = ?, shift_type = ?, phone_number = ?, email = ?, password = ?, profile = ? WHERE staff_id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ssssssssi", $name, $position, $address, $shift_type, $phone_number, $email, $password, $profile, $staff_id);
    } else {
        $query = "UPDATE staffs SET name = ?, position = ?, address = ?, shift_type = ?, phone_number = ?, email = ?, profile = ? WHERE staff_id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sssssssi", $name, $position, $address, $shift_type, $phone_number, $email, $profile, $staff_id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Staff updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update staff.";
    }

    $stmt->close();
    header('Location: ../admin/staffs.php');
    exit();
}

// Handle Delete Staff
if (isset($_GET['deleteStaff'])) {
    $staff_id = $_GET['deleteStaff'];

    // Fetch the profile image file name to delete it from the server
    $query = "SELECT profile FROM staffs WHERE staff_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $staff_id);
    $stmt->execute();
    $stmt->bind_result($profile);
    $stmt->fetch();
    $stmt->close();

    if ($profile) {
        $profilePath = '../uploads/' . $profile;
        if (file_exists($profilePath)) {
            unlink($profilePath); // Delete the profile image file
        }
    }

    $query = "DELETE FROM staffs WHERE staff_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $staff_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Staff deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete staff.";
    }

    $stmt->close();
    header('Location: ../admin/staffs.php');
    exit();
}

$con->close();
?>