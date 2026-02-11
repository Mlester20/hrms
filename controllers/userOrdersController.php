<?php
session_start();

require_once '../models/client/ordersModel.php';
require_once '../components/connection.php';
require_once '../includes/flash.php';

$userOrders = new ordersModel();

    // Default: display orders
    try {
        $user_id = $_SESSION['user_id'];
        $orders = $userOrders->getUserOrders($con, $user_id);
    } catch(Exception $e) {
        throw new Exception("Error getting Orders ". $e->getMessage(), 500);
    }

    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'cancel' && isset($_GET['id'])) {
            $order_id = $_GET['id'];
            $user_id = $_SESSION['user_id'];

            try {
                $userOrders->cancelOrders($con, $order_id, $user_id);
                setFlash("success", "Order Cancelled Successfully!");
                header("Location: ../public/myOrders.php");
                exit();

            } catch (Exception $e) {
                die("Error cancelling order: " . $e->getMessage());
            }
        }
    }