<?php
session_start();
include '../components/config.php';
include '../controllers/dashboardData.php';

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Fetch dashboard data
$stats = getDashboardStats($con);
$bookings_data = getBookingsData($con);
$table_stats = getTableReservationsStats($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8y+4g5e5c5e5c5e5c5e5c5e5c5e5c5e5c5e5c5e" crossorigin="anonymous" />
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        .dashboard-card {
            transition: transform 0.2s;
            cursor: pointer;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        canvas {
            max-height: 300px;
            width: 100% !important;
        }
        @media (max-width: 767.98px) {
            .chart-container {
                margin-bottom: 2rem;
            }
        }
    </style>
</head>
<body>
    <?php include '../components/header_admin.php'; ?>

    <div class="container py-5">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card dashboard-card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Today's Bookings</h5>
                        <h2 class="mb-0"><?php echo $stats['today_bookings']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card dashboard-card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <h2 class="mb-0">₱<?php echo number_format($stats['total_revenue'], 2); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card dashboard-card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Pending Tables</h5>
                        <h2 class="mb-0"><?php echo $stats['pending_tables']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card dashboard-card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Active Bookings</h5>
                        <h2 class="mb-0"><?php echo $stats['active_bookings']; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row">
            <div class="col-md-8 mb-4">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0">Room Bookings Overview</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="bookingsChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0">Table Reservations Status</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="tableReservationsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Convert PHP data to JavaScript
        const bookingsData = <?php echo json_encode($bookings_data); ?>;
        const tableStats = <?php echo json_encode($table_stats); ?>;

        // Bookings Chart
        const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
        new Chart(bookingsCtx, {
            type: 'line',
            data: {
                labels: bookingsData.map(item => item.date),
                datasets: [{
                    label: 'Number of Bookings',
                    data: bookingsData.map(item => item.count),
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }, {
                    label: 'Revenue (₱)',
                    data: bookingsData.map(item => item.revenue),
                    borderColor: 'rgb(255, 99, 132)',
                    tension: 0.1,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Bookings'
                        }
                    },
                    y1: {
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Revenue (₱)'
                        }
                    }
                }
            }
        });

        // Table Reservations Chart
        const tableCtx = document.getElementById('tableReservationsChart').getContext('2d');
        new Chart(tableCtx, {
            type: 'doughnut',
            data: {
                labels: tableStats.map(item => item.status.toUpperCase()),
                datasets: [{
                    data: tableStats.map(item => item.count),
                    backgroundColor: [
                        'rgb(75, 192, 192)',
                        'rgb(255, 205, 86)',
                        'rgb(255, 99, 132)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>

</body>
</html>