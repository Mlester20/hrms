<?php

require_once '../controllers/menusController.php';
require_once '../includes/flash.php';
require_once '../middleware/authMiddleware.php';
requireLogin();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order | <?php require_once '../components/title.php';?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="../css/clientNavbar.css">
    <link rel="stylesheet" href="../css/home.css">
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <style>
        .menu-image {
            height: 200px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
        }
        .no-image-placeholder {
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px 8px 0 0;
        }
        .card {
            transition: transform 0.2s, box-shadow 0.2s;
            border-radius: 8px;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <?php require_once '../components/header.php'; ?>

    <div class="pos-wrapper">

        <!-- LEFT SIDE -->
        <div class="pos-left">

            <h2 class="mb-4 text-center">Menu</h2>
            
            <?php showFlash(); ?>
            
            <div class="row" id="menuItems">

                <?php 
                $hasAvailable = false;
                foreach($menus as $menu): 
                    if($menu['status'] == 'available'): 
                        $hasAvailable = true;
                ?>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <?php if (!empty($menu['product_image'])): ?>
                                <img src="http://localhost/restaurant_ordering/uploads/<?= htmlspecialchars($menu['product_image']) ?>"
                                 class="card-img-top menu-image" 
                                 alt="<?= htmlspecialchars($menu['menu_name']) ?>"
                                 onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'no-image-placeholder\'><i class=\'fas fa-utensils fa-3x text-white\'></i></div>';">
                        <?php else: ?>
                            <div class="no-image-placeholder">
                                <i class="fas fa-utensils fa-3x text-white"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title">
                                <?= htmlspecialchars($menu['menu_name']) ?>
                            </h5>

                            <p class="text-muted small">
                                <i class="fas fa-tag"></i>
                                <?= ucfirst(htmlspecialchars($menu['category'])) ?>
                            </p>

                            <?php if (!empty($menu['description'])): ?>
                            <!-- <p class="text-muted small mb-2">
                                <?= htmlspecialchars(substr($menu['description'], 0, 80)) ?>
                                <?= strlen($menu['description']) > 80 ? '...' : '' ?>
                            </p> -->
                            <?php endif; ?>

                            <h6 class="text-success fw-bold mb-3">
                                ₱<?= number_format($menu['price'], 2) ?>
                            </h6>
                            
                            <div class="input-group mb-2">
                                <button class="btn btn-sm btn-outline-secondary"
                                    onclick="decreaseQty(<?= $menu['menu_id'] ?>)">
                                    -
                                </button>

                                <input type="number"
                                    class="form-control text-center"
                                    id="qty-<?= $menu['menu_id'] ?>"
                                    value="1"
                                    min="1"
                                    max="99">

                                <button class="btn btn-sm btn-outline-secondary"
                                    onclick="increaseQty(<?= $menu['menu_id'] ?>)">
                                    +
                                </button>
                            </div>
                            
                            <button class="btn btn-primary btn-sm w-100"
                                onclick="addToCart(
                                    <?= $menu['menu_id'] ?>,
                                    '<?= htmlspecialchars($menu['menu_name']) ?>',
                                    <?= $menu['price'] ?>
                                )">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>

                        </div>
                    </div>
                </div>

                <?php 
                    endif; 
                endforeach; 
                ?>

                <?php if(!$hasAvailable): ?>
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-circle"></i>
                            <strong>Menus Not Available</strong>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </div>


        <!-- RIGHT SIDE -->
        <div class="pos-right">

            <div class="card cart-sticky">

                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-cart"></i>
                        Your Cart (<span id="cartCount">0</span>)
                    </h5>
                </div>

                <div class="card-body d-flex flex-column">

                    <div id="cartItems">
                        <p class="text-muted text-center">
                            Your cart is empty
                        </p>
                    </div>

                    <hr>

                    <h4>
                        Total: ₱<span id="cartTotal">0.00</span>
                    </h4>

                    <form action="../controllers/ordersController.php"
                        method="POST"
                        id="orderForm">

                        <input type="hidden"
                            name="cart_items"
                            id="cart_items_input">
                        
                        <div class="mb-3">
                            <label class="form-label">
                                Room Number *
                            </label>
                            <input type="text"
                                name="room_number"
                                class="form-control"
                                required
                                placeholder="e.g. 301">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                Payment Method
                                <span class="text-danger">*</span>
                            </label>

                            <select name="payment_method"
                                    class="form-select"
                                    required>

                                <option value="">
                                    Select payment method
                                </option>

                                <option value="pay_on_delivery" selected>
                                    Pay Upon Delivery
                                </option>

                                <option value="card_payment" disabled>
                                    Payment via Card (Coming Soon)
                                </option>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Special Instructions
                            </label>

                            <textarea name="special_instructions"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Extra rice, no onions, etc.">
                            </textarea>
                        </div>
                        
                        <button type="submit"
                                name="create_order"
                                class="btn btn-success w-100"
                                id="placeOrderBtn"
                                disabled>
                            <i class="fas fa-check"></i>
                            Place Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script src="../js/orders.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>

</body>
</html>