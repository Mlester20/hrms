<?php
session_start();

require_once '../includes/flash.php';
require_once '../models/restaurantMenuModel.php';
require_once '../components/connection.php';

$restaurantMenuModel = new restaurantMenuModel();
$restaurantMenus = $restaurantMenuModel->getAllMenus($con);

$error = null;
$edit_menu = null;

// Handle Add Menu
if (isset($_POST['add_menu'])) {
    try {
        $success_message = handleAddMenu($con, $restaurantMenuModel);
        $_SESSION['success'] = $success_message;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle Update Menu
if (isset($_POST['update_menu'])) {
    try {
        $success_message = handleUpdateMenu($con, $restaurantMenuModel);
        $_SESSION['success'] = $success_message;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle Delete Menu
if (isset($_POST['delete_menu'])) {
    try {
        $success_message = handleDeleteMenu($con, $restaurantMenuModel);
        $_SESSION['success'] = $success_message;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}

// Get menu item for editing
if(isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_menu = $restaurantMenuModel->getMenuById($con, $_GET['edit']);
}

// Handle Add Menu
function handleAddMenu($con, $restaurantMenuModel) {
    $menu_name = mysqli_real_escape_string($con, $_POST['menu_name']);
    $menu_description = mysqli_real_escape_string($con, $_POST['menu_description']);
    $price = mysqli_real_escape_string($con, $_POST['price']);
    
    $image = '';
    if(isset($_FILES['image'])) {
        $upload_result = $restaurantMenuModel->handleImageUpload($_FILES['image'], '../uploads/');
        if($upload_result['success']) {
            $image = $upload_result['filename'];
        } else {
            throw new Exception($upload_result['error']);
        }
    }
    
    $restaurantMenuModel->addMenu($con, $menu_name, $menu_description, $image, $price);
    return setFlash("Success", "Menu Added Successfully!");
}

// Handle Update Menu
function handleUpdateMenu($con, $restaurantMenuModel) {
    $menu_id = mysqli_real_escape_string($con, $_POST['menu_id']);
    $menu_name = mysqli_real_escape_string($con, $_POST['menu_name']);
    $menu_description = mysqli_real_escape_string($con, $_POST['menu_description']);
    $price = mysqli_real_escape_string($con, $_POST['price']);
    $current_image = mysqli_real_escape_string($con, $_POST['current_image']);
    
    $image = $current_image;
    
    if(isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_result = $restaurantMenuModel->handleImageUpload($_FILES['image']);
        if($upload_result['success']) {
            $image = $upload_result['filename'];
            if(!empty($current_image)) {
                $restaurantMenuModel->deleteImageFile($current_image);
            }
        } else {
            throw new Exception($upload_result['error']);
        }
    }
    
    $restaurantMenuModel->updateMenu($con, $menu_id, $menu_name, $menu_description, $image, $price);
    return setFlash("Success", "Menu Updated Successfully!");
}

// Handle Delete Menu
function handleDeleteMenu($con, $restaurantMenuModel) {
    $menu_id = mysqli_real_escape_string($con, $_POST['menu_id']);
    $menu_item = $restaurantMenuModel->getMenuById($con, $menu_id);
    
    $restaurantMenuModel->deleteMenu($con, $menu_id);
    
    if($menu_item && !empty($menu_item['image'])) {
        $restaurantMenuModel->deleteImageFile($menu_item['image']);
    }
    
    return setFlash("Success", "Menu Deleted Successfully!");
}
?>