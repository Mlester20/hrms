<?php

require_once '../models/specialOffersModel.php';
require_once '../includes/flash.php';

class OffersController {
    private $model;
    
    public function __construct($connection) {
        $this->model = new SpecialOffersModel($connection);
    }

    public function getAllOffers() {
        return $this->model->getAllOffers();
    }

    public function getOfferById($id) {
        return $this->model->getOfferById($id);
    }

    public function handleRequest($post, $files, $get) {
        // Handle POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processRequest($post, $files);
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }

        // Handle GET edit
        $edit_offer = null;
        if (isset($get['edit']) && !empty($get['edit'])) {
            $edit_offer = $this->getOfferById($get['edit']);
            if (!$edit_offer) setFlash('error_message', 'Offer not found!');
        }

        return $edit_offer;
    }

    private function processRequest($post, $files) {
        if (!isset($post['action'])) {
            setFlash('error_message', 'Invalid action!');
            return;
        }

        switch ($post['action']) {
            case 'add':
                $this->addOffer($post['title'], $post['description'], $files['image'] ?? null, $post['price']);
                break;
            case 'update':
                $image = (isset($files['image']) && $files['image']['error'] !== 4) ? $files['image'] : null;
                $this->updateOffer($post['offers_id'], $post['title'], $post['description'], $image, $post['price']);
                break;
            case 'delete':
                $this->deleteOffer($post['offers_id']);
                break;
            default:
                setFlash('error_message', 'Invalid action!');
        }
    }

    private function addOffer($title, $description, $image, $price) {
        $image_name = '';
        if ($image && $image['error'] == 0) {
            $uploadResult = $this->uploadImage($image);
            if (!$uploadResult['success']) {
                setFlash('error_message', $uploadResult['message']);
                return;
            }
            $image_name = $uploadResult['filename'];
        }

        if ($this->model->insertOffer($title, $description, $image_name, $price)) {
            setFlash('success_message', 'Offer added successfully!');
        } else {
            setFlash('error_message', 'Failed to add offer.');
        }
    }

    private function updateOffer($id, $title, $description, $image, $price) {
        $current_offer = $this->model->getOfferById($id);
        if (!$current_offer) {
            setFlash('error_message', 'Offer not found!');
            return;
        }

        $image_name = $current_offer['image'];

        if ($image && $image['error'] == 0) {
            $this->deleteImageFile($current_offer['image']);
            $uploadResult = $this->uploadImage($image);
            if (!$uploadResult['success']) {
                setFlash('error_message', $uploadResult['message']);
                return;
            }
            $image_name = $uploadResult['filename'];
        }

        if ($this->model->updateOfferData($id, $title, $description, $image_name, $price)) {
            setFlash('success_message', 'Offer updated successfully!');
        } else {
            setFlash('error_message', 'Failed to update offer.');
        }
    }

    private function deleteOffer($id) {
        $offer = $this->model->getOfferById($id);
        if ($offer && !empty($offer['image'])) {
            $this->deleteImageFile($offer['image']);
        }

        if ($this->model->deleteOfferData($id)) {
            setFlash('success_message', 'Offer deleted successfully!');
        } else {
            setFlash('error_message', 'Failed to delete offer.');
        }
    }

    private function uploadImage($image) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!in_array($image['type'], $allowed_types)) {
            return ['success' => false, 'message' => 'Only JPG, JPEG, PNG & GIF files are allowed.'];
        }
        if ($image['size'] > 5000000) {
            return ['success' => false, 'message' => 'Image too large. Max 5MB.'];
        }

        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);

        $image_name = time() . '_' . basename($image['name']);
        if (!move_uploaded_file($image['tmp_name'], $target_dir . $image_name)) {
            return ['success' => false, 'message' => 'Failed to upload image.'];
        }

        return ['success' => true, 'filename' => $image_name];
    }

    private function deleteImageFile($filename) {
        if (!empty($filename)) {
            $path = "../uploads/" . $filename;
            if (file_exists($path)) unlink($path);
        }
    }
}