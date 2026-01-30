<?php
require '../components/connection.php';

    try {
        $query = "SELECT description_id, description_name FROM description ORDER BY description_id DESC";
        $stmt = $con->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
    } catch (Exception $e) {
        die("Error fetching description: " . $e->getMessage());
    }finally{
        $db->closeConnection();
    }
?>