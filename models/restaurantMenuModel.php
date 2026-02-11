<?php

class restaurantMenuModel {
    
    /**
     * Get all menu items ordered by ID descending
     */
    public function getAllMenus($con) {
        try {
            $query = "SELECT * FROM restaurant_menu ORDER BY menu_id DESC";
            $stmt = $con->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $menus = [];
            while($row = $result->fetch_assoc()) {
                $menus[] = $row;
            }
            $stmt->close();
            return $menus;
        } catch(Exception $e) {
            throw new Exception("Error fetching menus: " . $e->getMessage());
        }       
    }
    
    /**
     * Get a single menu item by ID
     */
    public function getMenuById($con, $menu_id) {
        try {
            $query = "SELECT * FROM restaurant_menu WHERE menu_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("i", $menu_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if($result->num_rows > 0) {
                return $result->fetch_assoc();
            }
            return null;
        } catch(Exception $e) {
            throw new Exception("Error fetching menu by ID: " . $e->getMessage());
        }
    }
    
    /**
     * Add a new menu item
     */
    public function addMenu($con, $menu_name, $menu_description, $image, $price) {
        try {
            $sql = "INSERT INTO restaurant_menu (menu_name, menu_description, image, price) 
                    VALUES (?, ?, ?, ?)";
            
            $stmt = $con->prepare($sql);
            $stmt->bind_param("sssi", $menu_name, $menu_description, $image, $price);
            
            if($stmt->execute()) {
                $stmt->close();
                return true;
            }
            throw new Exception("Failed to execute query");
        } catch(Exception $e) {
            throw new Exception("Error adding menu: " . $e->getMessage());
        }
    }
    
    /**
     * Update an existing menu item
     */
    public function updateMenu($con, $menu_id, $menu_name, $menu_description, $image, $price) {
        try {
            $sql = "UPDATE restaurant_menu 
                    SET menu_name = ?, 
                        menu_description = ?, 
                        image = ?, 
                        price = ? 
                    WHERE menu_id = ?";
            
            $stmt = $con->prepare($sql);
            $stmt->bind_param("sssii", $menu_name, $menu_description, $image, $price, $menu_id);
            
            if($stmt->execute()) {
                $stmt->close();
                return true;
            }
            throw new Exception("Failed to execute query");
        } catch(Exception $e) {
            throw new Exception("Error updating menu: " . $e->getMessage());
        }
    }
    
    /**
     * Delete a menu item
     */
    public function deleteMenu($con, $menu_id) {
        try {
            $sql = "DELETE FROM restaurant_menu WHERE menu_id = ?";
            
            $stmt = $con->prepare($sql);
            $stmt->bind_param("i", $menu_id);
            
            if($stmt->execute()) {
                $stmt->close();
                return true;
            }
            throw new Exception("Failed to execute query");
        } catch(Exception $e) {
            throw new Exception("Error deleting menu: " . $e->getMessage());
        }
    }
    
    /**
     * Handle image upload
     */
    public function handleImageUpload($file, $upload_dir = '../uploads/menu/') {
        try {
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
            
            // Verify file size (max 5MB)
            if($file['size'] > 5 * 1024 * 1024) {
                return ['success' => false, 'error' => 'File size exceeds 5MB limit'];
            }
            
            // Create unique filename
            $new_filename = uniqid() . '.' . $filetype;
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                if(!mkdir($upload_dir, 0777, true)) {
                    return ['success' => false, 'error' => 'Failed to create upload directory'];
                }
            }
            
            $upload_path = $upload_dir . $new_filename;
            
            // Upload the file
            if(move_uploaded_file($file['tmp_name'], $upload_path)) {
                return ['success' => true, 'filename' => $new_filename];
            }
            
            return ['success' => false, 'error' => 'Failed to upload image'];
        } catch(Exception $e) {
            return ['success' => false, 'error' => 'Upload error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Delete image file from server
     */
    public function deleteImageFile($filename, $upload_dir = '../uploads/menu/') {
        try {
            if($filename && file_exists($upload_dir . $filename)) {
                return unlink($upload_dir . $filename);
            }
            return false;
        } catch(Exception $e) {
            throw new Exception("Error deleting image file: " . $e->getMessage());
        }
    }
}

?>