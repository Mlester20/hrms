<?php

    class BaseModel{
        protected $con;

        public function __construct($con){
            $this->con = $con;
        }
    }

    class tableBookingModel extends BaseModel{
        protected $table = 'restaurant_tables';

        public function getTables(){
            try{
                $query = "SELECT * FROM {$this->table} ORDER BY table_id ASC";
                $stmt = mysqli_prepare($this->con, $query);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

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