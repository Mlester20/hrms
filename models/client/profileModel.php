<?php

class profileModel {
    
    public function getProfile($con, $user_id) {
        try {
            $query = "SELECT user_id, name, address, email, phone FROM users WHERE user_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
            
            if (!$user) {
                throw new Exception("User not found.");
            }
            
            return $user;
        } catch (Exception $e) {
            throw new Exception("Error fetching profile: " . $e->getMessage());
        }
    }

    public function verifyPassword($con, $user_id, $current_password) {
        try {
            $hashedPassword = null;
            $query = "SELECT password FROM users WHERE user_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($hashedPassword);
            $stmt->fetch();
            $stmt->close();

            // Check if password exists
            if ($hashedPassword === null) {
                throw new Exception("User not found.");
            }

            // Verify password using MD5
            if (md5($current_password) !== $hashedPassword) {
                throw new Exception("Incorrect current password. Please try again.");
            }

            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function updateProfile($con, $user_id, $name, $address, $email, $phone) {
        try {
            $query = "UPDATE users SET name = ?, address = ?, email = ?, phone = ? WHERE user_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ssssi", $name, $address, $email, $phone, $user_id);

            if (!$stmt->execute()) {
                throw new Exception("Failed to update profile: " . $stmt->error);
            }

            $stmt->close();
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function updatePassword($con, $user_id, $new_password, $confirm_password) {
        try {
            // Check if passwords match
            if ($new_password !== $confirm_password) {
                throw new Exception("New password and confirm password do not match.");
            }

            // Check password is not empty
            if (empty($new_password)) {
                throw new Exception("New password cannot be empty.");
            }

            // Hash the new password using MD5
            $new_hashed_password = md5($new_password);
            
            $query = "UPDATE users SET password = ? WHERE user_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("si", $new_hashed_password, $user_id);

            if (!$stmt->execute()) {
                throw new Exception("Failed to update password: " . $stmt->error);
            }

            $stmt->close();
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}

?>