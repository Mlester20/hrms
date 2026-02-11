<?php
require_once '../models/usersModel.php';
require_once '../components/connection.php';

$userModel = new usersModel();

// Fetch all users
$result = $userModel->getAllUsers($con);
$users = $result['users'];
$total_pages = $result['total_pages'];
$page = $result['current_page'];

?>