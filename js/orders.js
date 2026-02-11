let cart = [];

function increaseQty(menuId) {
    let qtyInput = document.getElementById('qty-' + menuId);
    qtyInput.value = parseInt(qtyInput.value) + 1;
}

function decreaseQty(menuId) {
    let qtyInput = document.getElementById('qty-' + menuId);

    if (parseInt(qtyInput.value) > 1) {
        qtyInput.value = parseInt(qtyInput.value) - 1;
    }
}

function addToCart(menuId, menuName, price) {
    let qty = parseInt(document.getElementById('qty-' + menuId).value);

    // Check if item already in cart
    let existingItem = cart.find(item => item.menu_id === menuId);

    if (existingItem) {
        existingItem.quantity += qty;
    } else {
        cart.push({
            menu_id: menuId,
            menu_name: menuName,
            price: price,
            quantity: qty
        });
    }

    updateCart();
}

function removeFromCart(menuId) {
    cart = cart.filter(item => item.menu_id !== menuId);
    updateCart();
}

function updateCart() {
    let cartHtml = '';
    let total = 0;

    cart.forEach(item => {
        let subtotal = item.price * item.quantity;
        total += subtotal;

        cartHtml += `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <strong>${item.menu_name}</strong><br>
                    <small>₱${item.price.toFixed(2)} x ${item.quantity}</small>
                </div>
                <div>
                    <span class="me-3">₱${subtotal.toFixed(2)}</span>
                    <button class="btn btn-sm btn-danger" onclick="removeFromCart(${item.menu_id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });

    document.getElementById('cartItems').innerHTML =
        cartHtml || '<p class="text-muted">Cart is empty</p>';

    document.getElementById('cartTotal').textContent = total.toFixed(2);
    document.getElementById('cartCount').textContent = cart.length;
    document.getElementById('cart_items_input').value = JSON.stringify(cart);

    // Enable / disable order button
    document.getElementById('placeOrderBtn').disabled = cart.length === 0;
}
