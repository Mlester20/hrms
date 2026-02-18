<?php
    class Database{
        private $host = 'localhost';
        private $user = 'root';
        private $pass = '';
        private $dbname = 'hoteldb';
        private $conn;

        public function __construct(){
            $this->connect();
        }

        private function connect(){
            $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);

            // Check connection
            if($this->conn->connect_error){
                // Handle connection error
                die("Connection failed:" . $this->conn->connect_error);
            }
            $this->conn->set_charset('utf8mb4');
        }

        public function getConnection(){
            return $this->conn;
        }

        public function closeConnection(){
            if($this->conn){
                $this->conn->close();
            }
        }

    }

    //sample usage
    $db = new Database();
    $con = $db->getConnection();

?>