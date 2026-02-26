<?php

    class BaseModel{
        protected $con;

        public function __construct($con){
            $this->con = $con;
        }
    }

    class authModel extends BaseModel {
        protected $table = 'users';

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
                $query = "SELECT * FROM {$this->table} WHERE email = ?";
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
    }

?>