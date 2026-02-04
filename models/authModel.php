<?php

class authModel {
    
    /**
     * Hash password using password_hash (bcrypt)
     * @param string $password
     * @return string hashed password
     */
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    /**
     * Verify password against hash
     * Supports both password_verify (bcrypt) and md5 for backward compatibility
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verifyPassword($password, $hash) {
        // Try password_verify first (for bcrypt hashed passwords)
        if (password_verify($password, $hash)) {
            return true;
        }

        // Fallback to MD5 for backward compatibility with old passwords
        if (md5($password) === $hash) {
            return true;
        }

        return false;
    }
    
    public function login($con, $email, $password) {
        try {
            $query = "SELECT user_id, name, role, address, phone FROM users WHERE email = ?";
            $stmt = $con->prepare($query);
            
            if (!$stmt) {
                throw new Exception("Database error: " . $con->error);
            }

            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                // Get stored password hash
                $passwordQuery = "SELECT password FROM users WHERE user_id = ?";
                $passwordStmt = $con->prepare($passwordQuery);
                $passwordStmt->bind_param("i", $row['user_id']);
                $passwordStmt->execute();
                $passwordResult = $passwordStmt->get_result();
                $passwordRow = $passwordResult->fetch_assoc();
                $storedHash = $passwordRow['password'];
                $passwordStmt->close();

                // Verify password
                if ($this->verifyPassword($password, $storedHash)) {
                    // Set session data
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['email'] = $email;
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['address'] = $row['address'];
                    $_SESSION['phone'] = $row['phone'];
                    $_SESSION['role'] = $row['role'];

                    $stmt->close();
                    return true;
                } else {
                    $stmt->close();
                    return false;
                }
            } else {
                $stmt->close();
                return false;
            }
        } catch (Exception $e) {
            throw new Exception("Error during login: " . $e->getMessage());
        }
    }

    /**
     * Register new user
     */
    public function register($con, $name, $email, $password, $address, $phone) {
        try {
            // Check if email already exists
            $checkQuery = "SELECT user_id FROM users WHERE email = ?";
            $checkStmt = $con->prepare($checkQuery);
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                throw new Exception("Email already exists!");
            }
            $checkStmt->close();

            // Hash password using bcrypt
            $hashedPassword = $this->hashPassword($password);

            // Insert new user
            $insertQuery = "INSERT INTO users (name, address, email, password, role, phone) VALUES (?, ?, ?, ?, ?, ?)";
            $insertStmt = $con->prepare($insertQuery);
            $role = 'user'; // Default role
            $insertStmt->bind_param("ssssss", $name, $address, $email, $hashedPassword, $role, $phone);

            if (!$insertStmt->execute()) {
                throw new Exception("Failed to register user: " . $insertStmt->error);
            }

            $insertStmt->close();
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}

?>