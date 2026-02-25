<?php

class authModel {

    private $con;

    public function __construct($con){
       $this->con = $con;
    }

    public function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    public function verifyPassword($password, $hash) {
        if (password_verify($password, $hash)) return true;
        if (md5($password) === $hash) return true;
        return false;
    }

    public function login($email, $password) {
        try {
            $query = "SELECT * FROM users WHERE email = ?";
            $stmt = $this->con->prepare($query);

            if (!$stmt) throw new Exception("Database error: " . $this->con->error);

            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                if ($this->verifyPassword($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['address'] = $row['address'];
                    $_SESSION['phone'] = $row['phone'];
                    $_SESSION['role'] = $row['role'];

                    $stmt->close();
                    return true;
                }
            }

            $stmt->close();
            return false;

        } catch (Exception $e) {
            throw new Exception("Error during login: " . $e->getMessage());
        }
    }

    public function register($name, $email, $password, $address, $phone) {
        try {
            $checkQuery = "SELECT user_id FROM users WHERE email = ?";
            $checkStmt = $this->con->prepare($checkQuery);
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) throw new Exception("Email already exists!");
            $checkStmt->close();

            $hashedPassword = $this->hashPassword($password);

            $insertQuery = "INSERT INTO users (name, address, email, password, role, phone) VALUES (?, ?, ?, ?, ?, ?)";
            $insertStmt = $this->con->prepare($insertQuery);
            $role = 'user';
            $insertStmt->bind_param("ssssss", $name, $address, $email, $hashedPassword, $role, $phone);

            if (!$insertStmt->execute()) throw new Exception("Failed to register user: " . $insertStmt->error);

            $insertStmt->close();
            return true;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}

?>