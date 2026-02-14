<?php
session_start();

require_once '../components/connection.php';
require_once '../models/client/ordersModel.php';
require_once '../includes/flash.php';

/* =========================
    CREATE ORDER
========================= */
if(isset($_POST['create_order'])) {
    // Clear any output buffers
    ob_start();
    
    $ordersModel = new ordersModel();
    
    $user_id = $_SESSION['user_id'];
    $room_number = trim($_POST['room_number']);
    $payment_method = $_POST['payment_method'];
    $special_instructions = !empty($_POST['special_instructions']) ? trim($_POST['special_instructions']) : null;
    $cart_items = json_decode($_POST['cart_items'], true);
    
    // Validate cart
    if(empty($cart_items)) {
        setFlash("error", "Cart is empty!");
        header("Location: ../views/order.php");
        exit();
    }
    
    // Validate payment method
    if(empty($payment_method)) {
        setFlash("error", "Please select a payment method!");
        header("Location: ../views/order.php");
        exit();
    }
    
    try {
        // Start transaction
        $con->begin_transaction();
        
        // Create order - ✅ Added payment_method parameter
        $order_id = $ordersModel->createOrder($con, $user_id, $room_number, $payment_method, $special_instructions);
        
        // Add each item to order
        foreach($cart_items as $item) {
            $ordersModel->addOrderItem(
                $con, 
                $order_id, 
                $item['menu_id'], 
                $item['quantity'], 
                $item['price'],
                isset($item['notes']) ? $item['notes'] : null
            );
        }

        $ordersModel->updateOrderTotal($con, $order_id);
        
        $con->commit();
        
        setFlash("success", "Order placed successfully!");
        
        ob_end_clean();
        header("location: ../views/order.php"); // ✅ Redirect to orders page
        exit();
        
    } catch(Exception $e) {
        $con->rollback();
        setFlash("error", $e->getMessage());
        
        ob_end_clean();
        header("Location: ../views/order.php");
        exit();
    }
}

/* =========================
    GET USER ORDERS
========================= */
if(isset($_GET['get_user_orders'])) {
    $ordersModel = new ordersModel();
    $user_id = $_SESSION['user_id'];
    
    try {
        $orders = $ordersModel->getUserOrders($con, $user_id);
        echo json_encode(['success' => true, 'orders' => $orders]);
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}


/* =========================
    GET ORDER DETAILS
========================= */
if(isset($_GET['order_id']) && !isset($_POST['create_order'])) {
    $ordersModel = new ordersModel();
    $order_id = $_GET['order_id'];
    
    try {
        $order = $ordersModel->getOrderById($con, $order_id);
        $items = $ordersModel->getOrderItems($con, $order_id);
        
        echo json_encode([
            'success' => true, 
            'order' => $order, 
            'items' => $items
        ]);
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}