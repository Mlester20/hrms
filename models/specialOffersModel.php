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
        try {
            $query = "SELECT * FROM special_offers ORDER BY offers_id DESC";
            $stmt = $this->con->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $offers = [];
            while ($row = $result->fetch_assoc()) {
                $offers[] = $row;
            }
            $stmt->close();
            
            return $offers;
        } catch(Exception $e) {
            throw new Exception("Error fetching offers: " . $e->getMessage());
        }
    }
    
    /**
     * Get single offer by ID
     */
    public function getOfferById($id) {
        try {
            $query = "SELECT * FROM special_offers WHERE offers_id = ?";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $offer = $result->fetch_assoc();
                $stmt->close();
                return $offer;
            }
            
            $stmt->close();
            return null;
        } catch(Exception $e) {
            throw new Exception("Error fetching offer by ID: " . $e->getMessage());
        }
    }
    
    /**
     * Insert new offer into database
     */
    public function insertOffer($title, $description, $image_name, $price) {
        try {
            $query = "INSERT INTO special_offers (title, description, image, price) 
                      VALUES (?, ?, ?, ?)";
            
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("sssi", $title, $description, $image_name, $price);
            
            if ($stmt->execute()) {
                $stmt->close();
                return true;
            }
            throw new Exception("Failed to execute query");
        } catch(Exception $e) {
            throw new Exception("Error inserting offer: " . $e->getMessage());
        }
    }
    
    /**
     * Update existing offer in database
     */
    public function updateOfferData($id, $title, $description, $image_name, $price) {
        try {
            $query = "UPDATE special_offers 
                      SET title = ?, description = ?, image = ?, price = ? 
                      WHERE offers_id = ?";
            
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("sssii", $title, $description, $image_name, $price, $id);
            
            if ($stmt->execute()) {
                $stmt->close();
                return true;
            }
            throw new Exception("Failed to execute query");
        } catch(Exception $e) {
            throw new Exception("Error updating offer: " . $e->getMessage());
        }
    }
    
    /**
     * Delete offer from database
     */
    public function deleteOfferData($id) {
        try {
            $query = "DELETE FROM special_offers WHERE offers_id = ?";
            
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $stmt->close();
                return true;
            }
            throw new Exception("Failed to execute query");
        } catch(Exception $e) {
            throw new Exception("Error deleting offer: " . $e->getMessage());
        }
    }
    
    /**
     * Get database error message (deprecated - use exceptions instead)
     */
    public function getError() {
        return mysqli_error($this->con);
    }
}

?>