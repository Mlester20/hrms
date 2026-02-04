<?php
session_start();
require_once '../includes/flash.php';
require_once '../components/connection.php';
require_once '../models/registerModel.php';

$registerModel = new registerUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $role = 'user'; // Default role for registered users

    try {
        // Call the register method
        if( $registerModel->register($con, $name, $email, $password, $confirmPassword, $phone, $address, $role)){
            setFlash('success', 'Registration successful! You can now log in.');
            header('Location: ../index.php');
            exit();
        } else {
            setFlash('error', 'Registration failed. Please try again.');
            header('Location: ../register.php');
            exit();
        }
    } catch (Exception $e) {
        setFlash('error', $e->getMessage());
        header('Location: ../register.php');
        exit();
    }
}

?>