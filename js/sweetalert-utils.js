/**
 * ============================================
 * SWEET ALERT UTILITY FUNCTIONS
 * ============================================
 * Centralized Sweet Alert functions para sa buong system
 * 
 * Usage:
 * 1. Include sa page: <script src="../js/sweetalert-utils.js"></script>
 * 2. Tawagin ang showSessionAlerts() sa bottom ng page
 * 3. Use confirmDelete() para sa delete confirmations
 */

// ===== AUTO-SHOW ALERTS BASED ON PHP SESSION =====
function showSessionAlerts() {
    // Check kung may success message from PHP
    const successMessage = document.getElementById('success-message');
    if (successMessage) {
        Swal.fire({
            title: 'Success!',
            text: successMessage.value,
            icon: 'success',
            confirmButtonColor: '#28a745',
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true
        });
    }

    // Check kung may error message from PHP
    const errorMessage = document.getElementById('error-message');
    if (errorMessage) {
        Swal.fire({
            title: 'Error!',
            text: errorMessage.value,
            icon: 'error',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'OK'
        });
    }
}

// ===== DELETE CONFIRMATION WITH CUSTOM MESSAGES =====
/**
 * Generic delete confirmation
 * @param {number} id - ID ng item na ide-delete
 * @param {string} formId - ID ng form na isu-submit
 * @param {string} itemName - Name ng item (e.g., "room type", "user", "booking")
 */
function confirmDelete(id, formId = 'deleteForm', itemName = 'this item') {
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete ${itemName}. This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Set ID sa hidden form
            document.getElementById('deleteId').value = id;
            // Submit ang form
            document.getElementById(formId).submit();
        }
    });
}

// ===== SUCCESS ALERT (Manually callable) =====
function showSuccess(message, title = 'Success!', autoClose = true) {
    const config = {
        title: title,
        text: message,
        icon: 'success',
        confirmButtonColor: '#28a745',
        confirmButtonText: 'OK'
    };

    if (autoClose) {
        config.timer = 3000;
        config.timerProgressBar = true;
    }

    Swal.fire(config);
}

// ===== ERROR ALERT (Manually callable) =====
function showError(message, title = 'Error!') {
    Swal.fire({
        title: title,
        text: message,
        icon: 'error',
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'OK'
    });
}

// ===== WARNING ALERT (Manually callable) =====
function showWarning(message, title = 'Warning!') {
    Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        confirmButtonColor: '#ffc107',
        confirmButtonText: 'OK'
    });
}

// ===== INFO ALERT (Manually callable) =====
function showInfo(message, title = 'Information') {
    Swal.fire({
        title: title,
        text: message,
        icon: 'info',
        confirmButtonColor: '#17a2b8',
        confirmButtonText: 'OK'
    });
}

// ===== CONFIRMATION DIALOG (Generic) =====
/**
 * Generic confirmation dialog
 * @param {string} title - Title ng confirmation
 * @param {string} text - Message
 * @param {function} onConfirm - Function to execute kung nag-confirm
 * @param {function} onCancel - Function to execute kung nag-cancel (optional)
 */
function confirmAction(title, text, onConfirm, onCancel = null) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed && typeof onConfirm === 'function') {
            onConfirm();
        } else if (result.isDismissed && typeof onCancel === 'function') {
            onCancel();
        }
    });
}

// ===== LOADING ALERT (Para sa AJAX requests) =====
function showLoading(message = 'Please wait...') {
    Swal.fire({
        title: message,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

// ===== CLOSE LOADING =====
function closeLoading() {
    Swal.close();
}

// ===== TOAST NOTIFICATION (Small notification sa corner) =====
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

function showToast(message, icon = 'success') {
    Toast.fire({
        icon: icon,
        title: message
    });
}