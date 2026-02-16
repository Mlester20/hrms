<?php
require_once '../models/usersModel.php';
require_once '../components/connection.php';

$userModel = new usersModel();

    try{
        // Fetch all users
        $result = $userModel->getAllUsers($con);
        $users = $result['users'];
        $total_pages = $result['total_pages'];
        $page = $result['current_page'];
    }catch(Exception $e){
        throw new Exception("Error getting Users " . $e->getMessage(), 500);
    }

?>