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

        if ($_GET['action'] === 'view' && isset($_GET['id'])) {
            header('Content-Type: application/json');

            $order_id = (int) $_GET['id'];
            $user_id  = $_SESSION['user_id'];

            try {
                $order = $userOrders->getOrderById($con, $order_id);

                if (!$order || (int)$order['user_id'] !== (int)$user_id) {
                    http_response_code(403);
                    echo json_encode(['error' => 'Unauthorized']);
                    exit();
                }

                $items = $userOrders->getOrderItems($con, $order_id);

                echo json_encode([
                    'order' => $order,
                    'items' => $items,
                ]);
                exit();

            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
                exit();
            }
        }

        if ($_GET['action'] === 'cancel' && isset($_GET['id'])) {
            $order_id = $_GET['id'];
            $user_id  = $_SESSION['user_id'];

            try {
                $userOrders->cancelOrders($con, $order_id, $user_id);
                setFlash("success", "Order Cancelled Successfully!");
                header("Location: ../views/myOrders.php");
                exit();

            } catch (Exception $e) {
                die("Error cancelling order: " . $e->getMessage());
            }
        }
    }