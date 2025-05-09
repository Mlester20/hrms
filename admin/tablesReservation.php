<?php
session_start();
include '../components/config.php';

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Fetch reserved tables with relational data
$query = "
    SELECT 
        r.reservation_id,
        r.reservation_date,
        r.time_slot,
        r.guest_count,
        r.special_requests,
        r.status, -- Include the status column
        t.table_number,
        t.capacity,
        u.name,
        u.email
    FROM table_reservations r
    INNER JOIN restaurant_tables t ON r.table_id = t.table_id
    INNER JOIN users u ON r.user_id = u.user_id
    ORDER BY r.reservation_date, r.time_slot
";

$result = mysqli_query($con, $query);

if (!$result) {
    die('Error fetching reservations: ' . mysqli_error($con));
}

$reservations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $reservations[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Reservations - <?php include '../components/title.php'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <?php include '../components/header_admin.php'; ?>

    <div class="container mt-5">
        <h3 class="mb-4 text-center text-muted">Table Reservations</h3>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Table Number</th>
                    <th>Capacity</th>
                    <th>Reservation Date</th>
                    <th>Time Slot</th>
                    <th>Guest Count</th>
                    <th>Special Requests</th>
                    <th>Reserved By</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reservations)): ?>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td><?php echo $reservation['table_number']; ?></td>
                            <td><?php echo $reservation['capacity']; ?> persons</td>
                            <td><?php echo $reservation['reservation_date']; ?></td>
                            <td><?php echo $reservation['time_slot']; ?></td>
                            <td><?php echo $reservation['guest_count']; ?></td>
                            <td><?php echo $reservation['special_requests'] ?: 'None'; ?></td>
                            <td><?php echo $reservation['name']; ?></td>
                            <td><?php echo ucfirst($reservation['status']); ?></td>
                            <td>
                                <button class="btn btn-success btn-sm mark-done" data-id="<?php echo $reservation['reservation_id']; ?>">Mark as Done</button>
                                <button class="btn btn-danger btn-sm delete-reservation" data-id="<?php echo $reservation['reservation_id']; ?>">Delete</button>
                                <button class="btn btn-primary btn-sm print-receipt" data-id="<?php echo $reservation['reservation_id']; ?>">Print Receipt</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">No reservations found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/reservations.js"></script>
</body>
</html>