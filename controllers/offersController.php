<?php
// offersController.php - Backend functions for managing offers

/**
 * Functions for offers CRUD operations
 */

// Get all offers
function getAllOffers($con) {
    $query = "SELECT * FROM special_offers ORDER BY offers_id DESC";
    $result = mysqli_query($con, $query);
    $offers = [];
    
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $offers[] = $row;
        }
    }
    
    return $offers;
}

// Get single offer by ID
function getOfferById($con, $id) {
    $id = mysqli_real_escape_string($con, $id);
    $query = "SELECT * FROM special_offers WHERE offers_id = '$id'";
    $result = mysqli_query($con, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Add new offer
function addOffer($con, $title, $description, $image, $price) {
    $title = mysqli_real_escape_string($con, $title);
    $description = mysqli_real_escape_string($con, $description);
    $price = mysqli_real_escape_string($con, $price);
    
    // Process image upload
    $image_name = '';
    if ($image['error'] == 0) {
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
    }
    
    $query = "INSERT INTO special_offers (title, description, image, price) VALUES ('$title', '$description', '$image_name', '$price')";
    
    if (mysqli_query($con, $query)) {
        return ['success' => true, 'message' => 'Offer added successfully!'];
    } else {
        return ['success' => false, 'message' => 'Error: ' . mysqli_error($con)];
    }
}

// Update an offer
function updateOffer($con, $id, $title, $description, $image, $price) {
    $id = mysqli_real_escape_string($con, $id);
    $title = mysqli_real_escape_string($con, $title);
    $description = mysqli_real_escape_string($con, $description);
    $price = mysqli_real_escape_string($con, $price);
    
    // Get current offer data
    $current_offer = getOfferById($con, $id);
    $image_name = $current_offer['image']; // Keep existing image if no new one
    
    // Process new image if uploaded
    if ($image && $image['error'] == 0) {
        // Delete old image if exists
        if (!empty($current_offer['image'])) {
            $old_image = "../uploads/" . $current_offer['image'];
            if (file_exists($old_image)) {
                unlink($old_image);
            }
        }
        
        $image_name = time() . '_' . basename($image['name']);
        $target_dir = "../uploads/";
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
    }
    
    $query = "UPDATE special_offers SET title='$title', description='$description', image='$image_name', price='$price' WHERE offers_id='$id'";
    
    if (mysqli_query($con, $query)) {
        return ['success' => true, 'message' => 'Offer updated successfully!'];
    } else {
        return ['success' => false, 'message' => 'Error: ' . mysqli_error($con)];
    }
}

// Delete an offer
function deleteOffer($con, $id) {
    $id = mysqli_real_escape_string($con, $id);
    
    // Get offer data to delete image file
    $offer = getOfferById($con, $id);
    
    if ($offer && !empty($offer['image'])) {
        $image_path = "../uploads/" . $offer['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    $query = "DELETE FROM special_offers WHERE offers_id = '$id'";
    
    if (mysqli_query($con, $query)) {
        return ['success' => true, 'message' => 'Offer deleted successfully!'];
    } else {
        return ['success' => false, 'message' => 'Error: ' . mysqli_error($con)];
    }
}
?>