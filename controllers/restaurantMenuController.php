<?php
session_start();

require_once '../includes/flash.php';
require_once '../models/restaurantMenuModel.php';
require_once '../components/connection.php';

$restaurantMenuModel = new restaurantMenuModel();
$restaurantMenus = $restaurantMenuModel->getAllMenus($con);

// Handle Add Menu
function handleAddMenu($con, $restaurantMenuModel) {
    // Sanitize input data
    $menu_name = mysqli_real_escape_string($con, $_POST['menu_name']);
    $menu_description = mysqli_real_escape_string($con, $_POST['menu_description']);
    $price = mysqli_real_escape_string($con, $_POST['price']);
    
    // Handle image upload
    $image = '';
    if(isset($_FILES['image'])) {
        $upload_result = $restaurantMenuModel->handleImageUpload($_FILES['image'], '../uploads/');
        
        if($upload_result['success']) {
            $image = $upload_result['filename'];
        } else {
            throw new Exception($upload_result['error']);
        }
    }
    
    // Add menu to database
    $restaurantMenuModel->addMenu($con, $menu_name, $menu_description, $image, $price);
    return setFlash("Success", "Menu Added Successfully!");
}

// Handle Update Menu
function handleUpdateMenu($con, $restaurantMenuModel) {
    // Sanitize input data
    $menu_id = mysqli_real_escape_string($con, $_POST['menu_id']);
    $menu_name = mysqli_real_escape_string($con, $_POST['menu_name']);
    $menu_description = mysqli_real_escape_string($con, $_POST['menu_description']);
    $price = mysqli_real_escape_string($con, $_POST['price']);
    $current_image = mysqli_real_escape_string($con, $_POST['current_image']);
    
    // Default to current image
    $image = $current_image;
    
    // Handle new image upload if provided
    if(isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_result = $restaurantMenuModel->handleImageUpload($_FILES['image']);
        
        if($upload_result['success']) {
            $image = $upload_result['filename'];
            
            // Delete old image
            if(!empty($current_image)) {
                $restaurantMenuModel->deleteImageFile($current_image);
            }
        } else {
            throw new Exception($upload_result['error']);
        }
    }
    
    // Update menu in database
    $restaurantMenuModel->updateMenu($con, $menu_id, $menu_name, $menu_description, $image, $price);
    return setFlash("Success", "Menu Updated Successfully!");
}

// Handle Delete Menu
function handleDeleteMenu($con, $restaurantMenuModel) {
    // Sanitize input
    $menu_id = mysqli_real_escape_string($con, $_POST['menu_id']);
    
    // Get menu item first to retrieve image filename
    $menu_item = $restaurantMenuModel->getMenuById($con, $menu_id);
    
    // Delete from database
    $restaurantMenuModel->deleteMenu($con, $menu_id);
    
    // Delete image file if exists
    if($menu_item && !empty($menu_item['image'])) {
        $restaurantMenuModel->deleteImageFile($menu_item['image']);
    }
    
    return setFlash("Success", "Menu Deleted Successfully!");
}

?>