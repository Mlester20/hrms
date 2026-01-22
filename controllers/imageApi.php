<?php
include '../components/connection.php';

    //connection
    $db = new Database();
    $con = $db->getConnection();

    try{
        $query = "SELECT id, title, room_type_id, images, price FROM rooms";
        $result = $con->query($query);

        $rooms = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['images'] = json_decode($row['images']); // Decode JSON images
                $rooms[] = $row;
            }
        }
    }catch( Exception $e ){
        throw new Exception("Error calling api". $e->getMessage(), 500);
    }finally{
        $db->closeConnection();
    }

header('Content-Type: application/json');
echo json_encode($rooms);
?>