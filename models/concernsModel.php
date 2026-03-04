<?php

    class BaseModel{
        protected $con;

        public function __construct($con){
            $this->con = $con;
        }
    }

    class concernsModel extends BaseModel {
        protected $concerns = 'concerns';
        protected $user = 'users';

        public function getAllConcerns(){
            try {
                $query = "SELECT c.id, c.subject, c.message, c.created_at, u.name, u.email, u.address 
                          FROM {$this->concerns} c
                          JOIN {$this->user} u ON c.user_id = u.user_id
                          ORDER BY c.created_at DESC";
                $stmt = $this->con->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();
    
                $concerns = [];
                while ($row = $result->fetch_assoc()) {
                    $concerns[] = $row;
                }
    
                $stmt->close();
                return $concerns;
            } catch (Exception $e) {
                throw new Exception("Error fetching concerns: " . $e->getMessage());
            }
        }
    }

?>