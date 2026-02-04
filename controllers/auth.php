<?php   
session_start();

require_once '../includes/flash.php';
require_once '../components/connection.php';
require_once '../models/authModel.php';

$auth = new authModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if ($auth->login($con, $email, $password)) {
            if ($_SESSION['role'] === 'admin') {
                header('Location: ../admin/dashboard.php');
            } else {
                header('Location: ../public/home.php');
            }
            exit();
        } else {
            setFlash('error', 'Invalid email or password. Please try again.');
            header('Location: ../index.php');
            exit(); 
        }
    } catch (Exception $e) {
        setFlash('error', $e->getMessage());
        header('Location: ../index.php');
        exit();
    }
}
?>