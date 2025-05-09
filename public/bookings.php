<?php
session_start();
include '../components/config.php';
include '../controllers/fetchBookings.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - <?php include '../components/title.php'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="../css/clientNavbar.css">
    <link rel="stylesheet" href="../css/home.css">
</head>
<body>
    <?php include '../components/header.php'; ?>
    
    <div class="container my-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="text-center mb-4">My Bookings</h1>
                <p class="text-center text-muted">View and manage all your reservations</p>
            </div>
        </div>
        
        <?php if (isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="row">
                <?php while ($booking = mysqli_fetch_assoc($result)): ?>
                    <div class="col-lg-6 mb-4">
                        <div class="card booking-card h-100">
                            <div class="booking-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Booking #<?php echo $booking['booking_id']; ?></h5>
                                <span class="badge <?php echo getStatusBadgeClass($booking['booking_status']); ?>"><?php echo $booking['booking_status']; ?></span>
                            </div>
                            <div class="booking-details">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-hotel me-2"></i>Room</h6>
                                        <p class="text-muted"><?php echo $booking['room_title']; ?> (<?php echo $booking['room_type']; ?>)</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-credit-card me-2"></i>Payment</h6>
                                        <p><span class="badge <?php echo getPaymentBadgeClass($booking['payment_status']); ?>"><?php echo $booking['payment_status']; ?></span></p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-calendar-check me-2"></i>Check-in</h6>
                                        <p class="text-muted"><?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-calendar-times me-2"></i>Check-out</h6>
                                        <p class="text-muted"><?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <h6><i class="fas fa-money-bill-wave me-2"></i>Total Price</h6>
                                        <p class="text-muted">₱<?php echo number_format($booking['total_price'], 2); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="booking-footer d-flex justify-content-between align-items-center">
                                <small class="text-muted">Booked on: <?php echo date('M d, Y H:i', strtotime($booking['created_at'])); ?></small>
                                <div>
                                    <?php if (strtolower($booking['booking_status']) == 'pending' || strtolower($booking['booking_status']) == 'confirmed'): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger me-2" data-bs-toggle="modal" data-bs-target="#cancelModal<?php echo $booking['booking_id']; ?>">
                                            <i class="fas fa-times-circle me-1"></i>Cancel
                                        </button>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#detailsModal<?php echo $booking['booking_id']; ?>">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Details Modal -->
                    <div class="modal fade" id="detailsModal<?php echo $booking['booking_id']; ?>" tabindex="-1" aria-labelledby="detailsModalLabel<?php echo $booking['booking_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="detailsModalLabel<?php echo $booking['booking_id']; ?>">
                                        Booking Details #<?php echo $booking['booking_id']; ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <?php if (isset($firstImage)): ?>
                                            <img src="../uploads/<?php echo $firstImage; ?>" 
                                                alt="<?php echo $booking['room_title']; ?>" class="img-fluid rounded mb-4">
                                            <?php else: ?>
                                            <div class="text-center p-4 bg-light mb-4 rounded">
                                                <i class="fas fa-hotel fa-3x text-muted"></i>
                                                <p class="mt-2">Room image</p>
                                            </div>
                                            <?php endif; ?>
                                                
                                            <div class="card mb-4">
                                                <div class="card-header bg-primary text-white">
                                                    <i class="fas fa-info-circle me-2"></i>Booking Status
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <h6>Reservation Status</h6>
                                                        <span class="badge <?php echo getStatusBadgeClass($booking['booking_status']); ?>"><?php echo $booking['booking_status']; ?></span>
                                                    </div>
                                                    <div>
                                                        <h6>Payment Status</h6>
                                                        <span class="badge <?php echo getPaymentBadgeClass($booking['payment_status']); ?>"><?php echo $booking['payment_status']; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="card mb-4">
                                                <div class="card-header bg-primary text-white">
                                                    <i class="fas fa-hotel me-2"></i>Room Information
                                                </div>
                                                <div class="card-body">
                                                    <h6>Room Name</h6>
                                                    <p><?php echo $booking['room_title']; ?></p>
                                                    
                                                    <h6>Room Type</h6>
                                                    <p><?php echo $booking['room_type']; ?></p>
                                                </div>
                                            </div>
                                            
                                            <div class="card mb-4">
                                                <div class="card-header bg-primary text-white">
                                                    <i class="fas fa-calendar-alt me-2"></i>Stay Information
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6>Check-in Date</h6>
                                                            <p><?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6>Check-out Date</h6>
                                                            <p><?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></p>
                                                        </div>
                                                    </div>
                                                    
                                                    <h6>Duration</h6>
                                                    <p><?php echo calculateNights($booking['check_in_date'], $booking['check_out_date']); ?> night(s)</p>
                                                </div>
                                            </div>
                                            
                                            <div class="card mb-4">
                                                <div class="card-header bg-primary text-white">
                                                    <i class="fas fa-money-check-alt me-2"></i>Payment Information
                                                </div>
                                                <div class="card-body">
                                                    <h6>Total Amount</h6>
                                                    <p>₱<?php echo number_format($booking['total_price'], 2); ?></p>
                                                </div>
                                            </div>
                                            
                                            <?php if (!empty($booking['special_requests'])): ?>
                                            <div class="card mb-4">
                                                <div class="card-header bg-primary text-white">
                                                    <i class="fas fa-clipboard-list me-2"></i>Special Requests
                                                </div>
                                                <div class="card-body">
                                                    <p><?php echo $booking['special_requests']; ?></p>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <div>
                                                <small class="text-muted">Booking created on: <?php echo date('M d, Y H:i', strtotime($booking['created_at'])); ?></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <?php if (strtolower($booking['booking_status']) == 'pending' || strtolower($booking['booking_status']) == 'confirmed'): ?>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal<?php echo $booking['booking_id']; ?>" data-bs-dismiss="modal">
                                            <i class="fas fa-times-circle me-1"></i>Cancel Booking
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cancel Booking Modal -->
                    <div class="modal fade" id="cancelModal<?php echo $booking['booking_id']; ?>" tabindex="-1" aria-labelledby="cancelModalLabel<?php echo $booking['booking_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title" id="cancelModalLabel<?php echo $booking['booking_id']; ?>">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Cancel Booking
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to cancel your booking for:</p>
                                    <div class="card p-3 mb-3">
                                        <p class="mb-1"><strong>Room:</strong> <?php echo $booking['room_title']; ?> (<?php echo $booking['room_type']; ?>)</p>
                                        <p class="mb-1"><strong>Check-in:</strong> <?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></p>
                                        <p class="mb-0"><strong>Check-out:</strong> <?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></p>
                                    </div>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-info-circle me-2"></i>This action cannot be undone. Please contact the hotel for any refund inquiries.
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep My Booking</button>
                                    <form method="POST">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                        <button type="button" class="btn btn-danger cancel-btn"
                                                data-bs-booking-id="<?php echo $booking['booking_id']; ?>">
                                            <i class="fas fa-times-circle me-1"></i>Cancel Booking
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-12">
                    <div class="card no-bookings">
                        <i class="fas fa-calendar-times fa-5x text-muted mb-3"></i>
                        <h3 class="text-muted">No Bookings Found</h3>
                        <p class="text-center">You haven't made any reservations yet.</p>
                        <div class="mt-3">
                            <a href="roomBookings.php" class="btn btn-primary">Book a Room Now</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../components/footer.php'; ?>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        document.querySelectorAll('.cancel-btn').forEach(button => {
            button.addEventListener('click', function () {
                const bookingId = this.getAttribute('data-bs-booking-id');
                const detailsModal = document.getElementById('detailsModal' + bookingId);
                const cancelModal = new bootstrap.Modal(document.getElementById('cancelModal' + bookingId));

                // Hide the details modal first
                const bsDetailsModal = bootstrap.Modal.getInstance(detailsModal);
                bsDetailsModal.hide();

                // Then show the cancel modal after a short delay
                setTimeout(() => {
                    cancelModal.show();
                }, 300); // enough time for fade-out animation
            });
        });

    </script>
</body>
</html>