<?php

    class BaseModel{
        protected $con;

        public function __construct($con){
            $this->con = $con;
        }
    }

    class registerUser extends BaseModel {

        /**
         * Hash password using bcrypt
         */
        public function hashPassword($password) {
            return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
        }

        public function register($name, $email, $password, $confirmPassword, $phone, $address, $role = 'user') {

            if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
                throw new Exception('All fields are required.');
            }

            // Check if passwords match
            if ($password !== $confirmPassword) {
                throw new Exception('Passwords do not match!');
            }

            if (strlen($password) < 6) {
                throw new Exception('Password must be at least 6 characters long.');
            }

            try {
                // Check if email already exists
                $query = "SELECT user_id FROM users WHERE email = ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    throw new Exception('Email already exists!');
                }
                $stmt->close();
                
                $hashedPassword = $this->hashPassword($password);

                $query = "INSERT INTO users (name, address, email, password, role, phone) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param('ssssss', $name, $address, $email, $hashedPassword, $role, $phone);

                if (!$stmt->execute()) {
                    throw new Exception('Error adding user: ' . $stmt->error);
                }

                $stmt->close();
                return true;
            } catch (Exception $e) {
                throw new Exception("Error creating account: " . $e->getMessage(), 500);
            }
        }
    }

?>