<?php
session_start();
require_once '../components/config.php';
require_once '../controllers/roomReservationsController.php';


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
</head>
<body>
    <?php include '../components/header_admin.php'; ?>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <h5 class="text-center mt-4 text-muted">Room Reservations</h5>
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
                            <table id="reservationsTable" class="table table-light">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Guest</th>
                                        <th>Room</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Total Price</th>
                                        <th>Booking Status</th>
                                        <th>Payment Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($reservations)): ?>
                                        <tr>
                                            <td><?php echo $row['booking_id']; ?></td>
                                            <td>
                                                <?php echo $row['guest_name']; ?><br>
                                                <small class="text-muted"><?php echo $row['guest_email']; ?></small>
                                            </td>
                                            <td>
                                                <?php echo $row['room_title']; ?><br>
                                                <small class="text-muted"><?php echo $row['room_type']; ?></small>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($row['check_in_date'])); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($row['check_out_date'])); ?></td>
                                            <td>₱<?php echo number_format($row['total_price'], 2); ?></td>
                                            <td>
                                                <form method="POST">
                                                    <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                                                    <input type="hidden" name="status_type" value="booking">
                                                    <select name="new_status" class="form-select form-select-sm status-select" 
                                                            onchange="this.form.submit()" 
                                                            style="background-color: <?php echo getStatusColor($row['booking_status']); ?>">
                                                        <option value="pending" <?php echo $row['booking_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="confirmed" <?php echo $row['booking_status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                        <option value="canceled" <?php echo $row['booking_status'] == 'canceled' ? 'selected' : ''; ?>>Canceled</option>
                                                        <option value="completed" <?php echo $row['booking_status'] == 'completed' ? 'selected' : ''; ?>>Complete</option>
                                                    </select>
                                                    <input type="hidden" name="update_status" value="1">
                                                </form>
                                            </td>
                                            <td>
                                                <form method="POST">
                                                    <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                                                    <input type="hidden" name="status_type" value="payment">
                                                    <select name="new_status" class="form-select form-select-sm status-select" 
                                                            onchange="this.form.submit()"
                                                            style="background-color: <?php echo getPaymentStatusColor($row['payment_status']); ?>">
                                                        <option value="unpaid" <?php echo $row['payment_status'] == 'unpaid' ? 'selected' : ''; ?>>Unpaid</option>
                                                        <option value="partially_paid" <?php echo $row['payment_status'] == 'partially_paid' ? 'selected' : ''; ?>>Partially Paid</option>
                                                        <option value="paid" <?php echo $row['payment_status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                                                    </select>
                                                    <input type="hidden" name="update_status" value="1">
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
    <script>
        $(document).ready(function() {
            $('#reservationsTable').DataTable({
                order: [[8, 'desc']]
            });
        });

        function getStatusColor(status) {
            switch(status) {
                case 'pending': return '#ffc107';
                case 'confirmed': return '#28a745';
                case 'canceled': return '#dc3545';
                case 'complete': return '#0dcaf0';
                default: return '#ffffff';
            }
        }

        function getPaymentStatusColor(status) {
            switch(status) {
                case 'unpaid': return '#dc3545';
                case 'partially_paid': return '#ffc107';
                case 'paid': return '#28a745';
                default: return '#ffffff';
            }
        }
    </script>
</body>
</html>