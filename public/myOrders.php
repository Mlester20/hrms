<?php

require_once '../controllers/userOrdersController.php';
require_once '../includes/flash.php';   
require_once '../middleware/auth.php';
requireLogin();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | <?php require_once '../components/title.php'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="../css/clientNavbar.css">
    <link rel="stylesheet" href="../css/notifications.css">
    <link rel="stylesheet" href="../css/home.css">
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <style>
        .order-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .status-badge {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
        }
    </style>
</head>
<body>

    <?php require_once '../components/header.php'; ?>

    <div class="container my-5">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="text-center">
                    My Orders
                </h3>
                <p class="text-muted">Track and manage your food orders</p>
            </div>
        </div>

        <?php showFlash(); ?>

            <!-- Orders List -->
            <?php if (!empty($orders)): ?>
                <div class="row g-4">
                    <?php foreach($orders as $order): ?>
                        <?php
                        $statusColors = [
                            'pending' => 'warning',
                            'preparing' => 'info',
                            'ready' => 'success',
                            'delivered' => 'primary',
                            'cancelled' => 'danger'
                        ];
                        $statusColor = $statusColors[$order['order_status']] ?? 'secondary';
                        ?>
                        
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card shadow-sm h-100 border-0">
                                <div class="card-body d-flex flex-column">

                                    <!-- Order Number -->
                                    <h5 class="fw-bold mb-1">
                                        Order #<?= str_pad($order['order_id'], 4, '0', STR_PAD_LEFT) ?>
                                    </h5>

                                    <!-- Date -->
                                    <small class="text-muted mb-2">
                                        <i class="far fa-calendar me-1"></i>
                                        <?= date('M d, Y g:i A', strtotime($order['ordered_at'])) ?>
                                    </small>

                                    <!-- Customer -->
                                    <p class="mb-1">
                                        <i class="fas fa-user text-secondary me-2"></i>
                                        <?= htmlspecialchars($order['customer_name']) ?>
                                    </p>

                                    <!-- Room -->
                                    <p class="mb-2">
                                        <i class="fas fa-door-open text-secondary me-2"></i>
                                        Room <?= htmlspecialchars($order['room_number']) ?>
                                    </p>

                                    <!-- Status -->
                                    <span class="badge bg-<?= $statusColor ?> mb-2">
                                        <?= ucfirst($order['order_status']) ?>
                                    </span>

                                    <!-- Payment -->
                                    <span class="badge bg-<?= $order['payment_status'] === 'paid' ? 'success' : 'danger' ?> mb-3">
                                        <?= ucfirst($order['payment_status']) ?>
                                    </span>

                                    <!-- Total -->
                                    <h5 class="text-primary mb-3">
                                        ₱<?= number_format($order['total_amount'], 2) ?>
                                    </h5>

                                    <!-- Buttons -->
                                    <div class="mt-auto">
                                        <a href="order-details.php?id=<?= $order['order_id'] ?>" 
                                        class="btn btn-sm btn-outline-primary w-100 mb-2">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>

                                        <?php if ($order['order_status'] === 'pending'): ?>
                                            <button onclick="cancelOrder(<?= $order['order_id'] ?>)" 
                                                    class="btn btn-sm btn-danger w-100">
                                                <i class="fas fa-times me-1"></i>Cancel
                                            </button>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </div>
            <?php else: ?>

            <!-- Empty State -->
            <div class="row">
                <div class="col-12">
                    <div class="card text-center py-5">
                        <div class="card-body">
                            <i class="fas fa-shopping-bag fa-5x text-muted mb-3"></i>
                            <h3 class="text-muted">No Orders Yet</h3>
                            <p class="text-muted">You haven't placed any orders yet. Start ordering now!</p>
                            <a href="menu.php" class="btn btn-primary mt-3">
                                <i class="fas fa-utensils me-2"></i>Browse Menu
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
    </div>

    <!-- external js scripts -->
    <script src="../js/orders.js"></script>
    <script src="../js/notifications.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    
    <script>
        function cancelOrder(orderId) {
            Swal.fire({
                title: 'Cancel Order?',
                text: "Are you sure you want to cancel this order?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `../controllers/userOrdersController.php?action=cancel&id=${orderId}`;
                }
            });
        }
    </script>
</body>
</html>