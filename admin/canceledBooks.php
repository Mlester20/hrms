<?php
session_start();

require_once '../components/connection.php';
require_once '../controllers/canceledBooksController.php';

//check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canceled Books - <?php include '../components/title.php'; ?> </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="shortcut icon" href="../images/final.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/notifications.css">
    <link rel="stylesheet" href="../css/app.css">
</head>
<body>
    <?php include '../components/header_admin.php'; ?>

    <div class="container mt-4">
        <h3 class="mt-4 mb-4 text-center">Canceled Bookings</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table">
                    <tr>
                        <th>Booking ID</th>
                        <th>Guest Name</th>
                        <th>Room</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Total Price</th>
                        <th>Payment Status</th>
                        <th>Canceled Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($canceledBookings as $booking) { ?>
                        <tr>
                            <td>#<?php echo $booking['booking_id']; ?></td>
                            <td><?php echo $booking['guest_name']; ?></td>
                            <td><?php echo $booking['room_title']; ?> (<?php echo $booking['room_type']; ?>)</td>
                            <td><?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></td>
                            <td><?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></td>
                            <td>₱<?php echo number_format($booking['total_price'], 2); ?></td>
                            <td>
                                <span class="badge <?php echo getPaymentBadgeClass($booking['payment_status']); ?>">
                                    <?php echo ucfirst($booking['payment_status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></td>
                            <td>
                                <form method="post" action="../controllers/canceledBooks.php" onsubmit="return confirm('Are you sure you want to delete this canceled booking?');">
                                    <input type="hidden" name="delete_booking_id" value="<?php echo $booking['booking_id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> 
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <!-- custom js scripts -->
    <script src="../js/notifications.js"></script>
</body>
</html>