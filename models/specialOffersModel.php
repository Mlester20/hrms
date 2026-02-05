<?php

class SpecialOffersModel {
    private $con;
    
    public function __construct($connection) {
        $this->con = $connection;
    }
    
    /**
     * Get all offers ordered by ID descending
     */
    public function getAllOffers() {
        $query = "SELECT * FROM special_offers ORDER BY offers_id DESC";
        $result = mysqli_query($this->con, $query);
        $offers = [];
        
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $offers[] = $row;
            }
        }
        
        return $offers;
    }
    
    /**
     * Get single offer by ID
     */
    public function getOfferById($id) {
        $id = mysqli_real_escape_string($this->con, $id);
        $query = "SELECT * FROM special_offers WHERE offers_id = '$id'";
        $result = mysqli_query($this->con, $query);
        
        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
        
        return null;
    }
    
    /**
     * Insert new offer into database
     */
    public function insertOffer($title, $description, $image_name, $price) {
        $title = mysqli_real_escape_string($this->con, $title);
        $description = mysqli_real_escape_string($this->con, $description);
        $price = mysqli_real_escape_string($this->con, $price);
        $image_name = mysqli_real_escape_string($this->con, $image_name);
        
        $query = "INSERT INTO special_offers (title, description, image, price) 
                  VALUES ('$title', '$description', '$image_name', '$price')";
        
        return mysqli_query($this->con, $query);
    }
    
    /**
     * Update existing offer in database
     */
    public function updateOfferData($id, $title, $description, $image_name, $price) {
        $id = mysqli_real_escape_string($this->con, $id);
        $title = mysqli_real_escape_string($this->con, $title);
        $description = mysqli_real_escape_string($this->con, $description);
        $price = mysqli_real_escape_string($this->con, $price);
        $image_name = mysqli_real_escape_string($this->con, $image_name);
        
        $query = "UPDATE special_offers 
                  SET title='$title', description='$description', image='$image_name', price='$price' 
                  WHERE offers_id='$id'";
        
        return mysqli_query($this->con, $query);
    }
    
    /**
     * Delete offer from database
     */
    public function deleteOfferData($id) {
        $id = mysqli_real_escape_string($this->con, $id);
        $query = "DELETE FROM special_offers WHERE offers_id = '$id'";
        
        return mysqli_query($this->con, $query);
    }
    
    /**
     * Get database error message
     */
    public function getError() {
        return mysqli_error($this->con);
    }
}

?>