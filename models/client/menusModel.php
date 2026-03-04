<?php

    class BaseModel{
        protected $con;

        public function __construct($con){
            $this->con = $con;
        }
    }

    class menusModel extends BaseModel{
        protected $menus = 'menus';
        public function getMenus(){
            try{
                $query = "SELECT * FROM {$this->menus} WHERE status = 'available' ORDER BY menu_id DESC";
                $stmt = $this->con->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();

                $menus = [];
                while($row = mysqli_fetch_assoc($result)){
                    $menus[] = $row;
                }
                $stmt->close();
                return $menus;
            }catch(Exception $e){
                throw new Exception("Error getting all menus ". $e->getMessage(), 500);
            }
        }
    }

?>