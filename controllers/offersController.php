<?php

require_once '../models/specialOffersModel.php';
require_once '../includes/flash.php';

class OffersController {
    private $model;
    
    public function __construct($connection) {
        $this->model = new SpecialOffersModel($connection);
    }
    
    /**
     * Get all offers
     */
    public function getAllOffers() {
        return $this->model->getAllOffers();
    }
    
    /**
     * Get single offer by ID
     */
    public function getOfferById($id) {
        return $this->model->getOfferById($id);
    }
    
    /**
     * Add new offer with image upload handling
     */
    public function addOffer($title, $description, $image, $price) {
        // Process image upload
        $image_name = '';
        if ($image && $image['error'] == 0) {
            $uploadResult = $this->uploadImage($image);
            
            if (!$uploadResult['success']) {
                return $uploadResult;
            }
            
            $image_name = $uploadResult['filename'];
        }
        
        // Insert into database
        if ($this->model->insertOffer($title, $description, $image_name, $price)) {
            setFlash('success_message', 'Offer added successfully!');
        } else {
            setFlash('error_message', 'Error: ' . $this->model->getError());
        }
    }
    
    /**
     * Update existing offer
     */
    public function updateOffer($id, $title, $description, $image, $price) {
        // Get current offer data
        $current_offer = $this->model->getOfferById($id);
        if (!$current_offer) {
            setFlash('error_message', 'Offer not found!');
        }
        
        $image_name = $current_offer['image']; // Keep existing image if no new one
        
        // Process new image if uploaded
        if ($image && $image['error'] == 0) {
            // Delete old image if exists
            $this->deleteImageFile($current_offer['image']);
            
            $uploadResult = $this->uploadImage($image);
            
            if (!$uploadResult['success']) {
                return $uploadResult;
            }
            
            $image_name = $uploadResult['filename'];
        }
        
        // Update database
        if ($this->model->updateOfferData($id, $title, $description, $image_name, $price)) {
            setFlash('success_message', 'Offer updated successfully!');
        } else {
            setFlash('error_message', 'Error: ' . $this->model->getError());
        }
    }
    
    /**
     * Delete an offer
     */
    public function deleteOffer($id) {
        // Get offer data to delete image file
        $offer = $this->model->getOfferById($id);
        
        if ($offer && !empty($offer['image'])) {
            $this->deleteImageFile($offer['image']);
        }
        
        // Delete from database
        if ($this->model->deleteOfferData($id)) {
            setFlash('success_message', 'Offer deleted successfully!');
        } else {
            setFlash('error_message', 'Error: ' . $this->model->getError());
        }
    }
    
    /**
     * Upload image file
     */
    private function uploadImage($image) {
        $image_name = time() . '_' . basename($image['name']);
        $target_dir = "../uploads/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $target_file = $target_dir . $image_name;
        
        // Check file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!in_array($image['type'], $allowed_types)) {
            return ['success' => false, 'message' => 'Only JPG, JPEG, PNG & GIF files are allowed.'];
        }
        
        // Check file size (max 5MB)
        if ($image['size'] > 5000000) {
            return ['success' => false, 'message' => 'Image is too large. Maximum file size is 5MB.'];
        }
        
        if (!move_uploaded_file($image['tmp_name'], $target_file)) {
            return ['success' => false, 'message' => 'Failed to upload image.'];
        }
        
        return ['success' => true, 'filename' => $image_name];
    }
    
    /**
     * Delete image file from uploads directory
     */
    private function deleteImageFile($filename) {
        if (!empty($filename)) {
            $image_path = "../uploads/" . $filename;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
    }
    
    /**
     * Process form submission
     */
    public function processRequest($post, $files) {
        if (!isset($post['action'])) {
            return ['success' => false, 'message' => 'Invalid action!'];
        }
        
        $action = $post['action'];
        
        switch ($action) {
            case 'add':
                $title = $post['title'];
                $description = $post['description'];
                $price = $post['price'];
                $image = isset($files['image']) ? $files['image'] : null;
                
                return $this->addOffer($title, $description, $image, $price);
                
            case 'update':
                $id = $post['offers_id'];
                $title = $post['title'];
                $description = $post['description'];
                $price = $post['price'];
                $image = isset($files['image']) && $files['image']['error'] !== 4 ? $files['image'] : null;
                
                return $this->updateOffer($id, $title, $description, $image, $price);
                
            case 'delete':
                $id = $post['offers_id'];
                return $this->deleteOffer($id);
                
            default:
                return ['success' => false, 'message' => 'Invalid action!'];
        }
    }
}

?>