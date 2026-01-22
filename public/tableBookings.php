<?php
session_start();
include '../components/connection.php';
include '../controllers/fetchTableReservations.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$bookings = getTableBookings($con, $user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Bookings - <?php include '../components/title.php'; ?> </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="../css/clientNavbar.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/tableBooking.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <style>
        .booking-card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .booking-card:hover {
            transform: translateY(-5px);
        }
        .card-header-custom {
            background-color: #f8f9fa;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .table-icon {
            color: #6c757d;
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
        .booking-detail {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        .status-badge {
            font-weight: 500;
            padding: 0.5rem 0.75rem;
            border-radius: 50px;
        }
        .special-requests {
            background-color: rgba(0,0,0,0.03);
            border-radius: 8px;
            padding: 12px;
            margin-top: 15px;
        }
        .btn-cancel {
            border-radius: 50px;
            padding: 8px 16px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4 mt-4 text-center card-title text-muted">My Table Bookings</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <?php if (!empty($bookings)): ?>
                <?php foreach ($bookings as $booking): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="booking-card card h-100">
                            <!-- Card Header with Table Number and Status -->
                            <div class="card-header-custom py-3 px-4 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold">
                                    <i class="fas fa-utensils me-2"></i>
                                    Table #<?php echo htmlspecialchars($booking['table_number']); ?>
                                </h5>
                                <span class="status-badge badge <?php echo getStatusBadgeClass($booking['status']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($booking['status'])); ?>
                                </span>
                            </div>
                            
                            <!-- Card Body with Booking Details -->
                            <div class="card-body p-4">
                                <div class="booking-details">
                                    <div class="booking-detail">
                                        <span class="table-icon"><i class="fas fa-calendar"></i></span>
                                        <span class="fw-medium"><?php echo date('F j, Y', strtotime($booking['reservation_date'])); ?></span>
                                    </div>
                                    
                                    <div class="booking-detail">
                                        <span class="table-icon"><i class="fas fa-clock"></i></span>
                                        <span><?php echo date('g:i A', strtotime($booking['time_slot'])); ?></span>
                                    </div>
                                    
                                    <div class="booking-detail">
                                        <span class="table-icon"><i class="fas fa-users"></i></span>
                                        <span><?php echo htmlspecialchars($booking['guest_count']); ?> Guests</span>
                                    </div>
                                    
                                    <div class="booking-detail">
                                        <span class="table-icon"><i class="fas fa-map-marker-alt"></i></span>
                                        <span><?php echo htmlspecialchars($booking['location']); ?></span>
                                    </div>
                                </div>

                                <?php if ($booking['special_requests']): ?>
                                    <div class="special-requests">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-comment-alt me-2 text-secondary"></i>
                                            <h6 class="mb-0 fw-bold text-secondary">Special Requests:</h6>
                                        </div>
                                        <p class="mb-0 text-secondary"><?php echo htmlspecialchars($booking['special_requests']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($booking['status'] === 'pending'): ?>
                                <div class="card-footer bg-transparent border-0 px-4 pb-4 pt-0 text-end">
                                    <button class="btn btn-outline-danger btn-cancel cancel-booking" 
                                            data-id="<?php echo $booking['reservation_id']; ?>">
                                        <i class="fas fa-times me-1"></i> Cancel Booking
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="fas fa-info-circle fs-4 me-3"></i>
                        <div>
                            You haven't made any table reservations yet.
                            <a href="restaurantTableBooking.php" class="alert-link d-inline-block mt-2">Book a table now</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="../js/fetchClientNotifications.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners for cancel buttons
            const cancelButtons = document.querySelectorAll('.cancel-booking');
            
            cancelButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const bookingId = this.dataset.id;
                    const bookingCard = this.closest('.booking-card');
                    const tableNumber = bookingCard.querySelector('h5').textContent.trim();
                    
                    // Show confirmation dialog
                    if (confirm(`Are you sure you want to cancel ${tableNumber}?`)) {
                        // Disable button to prevent double clicks
                        this.disabled = true;
                        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Cancelling...';
                        
                        // Create form data
                        const formData = new FormData();
                        formData.append('reservation_id', bookingId);
                        
                        // Make AJAX request
                        fetch('../controllers/cancelReservations.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Show success message
                                showAlert('success', data.message);
                                
                                // Update the card UI
                                updateBookingCardStatus(bookingCard, 'cancelled');
                                
                                // Remove the cancel button since it's no longer needed
                                const cardFooter = this.closest('.card-footer');
                                if (cardFooter) {
                                    cardFooter.remove();
                                }
                            } else {
                                // Show error message
                                showAlert('error', data.message);
                                
                                // Re-enable button
                                this.disabled = false;
                                this.innerHTML = '<i class="fas fa-times me-1"></i> Cancel Booking';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showAlert('error', 'An error occurred while cancelling the reservation');
                            
                            // Re-enable button
                            this.disabled = false;
                            this.innerHTML = '<i class="fas fa-times me-1"></i> Cancel Booking';
                        });
                    }
                });
            });
        });

        // Function to show alert messages
        function showAlert(type, message) {
            // Remove existing alerts
            const existingAlert = document.querySelector('.alert-custom');
            if (existingAlert) {
                existingAlert.remove();
            }
            
            // Create new alert
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-custom alert-dismissible fade show`;
            alertDiv.style.position = 'fixed';
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';
            alertDiv.style.maxWidth = '400px';
            
            alertDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Function to update booking card status
        function updateBookingCardStatus(card, newStatus) {
            const statusBadge = card.querySelector('.status-badge');
            if (statusBadge) {
                // Remove existing status classes
                statusBadge.className = statusBadge.className.replace(/bg-\w+/g, '');
                statusBadge.className = statusBadge.className.replace(/text-\w+/g, '');
                
                // Add new status class
                statusBadge.classList.add('badge', 'status-badge', 'bg-danger');
                statusBadge.textContent = 'Cancelled';
            }
            
            // Add visual indication that booking is cancelled
            card.style.opacity = '0.7';
            card.style.borderLeft = '4px solid #dc3545';
        }
    </script>
</body>
</html>