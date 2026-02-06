<?php
session_start();
require_once '../includes/flash.php';
require_once '../components/connection.php';
require_once '../models/profileModel.php';

$profileModel = new profileModel();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = trim($_POST['name']);
        $address = trim($_POST['address']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $current_password = $_POST['current_password'];
        $new_password = trim($_POST['new_password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');

        // Verify current password
        $profileModel->verifyPassword($con, $user_id, $current_password);

        // Update basic profile information
        $profileModel->updateProfile($con, $user_id, $name, $address, $email, $phone);

        // Handle password change if new password is provided
        if (!empty($new_password) || !empty($confirm_password)) {
            $profileModel->updatePassword($con, $user_id, $new_password, $confirm_password);
            setFlash('success', 'Profile and password updated successfully!');
        } else {
            setFlash('success', 'Profile updated successfully!');
        }

    } catch (Exception $e) {
        setFlash('error', $e->getMessage());
    }

    header('Location: ../public/profile.php');
    exit();
} else {
    // If not POST request, redirect back
    header('Location: ../public/profile.php');
    exit();
}
?>