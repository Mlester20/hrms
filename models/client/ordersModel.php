<?php

class ordersModel {
    
    // Create new order
    public function createOrder($con, $user_id, $room_number, $payment_method ,$special_instructions = null) {
        try {
            $query = "INSERT INTO orders (user_id, room_number, total_amount, payment_method ,special_instructions, order_status, payment_status) 
                      VALUES (?, ?, 0, ?, ?, 'pending', 'unpaid')";
            $stmt = $con->prepare($query);
            $stmt->bind_param("isss", $user_id, $room_number, $payment_method ,$special_instructions);
            $stmt->execute();
            
            $order_id = $stmt->insert_id;
            $stmt->close();
            
            return $order_id;
        } catch(Exception $e) {
            throw new Exception("Error creating order: " . $e->getMessage(), 500);
        }
    }
    
    // Add item to order
    public function addOrderItem($con, $order_id, $menu_id, $quantity, $price, $notes = null) {
        try {
            $subtotal = $price * $quantity;
            
            $query = "INSERT INTO order_items (order_id, menu_id, quantity, price, subtotal, notes) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $con->prepare($query);
            $stmt->bind_param("iiidds", $order_id, $menu_id, $quantity, $price, $subtotal, $notes);
            $stmt->execute();
            $stmt->close();
            
            return true;
        } catch(Exception $e) {
            throw new Exception("Error adding order item: " . $e->getMessage(), 500);
        }
    }
    
    // Update total amount of order
    public function updateOrderTotal($con, $order_id) {
        try {
            $query = "UPDATE orders 
                      SET total_amount = (SELECT SUM(subtotal) FROM order_items WHERE order_id = ?) 
                      WHERE order_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ii", $order_id, $order_id);
            $stmt->execute();
            $stmt->close();
            
            return true;
        } catch(Exception $e) {
            throw new Exception("Error updating order total: " . $e->getMessage(), 500);
        }
    }
    
    // Get user's orders
    public function getUserOrders($con, $user_id) {
        try {
            $query = "SELECT o.*, u.name as customer_name 
                      FROM orders o 
                      JOIN users u ON o.user_id = u.user_id 
                      WHERE o.user_id = ? 
                      ORDER BY o.ordered_at DESC";
            $stmt = $con->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $orders = [];
            while($row = mysqli_fetch_assoc($result)) {
                $orders[] = $row;
            }
            $stmt->close();
            
            return $orders;
        } catch(Exception $e) {
            throw new Exception("Error getting user orders: " . $e->getMessage(), 500);
        }
    }
    
    // Get order items by order_id
    public function getOrderItems($con, $order_id) {
        try {
            $query = "SELECT oi.*, m.menu_name, m.category 
                      FROM order_items oi 
                      JOIN menus m ON oi.menu_id = m.menu_id 
                      WHERE oi.order_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $items = [];
            while($row = mysqli_fetch_assoc($result)) {
                $items[] = $row;
            }
            $stmt->close();
            
            return $items;
        } catch(Exception $e) {
            throw new Exception("Error getting order items: " . $e->getMessage(), 500);
        }
    }
    
    // Get single order details
    public function getOrderById($con, $order_id) {
        try {
            $query = "SELECT o.*, u.name as customer_name, u.email 
                      FROM orders o 
                      JOIN users u ON o.user_id = u.user_id 
                      WHERE o.order_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $order = mysqli_fetch_assoc($result);
            $stmt->close();
            
            return $order;
        } catch(Exception $e) {
            throw new Exception("Error getting order: " . $e->getMessage(), 500);
        }
    }

    //cancell order function
    public function cancelOrders($con, $order_id, $user_id)
    {
        $stmt = $con->prepare("UPDATE orders 
                            SET order_status = 'cancelled' 
                            WHERE order_id = ? AND user_id = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $con->error);
        }

        $stmt->bind_param("ii", $order_id, $user_id);

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $stmt->close();
    }


    
}

?>