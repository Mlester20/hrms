<?php
// process_offer.php - Processes form submissions for offers

session_start();
include '../components/config.php';
include 'offersController.php';

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Add new offer
    if ($action === 'add') {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $image = isset($_FILES['image']) ? $_FILES['image'] : null;
        
        $result = addOffer($con, $title, $description, $image, $price);
        
        if ($result['success']) {
            $_SESSION['success_message'] = $result['message'];
        } else {
            $_SESSION['error_message'] = $result['message'];
        }
        
        header('Location: ../admin/specialOffers.php');
        exit();
    }
    
    // Update offer
    elseif ($action === 'update') {
        $id = $_POST['offers_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $image = isset($_FILES['image']) && $_FILES['image']['error'] !== 4 ? $_FILES['image'] : null;
        
        $result = updateOffer($con, $id, $title, $description, $image, $price);
        
        if ($result['success']) {
            $_SESSION['success_message'] = $result['message'];
        } else {
            $_SESSION['error_message'] = $result['message'];
        }
        
        header('Location: ../admin/specialOffers.php');
        exit();
    }
    
    // Delete offer
    elseif ($action === 'delete') {
        $id = $_POST['offers_id'];
        
        $result = deleteOffer($con, $id);
        
        if ($result['success']) {
            $_SESSION['success_message'] = $result['message'];
        } else {
            $_SESSION['error_message'] = $result['message'];
        }
        
        header('Location: ../admin/specialOffers.php');
        exit();
    }
}

// If not POST or no valid action, redirect back to offers page
header('Location: ../admin/specialOffers.php');
exit();
?>