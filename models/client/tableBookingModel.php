<?php

    class tableBookingModel{

        private $con;

        public function __construct($con){
            $this->con = $con;
        }

        public function getTables(){
            try{
                $query = "SELECT * FROM restaurant_tables ORDER BY table_id ASC";
                $stmt = $this->con->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();

                $tables = [];
                while($row = mysqli_fetch_assoc($result)){
                    $tables[] = $row;
                }
                return $tables;
            }catch(Exception $e){
                throw new Exception("Error getting tables ". $e->getMessage(), 500);
            }
        }

    }

?>