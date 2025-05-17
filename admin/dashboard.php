<?php
session_start();
include '../components/config.php';
include '../controllers/dashboardData.php';

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Call the functions to fetch dashboard data
$stats = getDashboardStats($con);
$bookings_data = getBookingsData($con);
$booking_status_stats = getBookingStatusStats($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | <?php include '../components/title.php'; ?> </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8y+4g5e5c5e5c5e5c5e5c5e5c5e5c5e5c5e5c" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/notifications.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- <link rel="stylesheet" href="../css/darkTheme.css"> -->
</head>
<body>

    <?php include '../components/header_admin.php'; ?>
    
    <div class="container-fluid py-4">
        <div class="row">
            <!-- Campaign Overview Column -->
            <div class="col-md-8">
                <div class="row mb-4">
                    <div class="col-md-3 col-6">
                        <div class="card dashboard-card bg-primary text-white mb-3">
                            <div class="card-body">
                                <h6 class="card-title"> <i class="fas fa-book"></i> Booked</h6>
                                <h3 class="mb-0"><?php echo $stats['today_bookings']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card dashboard-card bg-success text-white mb-3">
                            <div class="card-body">
                                <h6 class="card-title"> <i class="fas fa-users"></i> Total Users</h6>
                                <h3 class="mb-0"> <?php echo $stats['total_users']; ?> </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card dashboard-card bg-warning text-white mb-3">
                            <div class="card-body">
                                <h6 class="card-title"> <i class="fas fa-table"></i> Pending Tables</h6>
                                <h3 class="mb-0"> <?php echo $stats['pending_tables']; ?> </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card dashboard-card bg-info text-white mb-3">
                            <div class="card-body">
                                <h6 class="card-title"> <i class="fas fa-coins"></i> Revenue</h6>
                                <h3 class="mb-0">₱<?php echo number_format($stats['total_revenue'], 2); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campaign Overview Chart -->
                <div class="chart-container mb-4">
                    <canvas id="campaignOverviewChart"></canvas>
                </div>
            </div>

            <!-- Revenue Stat Column -->
            <div class="col-md-4">
                <div class="chart-container">
                    <h5 class="card-title text-center text-muted">Bookings Status</h5>
                    <canvas id="revenueStatChart"></canvas>
                </div>
            </div>
        </div>

        

    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- custom js for real time notifications -->
    <script src="../js/notifications.js"></script>
    
    <script>
        // Campaign Overview Chart
        const campaignCtx = document.getElementById('campaignOverviewChart').getContext('2d');
        const bookingsData = <?php echo json_encode($bookings_data); ?>;
        const totalRevenue = <?php echo json_encode($stats); ?>;

        // Create campaign chart
        window.campaignChart = new Chart(campaignCtx, {
            type: 'line',
            data: {
                labels: bookingsData.map(item => item.date),
                datasets: [
                    {
                        label: 'Total Revenue',
                        data: bookingsData.map(item => item.revenue),
                        borderColor: '#00BCD4',
                        backgroundColor: 'rgba(0, 188, 212, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true
                    }
                ]

            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Revenue Overview',
                        color: '#333' // Always dark text on white background
                    },
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#333' // Always dark text on white background
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#333' // Always dark text on white background
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#333' // Always dark text on white background
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });


        // Revenue Stat Chart
        const revenueCtx = document.getElementById('revenueStatChart').getContext('2d');
        const bookingsStatsData = <?php echo json_encode($booking_status_stats); ?>;
        // Prepare data for the chart
        const labels = bookingsStatsData.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1));
        const counts = bookingsStatsData.map(item => parseInt(item.count));
        const colors = {
            'Pending': '#FF4081',
            'Confirmed': '#4CAF50',
            'Canceled': '#E53935',
            'Completed': '#00BCD4'
        };

        const backgroundColor = labels.map(label => colors[label] || '#9C27B0');

        window.revenueChart = new Chart(revenueCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: counts,
                    backgroundColor: backgroundColor
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Booking Status Distribution',
                        color: '#333'
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#333'
                        }
                    }
                }
            }
        });


            // Update chart on window resize
            window.addEventListener('resize', function() {
                campaignChart.resize();
                revenueChart.resize();
            });
    </script>
</body>
</html>