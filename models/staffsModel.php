<?php

class staffsModel {
    
    private $con;
    
    public function __construct($con) {
        $this->con = $con;
    }
    
    /**
     * Get all staffs from database
     */
    public function getAllStaffs() {
        try {
            $query = "SELECT * FROM staffs ORDER BY staff_id DESC";
            $stmt = $this->con->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();

            $staffs = [];
            while($row = mysqli_fetch_assoc($result)){
                $staffs[] = $row;
            }
            return $staffs;
            $stmt->close();
        } catch(Exception $e) {
            throw new Exception("Error fetching staffs: " . $e->getMessage());
        }
    }
    
    /**
     * Add a new staff member
     */
    public function addStaff($data) {
        try {
            $name = $data['name'];
            $position = $data['position'];
            $address = $data['address'];
            $shift_type = $data['shift_type'];
            $phone_number = $data['phone_number'];
            $email = $data['email'];
            $password = md5($data['password']); // Encrypt password with MD5
            
            $query = "INSERT INTO staffs (name, position, address, shift_type, phone_number, email, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("sssssss", $name, $position, $address, $shift_type, $phone_number, $email, $password);
            
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
        } catch(Exception $e) {
            throw new Exception("Error adding staff: " . $e->getMessage());
        }
    }
    
    /**
     * Update an existing staff member
     */
    public function updateStaff($data) {
        try {
            $staff_id = $data['staff_id'];
            $name = $data['name'];
            $position = $data['position'];
            $address = $data['address'];
            $shift_type = $data['shift_type'];
            $phone_number = $data['phone_number'];
            $email = $data['email'];
            $password = $data['password'];
            
            // Update with or without password change
            if (!empty($password)) {
                $password = md5($password); // Encrypt password with MD5
                $query = "UPDATE staffs SET name = ?, position = ?, address = ?, shift_type = ?, phone_number = ?, email = ?, password = ? WHERE staff_id = ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("sssssssi", $name, $position, $address, $shift_type, $phone_number, $email, $password, $staff_id);
            } else {
                $query = "UPDATE staffs SET name = ?, position = ?, address = ?, shift_type = ?, phone_number = ?, email = ? WHERE staff_id = ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("ssssssi", $name, $position, $address, $shift_type, $phone_number, $email, $staff_id);
            }
            
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
        } catch(Exception $e) {
            throw new Exception("Error updating staff: " . $e->getMessage());
        }
    }
    
    /**
     * Delete a staff member
     */
    public function deleteStaff($staff_id) {
        try {
            $query = "DELETE FROM staffs WHERE staff_id = ?";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("i", $staff_id);
            
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
        } catch(Exception $e) {
            throw new Exception("Error deleting staff: " . $e->getMessage());
        }
    }
}

?>