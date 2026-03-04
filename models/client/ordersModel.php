<?php

    class BaseModel{
        protected $con;

        public function __construct($con){
            $this->con = $con;
        }
    }

    class ordersModel extends BaseModel {
    
        protected $orders = 'orders';
        protected $order_items = 'order_items';
        protected $users = 'users';
        protected $menus = 'menus';

        // Create new order
        public function createOrder($user_id, $room_number, $payment_method ,$special_instructions = null) {
            try {
                $query = "INSERT INTO {$this->orders} (user_id, room_number, total_amount, payment_method ,special_instructions, order_status, payment_status) 
                        VALUES (?, ?, 0, ?, ?, 'pending', 'unpaid')";
                $stmt = $this->con->prepare($query);
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
        public function addOrderItem($order_id, $menu_id, $quantity, $price, $notes = null) {
            try {
                $subtotal = $price * $quantity;
                
                $query = "INSERT INTO {$this->order_items} (order_id, menu_id, quantity, price, subtotal, notes) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("iiidds", $order_id, $menu_id, $quantity, $price, $subtotal, $notes);
                $stmt->execute();
                $stmt->close();
                
                return true;
            } catch(Exception $e) {
                throw new Exception("Error adding order item: " . $e->getMessage(), 500);
            }
        }
        
        // Update total amount of order
        public function updateOrderTotal($order_id) {
            try {
                $query = "UPDATE {$this->orders} 
                        SET total_amount = (SELECT SUM(subtotal) FROM {$this->order_items} WHERE order_id = ?) 
                        WHERE order_id = ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("ii", $order_id, $order_id);
                $stmt->execute();
                $stmt->close();
                
                return true;
            } catch(Exception $e) {
                throw new Exception("Error updating order total: " . $e->getMessage(), 500);
            }
        }
        
        // Get user's orders
        public function getUserOrders($user_id) {
            try {
                $query = "SELECT o.*, u.name as customer_name 
                        FROM {$this->orders} o 
                        JOIN {$this->users} u ON o.user_id = u.user_id 
                        WHERE o.user_id = ? 
                        ORDER BY o.ordered_at DESC";
                $stmt = $this->con->prepare($query);
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
        public function getOrderItems($order_id) {
            try {
                $query = "SELECT oi.*, m.menu_name, m.category 
                        FROM {$this->order_items} oi 
                        JOIN {$this->menus} m ON oi.menu_id = m.menu_id 
                        WHERE oi.order_id = ?";
                $stmt = $this->con->prepare($query);
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
        public function getOrderById($order_id) {
            try {
                $query = "SELECT o.*, u.name as customer_name, u.email 
                        FROM {$this->orders} o 
                        JOIN {$this->users} u ON o.user_id = u.user_id 
                        WHERE o.order_id = ?";
                $stmt = $this->con->prepare($query);
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
        public function cancelOrders($order_id, $user_id)
        {
            $stmt = $this->con->prepare("UPDATE {$this->orders} 
                                SET order_status = 'cancelled' 
                                WHERE order_id = ? AND user_id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->con->error);
            }

            $stmt->bind_param("ii", $order_id, $user_id);

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $stmt->close();
        }        
    }

?>