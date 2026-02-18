// ── Status helpers ──────────────────────────────────────────────────
const statusColorMap = {
    pending: 'warning',
    preparing: 'info',
    ready: 'success',
    delivered: 'primary',
    cancelled: 'danger'
};

function badgeHtml(text, colorMap) {
    const key = text.toLowerCase();
    const color = colorMap[key] ?? 'secondary';
    return `<span class="badge bg-${color}">${capitalize(text)}</span>`;
}

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function formatPHP(amount) {
    return '₱' + parseFloat(amount).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}


// ── View Modal ──────────────────────────────────────────────────────
const orderModal = new bootstrap.Modal(
    document.getElementById('orderModal')
);

const spinner  = document.getElementById('modalSpinner');
const content  = document.getElementById('modalContent');
const errorDiv = document.getElementById('modalError');
const errorMsg = document.getElementById('modalErrorMsg');

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
        const res = await fetch(
            `../controllers/userOrdersController.php?action=view&id=${orderId}`
        );

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
                year: 'numeric',
                month: 'short',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });

        document.getElementById('md-room').textContent =
            'Room ' + order.room_number;

        document.getElementById('md-payment-method').textContent =
            capitalize(order.payment_method ?? 'N/A');

        // Status badges
        document.getElementById('md-order-status').outerHTML =
            `<span id="md-order-status" class="badge ms-1 bg-${
                statusColorMap[order.order_status] ?? 'secondary'
            }">${capitalize(order.order_status)}</span>`;

        document.getElementById('md-payment-status').outerHTML =
            `<span id="md-payment-status" class="badge ms-1 bg-${
                order.payment_status === 'paid' ? 'success' : 'danger'
            }">${capitalize(order.payment_status)}</span>`;

        // Special instructions
        const specialWrap = document.getElementById('md-special-wrap');

        if (order.special_instructions) {
            specialWrap.classList.remove('d-none');
            document.getElementById('md-special').textContent =
                order.special_instructions;
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
                    ${
                        item.notes
                            ? `<small class="text-muted">${escHtml(item.notes)}</small>`
                            : ''
                    }
                </td>
                <td class="text-center">${item.quantity}</td>
                <td class="text-end">${formatPHP(item.price)}</td>
                <td class="text-end">${formatPHP(item.subtotal)}</td>
            `;

            tbody.appendChild(tr);
        });

        document.getElementById('md-total').textContent =
            formatPHP(order.total_amount);

        showContent();

    } catch (err) {
        console.error(err);
        showError('An unexpected error occurred.');
    }
}


// ── XSS Helper ──────────────────────────────────────────────────────
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
    }).then(result => {
        if (result.isConfirmed) {
            window.location.href =
                `../controllers/userOrdersController.php?action=cancel&id=${orderId}`;
        }
    });
}
