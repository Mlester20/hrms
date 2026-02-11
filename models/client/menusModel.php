<?php

    class menusModel{
        public function getMenus($con){
            try{
                $query = "SELECT * FROM menus WHERE status = 'available' ORDER BY menu_id DESC";
                $stmt = $con->prepare($query);
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