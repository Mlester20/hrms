<?php

    //connection
    $db = new Database();
    $con = $db->getConnection();
    try{
        $query = "SELECT user_id, name, address, email, password, role, phone FROM users WHERE role = 'user'";
        $result = mysqli_query($con, $query);

        if(!$result) {
            die("Query failed: " . mysqli_error($con));
        }

        // Fetch all users into an array
        $users = [];
        while($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }catch( Exception $e ){
        throw new Exception("Error fetching Users". $e->getMessage(), 500);
    }finally{
        $db->closeConnection();
    }

?>