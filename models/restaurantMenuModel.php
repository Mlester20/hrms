<?php

class restaurantMenuModel {
    
    /**
     * Get all menu items ordered by ID descending
     */
    public function getAllMenus($con) {
        try {
            $query = "SELECT * FROM restaurant_menu ORDER BY menu_id DESC";
            $menu_items = mysqli_query($con, $query);
            return $menu_items;
        } catch(Exception $e) {
            throw new Exception("Error fetching menus: " . $e->getMessage());
        }       
    }
    
    /**
     * Get a single menu item by ID
     */
    public function getMenuById($con, $menu_id) {
        $query = "SELECT * FROM restaurant_menu WHERE menu_id = $menu_id";
        $result = mysqli_query($con, $query);
        
        if($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
        return null;
    }
    
    /**
     * Add a new menu item
     */
    public function addMenu($con, $menu_name, $menu_description, $image, $price) {
        $sql = "INSERT INTO restaurant_menu (menu_name, menu_description, image, price) 
                VALUES ('$menu_name', '$menu_description', '$image', '$price')";
        
        if(mysqli_query($con, $sql)) {
            return true;
        }
        throw new Exception("Error adding menu: " . mysqli_error($con));
    }
    
    /**
     * Update an existing menu item
     */
    public function updateMenu($con, $menu_id, $menu_name, $menu_description, $image, $price) {
        $sql = "UPDATE restaurant_menu 
                SET menu_name='$menu_name', 
                    menu_description='$menu_description', 
                    image='$image', 
                    price='$price' 
                WHERE menu_id=$menu_id";
        
        if(mysqli_query($con, $sql)) {
            return true;
        }
        throw new Exception("Error updating menu: " . mysqli_error($con));
    }
    
    /**
     * Delete a menu item
     */
    public function deleteMenu($con, $menu_id) {
        $sql = "DELETE FROM restaurant_menu WHERE menu_id=$menu_id";
        
        if(mysqli_query($con, $sql)) {
            return true;
        }
        throw new Exception("Error deleting menu: " . mysqli_error($con));
    }
    
    /**
     * Handle image upload
     */
    public function handleImageUpload($file, $upload_dir = '../uploads/menu/') {
        if(!isset($file) || $file['error'] !== 0) {
            return ['success' => false, 'error' => 'No file uploaded'];
        }
        
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $file['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        // Verify file extension
        if(!in_array(strtolower($filetype), $allowed)) {
            return [
                'success' => false, 
                'error' => 'Invalid file type. Only JPG, JPEG, PNG and WEBP files are allowed.'
            ];
        }
        
        // Create unique filename
        $new_filename = uniqid() . '.' . $filetype;
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $upload_path = $upload_dir . $new_filename;
        
        // Upload the file
        if(move_uploaded_file($file['tmp_name'], $upload_path)) {
            return ['success' => true, 'filename' => $new_filename];
        }
        
        return ['success' => false, 'error' => 'Failed to upload image'];
    }
    
    /**
     * Delete image file from server
     */
    public function deleteImageFile($filename, $upload_dir = '../uploads/menu/') {
        if($filename && file_exists($upload_dir . $filename)) {
            return unlink($upload_dir . $filename);
        }
        return false;
    }
}

?>