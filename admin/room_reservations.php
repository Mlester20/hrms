<?php

require_once '../components/config.php';
require_once '../controllers/roomReservationsController.php';
require_once '../includes/flash.php';
require_once '../middleware/authMiddleware.php';
requireAdmin();

// Helper functions
function getPaymentStatusColor($status) {
    switch ($status) {
        case 'unpaid':
            return '#dc3545';
        case 'partially_paid':
            return '#ffc107';
        case 'paid':
            return '#28a745';
        default:
            return '#ffffff';
    }
}

function getStatusColor($status) {
    switch ($status) {
        case 'pending':
            return '#ffc107';
        case 'confirmed':
            return '#28a745';
        case 'canceled':
            return '#dc3545';
        case 'complete':
            return '#0dcaf0';
        default:
            return '#ffffff';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8y+4g5e5c5e5c5e5c5e5c5e5c5e5c5e5c5e5c5e" crossorigin="anonymous" />
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../css/notifications.css">
    <link rel="shortcut icon" href="../images/final.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .status-column {
            width: 140px;
            min-width: 140px;
        }
        .status-column .form-select {
            width: 100%;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <?php include '../components/header_admin.php'; ?>

    <div class="container-fluid py-4">
        <div class="text-end col-md-4 mb-3">
            <input type="text" class="form-control form-control-sm mb-3" id="searchInput" placeholder="Search by Guest Name or Room Title" onkeyup="filterTable()">
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow">
                    <h5 class="text-center mt-4">Room Reservations</h5>
                    <div class="card-body">
                        
                        <?php showFlash(); ?>

                        <div class="table-responsive">
                            <table id="reservationsTable" class="table table-white">
                                <thead>
                                    <tr>
                                        <!-- <th>ID</th> -->
                                        <th>Guest Name</th>
                                        <th>Room</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <!-- <th>Total Price</th> -->
                                        <th>Booking Status</th>
                                        <th>Payment Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="reservationsBody">
                                    <?php while($row = mysqli_fetch_assoc($reservations_data['data'])): ?>
                                        <tr>
                                            <!-- <td><?php echo $row['booking_id']; ?></td> -->
                                            <td>
                                                <?php echo $row['guest_name']; ?><br>
                                                <!-- <small class="text-muted"><?php echo $row['guest_email']; ?></small> -->
                                            </td>
                                            <td>
                                                <?php echo $row['room_title']; ?><br>
                                                <small class="text-muted"><?php echo $row['room_type']; ?></small>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($row['check_in_date'])); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($row['check_out_date'])); ?></td>
                                            <!-- <td>₱<?php echo number_format($row['total_price'], 2); ?></td> -->
                                            <td class="status-column">
                                                <form method="POST">
                                                    <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                                                    <input type="hidden" name="status_type" value="booking">
                                                    <select name="new_status" class="form-select form-select-sm status-select" 
                                                            onchange="this.form.submit()" 
                                                            style="background-color: <?php echo getStatusColor($row['booking_status']); ?>">
                                                        
                                                        <?php if ($row['booking_status'] === 'pending'): ?>
                                                            <option class="card" value="pending" selected>Pending</option class="card">
                                                        <?php else: ?>
                                                            <option class="card" value="pending" disabled class="options">Pending (cannot revert)</option class="card">
                                                        <?php endif; ?>
                                                        
                                                        <option class="card" value="confirmed" <?php echo $row['booking_status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option class="card">
                                                        <option class="card" value="cancelled" <?php echo $row['booking_status'] == 'cancelled' ? 'selected' : ''; ?>>Canceled</option class="card">
                                                        <option class="card" value="completed" <?php echo $row['booking_status'] == 'completed' ? 'selected' : ''; ?>>Complete</option class="card">
                                                    </select>

                                                    <input type="hidden" name="update_status" value="1">
                                                </form>
                                            </td>
                                            <td class="status-column">
                                                <form method="POST">
                                                    <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                                                    <input type="hidden" name="status_type" value="payment">
                                                    <select name="new_status" class="form-select form-select-sm status-select" 
                                                            onchange="this.form.submit()"
                                                            style="background-color: <?php echo getPaymentStatusColor($row['payment_status']); ?>">
                                                        <option class="card" value="unpaid" <?php echo $row['payment_status'] == 'unpaid' ? 'selected' : ''; ?>>Unpaid</option class="card">
                                                        <option class="card" value="partially_paid" <?php echo $row['payment_status'] == 'partially_paid' ? 'selected' : ''; ?>>Partially Paid</option class="card">
                                                        <option class="card" value="paid" <?php echo $row['payment_status'] == 'paid' ? 'selected' : ''; ?>>Paid</option class="card">
                                                    </select>
                                                    <input type="hidden" name="update_status" value="1">
                                                </form>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailsModal" 
                                                        onclick="showDetails(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                            
                            <!-- Pagination -->
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if($reservations_data['current_page'] > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $reservations_data['current_page'] - 1; ?>" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for($i = 1; $i <= $reservations_data['total_pages']; $i++): ?>
                                        <li class="page-item <?php echo $i == $reservations_data['current_page'] ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if($reservations_data['current_page'] < $reservations_data['total_pages']): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $reservations_data['current_page'] + 1; ?>" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                            
                            <!-- Records info -->
                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    Showing <?php echo ($reservations_data['current_page'] - 1) * $reservations_data['per_page'] + 1; ?> to 
                                    <?php echo min($reservations_data['current_page'] * $reservations_data['per_page'], $reservations_data['total_records']); ?> of 
                                    <?php echo $reservations_data['total_records']; ?> entries
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content card">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="detailsModalLabel">Reservation Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Guest Information</h6>
                            <p><strong>Name:</strong> <span id="guestName"></span></p>
                            <p><strong>Email:</strong> <span id="guestEmail"></span></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Booking Information</h6>
                            <p><strong>Booking ID:</strong> <span id="bookingId"></span></p>
                            <p><strong>Status:</strong> <span id="bookingStatus" class="badge"></span></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Room Details</h6>
                            <p><strong>Room Title:</strong> <span id="roomTitle"></span></p>
                            <p><strong>Room Type:</strong> <span id="roomType"></span></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Dates</h6>
                            <p><strong>Check In:</strong> <span id="checkIn"></span></p>
                            <p><strong>Check Out:</strong> <span id="checkOut"></span></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Payment Information</h6>
                            <p><strong>Total Price:</strong> <span id="totalPrice"></span></p>
                            <p><strong>Payment Status:</strong> <span id="paymentStatus" class="badge"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- js external scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="../js/notifications.js"></script>
    <script src="../js/searchGuess.js"></script>
    <script src="../js/room_reservations.js"></script>
</body>
</html>