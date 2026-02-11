<?php
session_start();
require_once '../components/config.php';
require_once '../controllers/roomReservationsController.php';

$auto_completed = runAutoCompletionCheck($con);
if ($auto_completed > 0) {
    echo "<div class='alert alert-success'>
        $auto_completed bookings were automatically completed.
    </div>";
}

function getPaymentStatusColor($status) {
    switch ($status) {
        case 'unpaid':
            return '#dc3545'; // red
        case 'partially_paid':
            return '#ffc107'; // yellow
        case 'paid':
            return '#28a745'; // green
        default:
            return '#ffffff'; // white
    }
}

function getStatusColor($status) {
    switch ($status) {
        case 'pending':
            return '#ffc107'; // yellow
        case 'confirmed':
            return '#28a745'; // green
        case 'canceled':
            return '#dc3545'; // red
        case 'complete':
            return '#0dcaf0'; // blue
        default:
            return '#ffffff'; // white
    }
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Get current page from URL parameter
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, $current_page); // Ensure page is at least 1

// Fetch reservations with pagination
$reservations_data = getReservations($con, $current_page, 5);
$reservations = $reservations_data['data'];

// Handle status updates
if (isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['new_status'];
    $type = $_POST['status_type'];
    
    if (updateBookingStatus($con, $booking_id, $new_status, $type)) {
        $_SESSION['success'] = "Status updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update status!";
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch reservations
$reservations = getReservations($con);
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
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php 
                                    echo $_SESSION['success'];
                                    unset($_SESSION['success']);
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php 
                                    echo $_SESSION['error'];
                                    unset($_SESSION['error']);
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

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


<?php
function getReservations($con, $page = 1, $perPage = 5) {
    // Calculate the offset
    $offset = ($page - 1) * $perPage;
    
    // Get total number of records for pagination
    $total_query = "SELECT COUNT(*) as total FROM bookings";
    $total_result = mysqli_query($con, $total_query);
    $total_row = mysqli_fetch_assoc($total_result);
    $total_records = $total_row['total'];
    
    // Calculate total pages
    $total_pages = ceil($total_records / $perPage);
    
    // Main query with LIMIT and OFFSET
    $query = "SELECT 
        b.booking_id,
        b.check_in_date,
        b.check_out_date,
        b.total_price,
        b.status as booking_status,
        b.payment_status,
        b.special_requests,
        b.created_at,
        u.name as guest_name,
        u.email as guest_email,
        r.title as room_title,
        rt.title as room_type
    FROM bookings b
    LEFT JOIN users u ON b.user_id = u.user_id
    LEFT JOIN rooms r ON b.room_id = r.id
    LEFT JOIN room_type rt ON r.room_type_id = rt.id
    ORDER BY b.created_at DESC
    LIMIT $perPage OFFSET $offset";
    
    $result = mysqli_query($con, $query);
    
    return [
        'data' => $result,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'total_records' => $total_records,
        'per_page' => $perPage
    ];
}

function updateBookingStatus($con, $booking_id, $status, $type) {
    $valid_booking_status = ['pending', 'confirmed', 'cancelled', 'completed'];
    $valid_payment_status = ['unpaid', 'partially_paid', 'paid'];
    
    if ($type === 'booking' && in_array($status, $valid_booking_status)) {
        // Get current booking details including user_id and room info
        $result = mysqli_query($con, "
            SELECT b.status, b.user_id, b.check_in_date, b.check_out_date, 
                   r.title as room_title, r.id as room_id
            FROM bookings b 
            LEFT JOIN rooms r ON b.room_id = r.id 
            WHERE b.booking_id = $booking_id
        ");
        
        if ($result && $row = mysqli_fetch_assoc($result)) {
            $current_status = $row['status'];
            $user_id = $row['user_id'];
            $room_title = $row['room_title'];
            $room_id = $row['room_id'];
            $check_in_date = $row['check_in_date'];
            $check_out_date = $row['check_out_date'];
            
            // Prevent changing from confirmed or later back to pending
            if ($current_status !== 'pending' && $status === 'pending') {
                return false; // reject the update
            }
            
            // Only proceed if status is actually changing
            if ($current_status === $status) {
                return true; // No change needed, but return success
            }
            
            // Update booking status
            $query = "UPDATE bookings SET status = ? WHERE booking_id = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "si", $status, $booking_id);
            $update_success = mysqli_stmt_execute($stmt);
            
            // Only create notification if update was successful and status actually changed
            if ($update_success) {
                createBookingNotification($con, $user_id, $booking_id, $current_status, $status, $room_title, $room_id, $check_in_date, $check_out_date);
            }
            
            return $update_success;
        }
        return false;
        
    } elseif ($type === 'payment' && in_array($status, $valid_payment_status)) {
        // Get booking details for payment notifications
        $result = mysqli_query($con, "
            SELECT b.payment_status, b.user_id, b.total_price, 
                   r.title as room_title
            FROM bookings b 
            LEFT JOIN rooms r ON b.room_id = r.id 
            WHERE b.booking_id = $booking_id
        ");
        
        if ($result && $row = mysqli_fetch_assoc($result)) {
            $current_payment_status = $row['payment_status'];
            $user_id = $row['user_id'];
            $total_price = $row['total_price'];
            $room_title = $row['room_title'];
            
            // Only proceed if status is actually changing
            if ($current_payment_status === $status) {
                return true; // No change needed, but return success
            }
            
            // Update payment status
            $query = "UPDATE bookings SET payment_status = ? WHERE booking_id = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "si", $status, $booking_id);
            $update_success = mysqli_stmt_execute($stmt);
            
            // Only create notification if update was successful and status actually changed
            if ($update_success) {
                createPaymentNotification($con, $user_id, $booking_id, $current_payment_status, $status, $room_title, $total_price);
            }
            
            return $update_success;
        }
        return false;
    } else {
        return false;
    }
}

function createBookingNotification($con, $user_id, $booking_id, $old_status, $new_status, $room_title, $room_id, $check_in_date, $check_out_date) {
    // Check if notification already exists for this booking and status change
    $check_query = "SELECT COUNT(*) as count FROM notifications 
                   WHERE user_id = ? AND booking_id = ? AND type = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
    $notification_type = 'booking_' . $new_status;
    $check_stmt = mysqli_prepare($con, $check_query);
    mysqli_stmt_bind_param($check_stmt, "iis", $user_id, $booking_id, $notification_type);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    $check_row = mysqli_fetch_assoc($check_result);
    
    // If notification already exists within the last minute, don't create another one
    if ($check_row['count'] > 0) {
        error_log("Notification already exists for booking $booking_id, user $user_id, type $notification_type");
        return false;
    }
    
    $title = '';
    $message = '';
    $type = '';
    
    // Format dates for display
    $check_in_formatted = date('F j, Y', strtotime($check_in_date));
    $check_out_formatted = date('F j, Y', strtotime($check_out_date));
    $room_display = $room_title ? $room_title : "Room #$room_id";
    
    switch ($new_status) {
        case 'confirmed':
            $title = 'Booking Confirmed! ✅';
            $message = "Great news! Your booking for {$room_display} from {$check_in_formatted} to {$check_out_formatted} has been confirmed. We look forward to welcoming you!";
            $type = 'booking_confirmed';
            break;
            
        case 'cancelled':
            $title = 'Booking Cancelled ❌';
            $message = "Your booking for {$room_display} from {$check_in_formatted} to {$check_out_formatted} has been cancelled. If you have any questions, please contact us.";
            $type = 'booking_cancelled';
            break;
            
        case 'completed':
            $title = 'Stay Completed 🏨';
            $message = "Thank you for staying with us! Your booking for {$room_display} has been completed. We hope you had a wonderful experience.";
            $type = 'booking_completed';
            break;
            
        default:
            return false; // Don't create notification for other status changes
    }
    
    // Insert notification
    $insert_query = "INSERT INTO notifications (user_id, booking_id, title, message, type, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($con, $insert_query);
    mysqli_stmt_bind_param($stmt, "iisss", $user_id, $booking_id, $title, $message, $type);
    $success = mysqli_stmt_execute($stmt);
    
    if ($success) {
        error_log("Created notification for booking $booking_id, user $user_id, type $type");
    } else {
        error_log("Failed to create notification for booking $booking_id: " . mysqli_error($con));
    }
    
    return $success;
}

function createPaymentNotification($con, $user_id, $booking_id, $old_status, $new_status, $room_title, $total_price) {
    // Check if notification already exists for this booking and payment status
    $check_query = "SELECT COUNT(*) as count FROM notifications 
                   WHERE user_id = ? AND booking_id = ? AND type = 'payment_received' AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
    $check_stmt = mysqli_prepare($con, $check_query);
    mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $booking_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    $check_row = mysqli_fetch_assoc($check_result);
    
    // If notification already exists within the last minute, don't create another one
    if ($check_row['count'] > 0) {
        error_log("Payment notification already exists for booking $booking_id, user $user_id");
        return false;
    }
    
    $title = '';
    $message = '';
    $type = 'payment_received';
    
    $formatted_price = number_format($total_price, 2);
    $room_display = $room_title ? $room_title : "your booking";
    
    switch ($new_status) {
        case 'paid':
            $title = 'Payment Received! 💳';
            $message = "We have received your full payment of ₱{$formatted_price} for {$room_display}. Thank you for your payment!";
            break;
            
        case 'partially_paid':
            $title = 'Partial Payment Received 💰';
            $message = "We have received a partial payment for {$room_display}. Remaining balance: ₱{$formatted_price}. Please complete your payment before check-in.";
            break;
            
        default:
            return false; // Don't create notification for unpaid status
    }
    
    // Insert notification
    $insert_query = "INSERT INTO notifications (user_id, booking_id, title, message, type, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($con, $insert_query);
    mysqli_stmt_bind_param($stmt, "iisss", $user_id, $booking_id, $title, $message, $type);
    $success = mysqli_stmt_execute($stmt);
    
    if ($success) {
        error_log("Created payment notification for booking $booking_id, user $user_id");
    } else {
        error_log("Failed to create payment notification for booking $booking_id: " . mysqli_error($con));
    }
    
    return $success;
}

// ========== AUTO-COMPLETE BOOKING FUNCTIONS ==========

/**
 * Automatically mark bookings as completed when check-out date has passed
 */
function autoCompleteBookings($con) {
    // Get all confirmed bookings where check_out_date has passed and status is not already completed
    $query = "SELECT 
        b.booking_id,
        b.user_id,
        b.check_in_date,
        b.check_out_date,
        b.status,
        r.title as room_title,
        r.id as room_id
    FROM bookings b
    LEFT JOIN rooms r ON b.room_id = r.id
    WHERE b.status = 'confirmed' 
    AND DATE(b.check_out_date) < CURDATE()";
    
    $result = mysqli_query($con, $query);
    
    if (!$result) {
        error_log("Error fetching bookings for auto-completion: " . mysqli_error($con));
        return false;
    }
    
    $completed_count = 0;
    
    while ($booking = mysqli_fetch_assoc($result)) {
        // Update booking status to completed
        $update_query = "UPDATE bookings SET 
            status = 'completed',
            updated_at = NOW()
            WHERE booking_id = ?";
        
        $stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($stmt, "i", $booking['booking_id']);
        
        if (mysqli_stmt_execute($stmt)) {
            // Create auto-completion notification using existing function
            createAutoCompletionNotification(
                $con,
                $booking['user_id'],
                $booking['booking_id'],
                'confirmed',
                'completed',
                $booking['room_title'] ?: "Room #" . $booking['room_id'],
                $booking['room_id'],
                $booking['check_in_date'],
                $booking['check_out_date']
            );
            
            $completed_count++;
            error_log("Auto-completed booking ID: " . $booking['booking_id']);
        } else {
            error_log("Failed to auto-complete booking ID: " . $booking['booking_id'] . " - " . mysqli_error($con));
        }
        
        mysqli_stmt_close($stmt);
    }
    
    if ($completed_count > 0) {
        error_log("Auto-completed $completed_count bookings");
    }
    
    return $completed_count;
}

/**
 * Create notification for auto-completed bookings
 */
function createAutoCompletionNotification($con, $user_id, $booking_id, $old_status, $new_status, $room_title, $room_id, $check_in_date, $check_out_date) {
    // Check if completion notification already exists for this booking
    $check_query = "SELECT COUNT(*) as count FROM notifications 
                   WHERE user_id = ? AND booking_id = ? AND type = 'booking_completed'";
    $check_stmt = mysqli_prepare($con, $check_query);
    mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $booking_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    $check_row = mysqli_fetch_assoc($check_result);
    
    // If notification already exists, don't create another one
    if ($check_row['count'] > 0) {
        return false;
    }
    
    $check_in_formatted = date('F j, Y', strtotime($check_in_date));
    $check_out_formatted = date('F j, Y', strtotime($check_out_date));
    $room_display = $room_title ? $room_title : "Room #$room_id";
    
    $title = 'Stay Completed - Thank You! 🏨';
    $message = "Thank you for staying with us! Your booking for {$room_display} (from {$check_in_formatted} to {$check_out_formatted}) has been automatically completed. We hope you had a wonderful experience and look forward to welcoming you back soon!";
    $type = 'booking_completed';
    
    // Insert notification
    $insert_query = "INSERT INTO notifications (user_id, booking_id, title, message, type, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($con, $insert_query);
    mysqli_stmt_bind_param($stmt, "iisss", $user_id, $booking_id, $title, $message, $type);
    $success = mysqli_stmt_execute($stmt);
    
    if ($success) {
        error_log("Created auto-completion notification for booking $booking_id, user $user_id");
    } else {
        error_log("Failed to create auto-completion notification for booking $booking_id: " . mysqli_error($con));
    }
    
    return $success;
}

/**
 * Run auto-completion check with throttling to prevent too frequent runs
 */
function runAutoCompletionCheck($con) {
    // Check if we've run this recently (within the last hour)
    $last_run_file = dirname(__FILE__) . '/last_auto_completion_check.txt';
    $current_time = time();
    
    if (file_exists($last_run_file)) {
        $last_run = (int)file_get_contents($last_run_file);
        // If last run was less than 1 hour ago, skip
        if (($current_time - $last_run) < 3600) {
            return 0; // No check performed
        }
    }
    
    // Run the auto-completion
    $completed_count = autoCompleteBookings($con);
    
    // Update last run time
    file_put_contents($last_run_file, $current_time);
    
    return $completed_count;
}

/**
 * Alternative function with specific checkout time (e.g., 11 AM checkout)
 */
function autoCompleteBookingsWithTime($con, $checkout_hour = 11) {
    $current_datetime = date('Y-m-d H:i:s');
    
    // Get all confirmed bookings where check_out_date + checkout_hour has passed
    $query = "SELECT 
        b.booking_id,
        b.user_id,
        b.check_in_date,
        b.check_out_date,
        b.status,
        r.title as room_title,
        r.id as room_id
    FROM bookings b
    LEFT JOIN rooms r ON b.room_id = r.id
    WHERE b.status = 'confirmed' 
    AND CONCAT(DATE(b.check_out_date), ' ', LPAD(?, 2, '0'), ':00:00') <= ?";
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "is", $checkout_hour, $current_datetime);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result) {
        error_log("Error fetching bookings for time-based auto-completion: " . mysqli_error($con));
        return false;
    }
    
    $completed_count = 0;
    
    while ($booking = mysqli_fetch_assoc($result)) {
        // Update booking status to completed
        $update_query = "UPDATE bookings SET 
            status = 'completed',
            updated_at = NOW()
            WHERE booking_id = ?";
        
        $update_stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($update_stmt, "i", $booking['booking_id']);
        
        if (mysqli_stmt_execute($update_stmt)) {
            // Create completion notification
            createAutoCompletionNotification(
                $con,
                $booking['user_id'],
                $booking['booking_id'],
                'confirmed',
                'completed',
                $booking['room_title'] ?: "Room #" . $booking['room_id'],
                $booking['room_id'],
                $booking['check_in_date'],
                $booking['check_out_date']
            );
            
            $completed_count++;
            error_log("Auto-completed booking ID: " . $booking['booking_id'] . " (time-based)");
        } else {
            error_log("Failed to auto-complete booking ID: " . $booking['booking_id'] . " - " . mysqli_error($con));
        }
        
        mysqli_stmt_close($update_stmt);
    }
    
    mysqli_stmt_close($stmt);
    
    if ($completed_count > 0) {
        error_log("Auto-completed $completed_count bookings (time-based)");
    }
    
    return $completed_count;
}

// ========== EXISTING FUNCTIONS (UNCHANGED) ==========

// Function to create notifications table if it doesn't exist
function createNotificationsTable($con) {
    $query = "CREATE TABLE IF NOT EXISTS notifications (
        notification_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        booking_id INT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        type ENUM('booking_confirmed', 'booking_cancelled', 'booking_completed', 'payment_received', 'general') DEFAULT 'general',
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_booking_id (booking_id),
        INDEX idx_is_read (is_read),
        INDEX idx_user_booking_type_time (user_id, booking_id, type, created_at),
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE SET NULL
    )";
    
    return mysqli_query($con, $query);
}

// Function to manually create notification (for admin use)
function createCustomNotification($con, $user_id, $title, $message, $type = 'general', $booking_id = null) {
    $insert_query = "INSERT INTO notifications (user_id, booking_id, title, message, type, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($con, $insert_query);
    mysqli_stmt_bind_param($stmt, "iisss", $user_id, $booking_id, $title, $message, $type);
    return mysqli_stmt_execute($stmt);
}

function cleanupDuplicateNotifications($con) {
    $query = "
    DELETE n1 FROM notifications n1
    INNER JOIN notifications n2 
    WHERE n1.notification_id > n2.notification_id
    AND n1.user_id = n2.user_id
    AND n1.booking_id = n2.booking_id
    AND n1.type = n2.type
    AND n1.title = n2.title
    AND ABS(TIMESTAMPDIFF(SECOND, n1.created_at, n2.created_at)) < 10
    ";
    
    $result = mysqli_query($con, $query);
    if ($result) {
        $affected_rows = mysqli_affected_rows($con);
        error_log("Cleaned up $affected_rows duplicate notifications");
        return $affected_rows;
    }
    return false;
}

?>