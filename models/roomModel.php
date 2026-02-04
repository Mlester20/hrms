<?php

class roomModel {
    
    private $con;
    
    public function __construct($con) {
        $this->con = $con;
    }
    
    /**
     * Get all rooms with pagination
     */
    public function getRoomsWithPagination($offset, $limit) {
        try {
            $query = "SELECT rooms.id, rooms.title, rooms.room_type_id, rooms.images, rooms.price, rooms.includes, room_type.title AS room_type_title 
                      FROM rooms 
                      INNER JOIN room_type ON rooms.room_type_id = room_type.id
                      LIMIT ?, ?";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("ii", $offset, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        } catch(Exception $e) {
            throw new Exception("Error fetching rooms: " . $e->getMessage());
        }
    }
    
    /**
     * Get total count of rooms
     */
    public function getTotalRoomsCount() {
        try {
            $query = "SELECT COUNT(*) as total FROM rooms";
            $result = $this->con->query($query);
            $row = $result->fetch_assoc();
            return $row['total'];
        } catch(Exception $e) {
            throw new Exception("Error counting rooms: " . $e->getMessage());
        }
    }
    
    /**
     * Get all room types
     */
    public function getAllRoomTypes() {
        try {
            $query = "SELECT id, title FROM room_type";
            $result = $this->con->query($query);
            return $result;
        } catch(Exception $e) {
            throw new Exception("Error fetching room types: " . $e->getMessage());
        }
    }
    
    /**
     * Add a new room with multiple images
     */
    public function addRoom($data, $fileData) {
        try {
            $title = $data['title'];
            $room_type_id = $data['room_type_id'];
            $price = $data['price'];
            $package_name = $data['package_name'];
            
            // Handle multiple image uploads
            $imageNames = $this->handleMultipleImageUpload($fileData);
            
            if (empty($imageNames)) {
                throw new Exception("No images uploaded.");
            }
            
            // Convert array of image names to JSON string for storage
            $imagesJson = json_encode($imageNames);
            
            $query = "INSERT INTO rooms (title, room_type_id, images, price, includes) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("sisds", $title, $room_type_id, $imagesJson, $price, $package_name);
            
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
        } catch(Exception $e) {
            throw new Exception("Error adding room: " . $e->getMessage());
        }
    }
    
    /**
     * Update an existing room
     */
    public function updateRoom($data, $fileData) {
        try {
            $id = $data['id'];
            $title = $data['title'];
            $room_type_id = $data['room_type_id'];
            $price = $data['price'];
            
            // Get existing images
            $existingImages = $this->getRoomImages($id);
            
            // Check if new images were uploaded
            $hasNewImages = false;
            if (isset($fileData['images']) && is_array($fileData['images']['name'])) {
                foreach ($fileData['images']['error'] as $error) {
                    if ($error === UPLOAD_ERR_OK) {
                        $hasNewImages = true;
                        break;
                    }
                }
            }
            
            // If new images uploaded, handle them
            if ($hasNewImages) {
                $newImages = $this->handleMultipleImageUpload($fileData);
                
                // Delete old images
                $this->deleteImages($existingImages);
                
                // Update with new images
                $imagesJson = json_encode($newImages);
                $query = "UPDATE rooms SET title = ?, room_type_id = ?, images = ?, price = ? WHERE id = ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("sisdi", $title, $room_type_id, $imagesJson, $price, $id);
            } else {
                // Update without changing images
                $query = "UPDATE rooms SET title = ?, room_type_id = ?, price = ? WHERE id = ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("sidi", $title, $room_type_id, $price, $id);
            }
            
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
        } catch(Exception $e) {
            throw new Exception("Error updating room: " . $e->getMessage());
        }
    }
    
    /**
     * Delete a room
     */
    public function deleteRoom($id) {
        try {
            // Get images to delete
            $images = $this->getRoomImages($id);
            
            // Delete image files
            $this->deleteImages($images);
            
            // Delete room from database
            $query = "DELETE FROM rooms WHERE id = ?";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("i", $id);
            
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
        } catch(Exception $e) {
            throw new Exception("Error deleting room: " . $e->getMessage());
        }
    }
    
    /**
     * Get room images by room ID
     */
    private function getRoomImages($id) {
        $query = "SELECT images FROM rooms WHERE id = ?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($imagesJson);
        $stmt->fetch();
        $stmt->close();
        
        if ($imagesJson) {
            return json_decode($imagesJson, true) ?: [];
        }
        
        return [];
    }
    
    /**
     * Handle multiple image uploads
     */
    private function handleMultipleImageUpload($fileData) {
        $imageNames = [];
        
        if (!isset($fileData['images']) || !is_array($fileData['images']['name'])) {
            return $imageNames;
        }
        
        $totalFiles = count($fileData['images']['name']);
        
        for ($i = 0; $i < $totalFiles; $i++) {
            if ($fileData['images']['error'][$i] === UPLOAD_ERR_OK) {
                $imageTmpPath = $fileData['images']['tmp_name'][$i];
                $imageName = $fileData['images']['name'][$i];
                $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
                
                // Define allowed file types
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (!in_array(strtolower($imageExtension), $allowedExtensions)) {
                    throw new Exception("Invalid image file type for image #" . ($i + 1));
                }
                
                $uploadFolder = '../uploads/';
                $newImageName = uniqid('room_', true) . '.' . $imageExtension;
                $destinationPath = $uploadFolder . $newImageName;
                
                if (!move_uploaded_file($imageTmpPath, $destinationPath)) {
                    throw new Exception("Failed to upload image #" . ($i + 1));
                }
                
                $imageNames[] = $newImageName;
            } else if ($fileData['images']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                throw new Exception("Error uploading image #" . ($i + 1));
            }
        }
        
        return $imageNames;
    }
    
    /**
     * Delete multiple images from server
     */
    private function deleteImages($images) {
        if (empty($images)) {
            return;
        }
        
        foreach ($images as $image) {
            $imagePath = '../uploads/' . $image;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
    }
}

?>