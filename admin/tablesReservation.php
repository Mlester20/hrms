<?php
session_start();
include '../components/connection.php';
include '../controllers/fetchTablesReservation.php';

// Check if the user is not logged in
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
    <title>Table Reservations - <?php include '../components/title.php'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../css/notifications.css">
    <link rel="shortcut icon" href="../images/final.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/app.css">
</head>
<body>
    <?php include '../components/header_admin.php'; ?>

    <div class="container mt-5">
        <div class="text-end col-md-4 mb-3">
            <input type="text" class="form-control form-control-sm mb-3" id="SearchInput" placeholder="Search Restaurant Reservations" onkeyup="filterTable()">
        </div>
        <h3 class="mb-4 text-center">Table Reservations</h3>
        <table class="table table-bordered table-white" id="reservationsTable">
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
                    <th>Print</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reservations)): ?>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td><?php echo $reservation['table_number']; ?></td>
                            <td><?php echo $reservation['capacity']; ?> persons</td>
                            <td><?php echo $reservation['reservation_date']; ?></td>
                            <td>
                                <?php 
                                $time = date("g:i A", strtotime($reservation['time_slot']));
                                echo $time;
                                ?>
                            </td>
                            <td><?php echo $reservation['guest_count']; ?></td>
                            <td><?php echo $reservation['special_requests'] ?: 'None'; ?></td>
                            <td><?php echo $reservation['name']; ?></td>
                            <td><?php echo ucfirst($reservation['status']); ?></td>
                            <td>
                                <button class="btn btn-success btn-sm mark-done" data-id="<?php echo $reservation['reservation_id']; ?>"> <i class="fas fa-check"></i> </button>
                                <button class="btn btn-danger btn-sm delete-reservation" data-id="<?php echo $reservation['reservation_id']; ?>"> <i class="fas fa-trash"></i> </button>
                            
                            </td>
                            <td>
                                <button class="btn btn-primary btn-sm print-receipt" data-id="<?php echo $reservation['reservation_id']; ?>"><i class="fas fa-receipt"></i></button>
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
    <script src="../js/notifications.js"></script>
    <script src="../js/darkTheme.js"></script>
    <script src="../js/searchTableReservation.js"></script>
</body>
</html>