<?php

    class BaseModel{
        protected $con;

        public function __construct($con){
            $this->con = $con;
        }
    }

    class DescriptionModel extends BaseModel {
        protected $description = 'description';
        public function getDescriptions(){
            try{
                $query = "SELECT description_id, description_name FROM {$this->description}";
                $result = mysqli_query($this->con, $query);

                $descriptions = [];

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $descriptions[] = $row;
                    }
                }
                return $descriptions;
            }catch(Exception $e){
                throw new Exception("Error getting Descriptions ". $e->getMessage(), 500);
            }
        }
    }

?>