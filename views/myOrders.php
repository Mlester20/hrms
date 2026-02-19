<?php

require_once '../controllers/userOrdersController.php';
require_once '../includes/flash.php';   
require_once '../middleware/authMiddleware.php';
requireLogin();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | <?php require_once '../includes/title.php'; ?></title>
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

        /* ── Modal tweaks ─────────────────────────── */
        #orderModal .modal-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: #fff;
        }
        #orderModal .modal-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        #orderModal .info-row {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: .35rem;
            font-size: .9rem;
        }
        #orderModal .info-row i {
            width: 16px;
            text-align: center;
            color: #6c757d;
        }
        #orderModal .items-table th {
            background-color: #f8f9fa;
            font-size: .82rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        #orderModal .items-table td {
            vertical-align: middle;
            font-size: .88rem;
        }
        #orderModal .total-row td {
            font-weight: 700;
            font-size: .95rem;
            border-top: 2px solid #dee2e6;
        }
        #orderModal .special-note {
            background: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: .5rem .75rem;
            border-radius: 0 .25rem .25rem 0;
            font-size: .85rem;
        }
        /* Spinner overlay */
        #modalSpinner {
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>

    <?php require_once '../components/header.php'; ?>

    <div class="container my-5">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="text-center">My Orders</h3>
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
                        'pending'   => 'warning',
                        'preparing' => 'info',
                        'ready'     => 'success',
                        'delivered' => 'primary',
                        'cancelled' => 'danger'
                    ];
                    $statusColor = $statusColors[$order['order_status']] ?? 'secondary';
                    ?>
                    
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="card shadow-sm h-100 border-0 order-card">
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
                                    <!-- View button now opens modal -->
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary w-100 mb-2"
                                            onclick="viewOrder(<?= $order['order_id'] ?>)">
                                        <i class="fas fa-eye me-1"></i>View
                                    </button>

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


    <!-- ═══════════════════════════════════════════════════════
         ORDER DETAILS MODAL
    ════════════════════════════════════════════════════════ -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="orderModalLabel">
                        <i class="fas fa-receipt me-2"></i>Order Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-0">

                    <!-- Spinner (shown while loading) -->
                    <div id="modalSpinner">
                        <div class="text-center text-muted">
                            <div class="spinner-border text-primary mb-2" role="status"></div>
                            <p class="mb-0">Loading order details…</p>
                        </div>
                    </div>

                    <!-- Content (hidden until data loads) -->
                    <div id="modalContent" class="d-none p-4">

                        <!-- ── Order Meta ── -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-uppercase text-muted mb-2" style="font-size:.75rem; letter-spacing:.06em;">Order Info</h6>
                                <div class="info-row"><i class="fas fa-hashtag"></i><span id="md-order-id"></span></div>
                                <div class="info-row"><i class="far fa-calendar"></i><span id="md-date"></span></div>
                                <div class="info-row"><i class="fas fa-door-open"></i><span id="md-room"></span></div>
                                <div class="info-row"><i class="fas fa-credit-card"></i><span id="md-payment-method"></span></div>
                            </div>
                            <div class="col-md-6 mt-3 mt-md-0">
                                <h6 class="fw-bold text-uppercase text-muted mb-2" style="font-size:.75rem; letter-spacing:.06em;">Status</h6>
                                <div class="info-row">
                                    <i class="fas fa-circle-notch"></i>
                                    <span>Order: </span>
                                    <span id="md-order-status" class="badge ms-1"></span>
                                </div>
                                <div class="info-row">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span>Payment: </span>
                                    <span id="md-payment-status" class="badge ms-1"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Special Instructions -->
                        <div id="md-special-wrap" class="mb-3 d-none">
                            <div class="special-note">
                                <i class="fas fa-sticky-note me-1 text-warning"></i>
                                <strong>Special Instructions:</strong>
                                <span id="md-special"></span>
                            </div>
                        </div>

                        <hr class="my-3">

                        <!-- ── Items Table ── -->
                        <h6 class="fw-bold text-uppercase text-muted mb-2" style="font-size:.75rem; letter-spacing:.06em;">Items Ordered</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered items-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="md-items-body"></tbody>
                                <tfoot>
                                    <tr class="total-row">
                                        <td colspan="3" class="text-end">Total</td>
                                        <td class="text-end text-primary" id="md-total"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div><!-- /modalContent -->

                    <!-- Error state -->
                    <div id="modalError" class="d-none p-4 text-center text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p id="modalErrorMsg" class="mb-0">Failed to load order details.</p>
                    </div>

                </div><!-- /modal-body -->

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                </div>

            </div>
        </div>
    </div>
    <!-- ═══════════════════════════════════════════════════════ -->


    <!-- External JS -->
    <script src="../js/orders.js"></script>
    <script src="../js/notifications.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    
    <!-- Internal Js -->
    <script>
        // ── Status helpers ──────────────────────────────────────────────────
        const statusColorMap = {
            pending:   'warning',
            preparing: 'info',
            ready:     'success',
            delivered: 'primary',
            cancelled: 'danger'
        };

        function badgeHtml(text, colorMap) {
            const key   = text.toLowerCase();
            const color = colorMap[key] ?? 'secondary';
            return `<span class="badge bg-${color}">${capitalize(text)}</span>`;
        }

        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function formatPHP(amount) {
            return '₱' + parseFloat(amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }


        // ── View Modal ──────────────────────────────────────────────────────
        const orderModal  = new bootstrap.Modal(document.getElementById('orderModal'));
        const spinner     = document.getElementById('modalSpinner');
        const content     = document.getElementById('modalContent');
        const errorDiv    = document.getElementById('modalError');
        const errorMsg    = document.getElementById('modalErrorMsg');

        function showSpinner() {
            spinner.classList.remove('d-none');
            content.classList.add('d-none');
            errorDiv.classList.add('d-none');
        }

        function showContent() {
            spinner.classList.add('d-none');
            content.classList.remove('d-none');
            errorDiv.classList.add('d-none');
        }

        function showError(msg) {
            spinner.classList.add('d-none');
            content.classList.add('d-none');
            errorDiv.classList.remove('d-none');
            errorMsg.textContent = msg || 'Failed to load order details.';
        }

        async function viewOrder(orderId) {
            showSpinner();
            orderModal.show();

            try {
                const res  = await fetch(`../controllers/userOrdersController.php?action=view&id=${orderId}`);
                const data = await res.json();

                if (!res.ok || data.error) {
                    showError(data.error ?? 'Server error.');
                    return;
                }

                const { order, items } = data;

                // Populate meta
                document.getElementById('md-order-id').textContent =
                    'Order #' + String(order.order_id).padStart(4, '0');

                document.getElementById('md-date').textContent =
                    new Date(order.ordered_at).toLocaleString('en-PH', {
                        year: 'numeric', month: 'short', day: '2-digit',
                        hour: '2-digit', minute: '2-digit', hour12: true
                    });

                document.getElementById('md-room').textContent = 'Room ' + order.room_number;
                document.getElementById('md-payment-method').textContent =
                    capitalize(order.payment_method ?? 'N/A');

                // Status badges
                document.getElementById('md-order-status').outerHTML =
                    `<span id="md-order-status" class="badge ms-1 bg-${statusColorMap[order.order_status] ?? 'secondary'}">${capitalize(order.order_status)}</span>`;

                document.getElementById('md-payment-status').outerHTML =
                    `<span id="md-payment-status" class="badge ms-1 bg-${order.payment_status === 'paid' ? 'success' : 'danger'}">${capitalize(order.payment_status)}</span>`;

                // Special instructions
                const specialWrap = document.getElementById('md-special-wrap');
                if (order.special_instructions) {
                    specialWrap.classList.remove('d-none');
                    document.getElementById('md-special').textContent = order.special_instructions;
                } else {
                    specialWrap.classList.add('d-none');
                }

                // Items table
                const tbody = document.getElementById('md-items-body');
                tbody.innerHTML = '';

                items.forEach(item => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>
                            <div class="fw-semibold">${escHtml(item.menu_name)}</div>
                            ${item.notes ? `<small class="text-muted">${escHtml(item.notes)}</small>` : ''}
                        </td>
                        <td class="text-center">${item.quantity}</td>
                        <td class="text-end">${formatPHP(item.price)}</td>
                        <td class="text-end">${formatPHP(item.subtotal)}</td>
                    `;
                    tbody.appendChild(tr);
                });

                document.getElementById('md-total').textContent = formatPHP(order.total_amount);

                showContent();

            } catch (err) {
                console.error(err);
                showError('An unexpected error occurred.');
            }
        }

        // XSS helper
        function escHtml(str) {
            const d = document.createElement('div');
            d.textContent = str;
            return d.innerHTML;
        }


        // ── Cancel Order ────────────────────────────────────────────────────
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