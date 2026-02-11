<?php

require_once '../middleware/auth.php';
require_once '../includes/flash.php';
requireLogin();

// Get order_id from URL
if(!isset($_GET['order_id'])) {
    header("Location: order.php");
    exit();
}

$order_id = $_GET['order_id'];

// Fetch order details
require_once '../components/connection.php';
require_once '../models/client/ordersModel.php';

$ordersModel = new ordersModel();

try {
    $order = $ordersModel->getOrderById($con, $order_id);
    $items = $ordersModel->getOrderItems($con, $order_id);
    
    // Security check - make sure this order belongs to logged in user
    if($order['user_id'] != $_SESSION['user_id']) {
        $_SESSION['error'] = "Unauthorized access to order.";
        header("Location: order.php");
        exit();
    }
    
} catch(Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: order.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation | <?php require_once '../components/title.php';?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="../css/clientNavbar.css">
    <style>
        .order-success {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 2rem;
        }
        .order-card {
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }
        .print-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            font-size: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        @media print {
            .no-print { display: none; }
            .print-btn { display: none; }
        }
    </style>
</head>
<body>

    <?php require_once '../components/header.php'; ?>

    <div class="container mt-5 mb-5">
        
        <!-- Success Banner -->
        <div class="order-success">
            <i class="fas fa-check-circle fa-4x mb-3"></i>
            <h1>Order Placed Successfully!</h1>
            <p class="mb-0">Your order #<?= $order_id ?> has been received and is being prepared.</p>
        </div>

        <?php showFlash(); ?>

        <!-- Order Details Card -->
        <div class="card order-card mb-4">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="mb-0"><i class="fas fa-receipt"></i> Order #<?= $order_id ?></h4>
                        <small class="text-muted">
                            <i class="far fa-clock"></i> 
                            <?= date('F j, Y - g:i A', strtotime($order['ordered_at'])) ?>
                        </small>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="status-badge bg-warning text-dark">
                            <i class="fas fa-clock"></i> <?= ucfirst($order['order_status']) ?>
                        </span>
                        <span class="status-badge bg-danger text-white ms-2">
                            <i class="fas fa-wallet"></i> <?= ucfirst($order['payment_status']) ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Customer & Delivery Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6><i class="fas fa-user"></i> Customer Information</h6>
                        <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                        <p class="mb-0"><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-map-marker-alt"></i> Delivery Location</h6>
                        <p class="mb-0">
                            <strong>Room Number:</strong> 
                            <span class="badge bg-primary" style="font-size: 1rem;">
                                <?= htmlspecialchars($order['room_number']) ?>
                            </span>
                        </p>
                    </div>
                </div>

                <?php if($order['special_instructions']): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Special Instructions:</strong> 
                    <?= nl2br(htmlspecialchars($order['special_instructions'])) ?>
                </div>
                <?php endif; ?>

                <hr>

                <!-- Order Items -->
                <h5 class="mb-3"><i class="fas fa-utensils"></i> Order Items</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>Category</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($items as $item): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($item['menu_name']) ?></strong>
                                    <?php if($item['notes']): ?>
                                        <br><small class="text-muted">
                                            <i class="fas fa-sticky-note"></i> 
                                            <?= htmlspecialchars($item['notes']) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= htmlspecialchars($item['category']) ?>
                                    </span>
                                </td>
                                <td class="text-center"><?= $item['quantity'] ?></td>
                                <td class="text-end">₱<?= number_format($item['price'], 2) ?></td>
                                <td class="text-end">
                                    <strong>₱<?= number_format($item['subtotal'], 2) ?></strong>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="4" class="text-end"><h5 class="mb-0">Total Amount:</h5></td>
                                <td class="text-end">
                                    <h4 class="text-success mb-0">
                                        ₱<?= number_format($order['total_amount'], 2) ?>
                                    </h4>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-4 no-print">
                    <div class="col-md-6">
                        <a href="order.php" class="btn btn-primary w-100">
                            <i class="fas fa-plus"></i> Place Another Order
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="my-orders.php" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-history"></i> View Order History
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Print Button -->
        <button onclick="window.print()" class="btn btn-success print-btn no-print" title="Print Receipt">
            <i class="fas fa-print"></i>
        </button>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>