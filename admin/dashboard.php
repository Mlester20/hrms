<?php
session_start();
include '../components/config.php';
include '../controllers/fetchBookings.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Dashboard | <?php include '../components/title.php'; ?> </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/notifications.css">
    <link rel="stylesheet" href="../css/darkTheme.css">
    <style>
        .chart-container {
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .dashboard-card {
            border: none;
            border-radius: 12px;
            transition: transform 0.2s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
        }
        .recent-bookings {
            max-height: 400px;
            overflow-y: auto;
        }
        .booking-item {
            border-left: 4px solid #007bff;
            background: #f8f9fa;
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 8px;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 4px 8px;
        }
    </style>
</head>
<body>

    <?php include '../components/header_admin.php'; ?>
    
    <div class="container-fluid py-4">
        <h3 class="mb-4 card-title text-center text-muted">Room Analytics</h3>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-6">
                <div class="card dashboard-card bg-primary text-white mb-3">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-calendar-check"></i> Today's Bookings</h6>
                        <h3 class="mb-0"><?php echo number_format($stats['today_bookings']); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card dashboard-card bg-success text-white mb-3">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-users"></i> Total Customers</h6>
                        <h3 class="mb-0"><?php echo number_format($stats['total_users']); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card dashboard-card bg-warning text-white mb-3">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-clock"></i> Pending Bookings</h6>
                        <h3 class="mb-0"><?php echo number_format($stats['pending_tables']); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card dashboard-card bg-info text-white mb-3">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-peso-sign"></i> Total Revenue</h6>
                        <h3 class="mb-0">₱<?php echo number_format($stats['total_revenue'], 2); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Main Charts -->
            <div class="col-md-8">
                <!-- Monthly Revenue Line Chart -->
                <div class="chart-container">
                    <h5 class="text-center text-muted">Dashboard</h5>
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
                <!-- Monthly Bookings Bar Chart -->
                <div class="chart-container">
                    <h5 class="text-center text-muted">Monthly Bookings (<?php echo date('Y'); ?>)</h5>
                    <canvas id="bookingsChart" height="100"></canvas>
                </div>
            </div>

            <!-- Right Column - Status Charts & Recent Bookings -->
            <div class="col-md-4">
                <!-- Booking Status Pie Chart -->
                <div class="chart-container">
                    <h5 class="text-center text-muted">Booking Status</h5>
                    <?php if (count($pie_statuses) > 0): ?>
                        <canvas id="statusChart" height="150"></canvas>
                    <?php else: ?>
                        <div class="text-center text-muted p-5">No bookings data available!</div>
                    <?php endif; ?>
                </div>


                <!-- Recent Bookings -->
                <div class="chart-container">
                    <h5 class="text-center text-muted mb-3">Recent Bookings</h5>
                    <div class="recent-bookings">
                        <?php foreach ($recent_bookings as $booking): ?>
                            <div class="booking-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong><?php echo htmlspecialchars($booking['customer_name'] ?: 'Guest'); ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            Room: <?php echo htmlspecialchars($booking['room_id'] ?: 'N/A'); ?>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <?php echo date('M j', strtotime($booking['check_in_date'])); ?> - 
                                            <?php echo date('M j', strtotime($booking['check_out_date'])); ?>
                                        </small>
                                    </div>
                                    <span class="badge status-badge bg-<?php 
                                        echo $booking['status'] == 'confirmed' ? 'success' : 
                                             ($booking['status'] == 'pending' ? 'warning' : 
                                             ($booking['status'] == 'cancelled' ? 'danger' : 'info')); 
                                    ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </div>
                                <div class="mt-2 d-flex justify-content-between">
                                    <small class="text-muted">
                                        Booking #<?php echo $booking['booking_id']; ?>
                                    </small>
                                    <strong class="text-primary">
                                        ₱<?php echo number_format($booking['total_price'], 2); ?>
                                    </strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (empty($recent_bookings)): ?>
                            <div class="text-center text-muted p-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>No recent bookings found</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/notifications.js"></script>
    <script src="../js/darkTheme.js"></script>
    
    <script>
        // Convert PHP data to JavaScript
        const statusLabels = <?php echo json_encode($pie_statuses); ?>;
        const statusCounts = <?php echo json_encode($pie_counts); ?>;
        const paymentLabels = <?php echo json_encode($payment_statuses); ?>;
        const paymentCounts = <?php echo json_encode($payment_counts); ?>;
        const bookingsData = <?php echo json_encode($bar_data); ?>;
        const revenueData = <?php echo json_encode($line_data); ?>;

        // Color schemes
        const statusColors = {
            'Pending': '#FFC107',
            'Confirmed': '#28A745', 
            'Canceled': '#DC3545',
            'Completed': '#17A2B8'
        };

        const paymentColors = {
            'Paid': '#28A745',
            'Pending': '#FFC107',
            'Failed': '#DC3545',
            'Refunded': '#6C757D'
        };

        // Booking Status Chart
        <?php if (count($pie_statuses) > 0): ?>
        const statusChart = new Chart(document.getElementById("statusChart"), {
            type: "doughnut",
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusCounts,
                    backgroundColor: statusLabels.map(label => statusColors[label] || '#6C757D'),
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
        <?php endif; ?>

        // Payment Status Chart
        <?php if (count($payment_statuses) > 0): ?>
        const paymentChart = new Chart(document.getElementById("paymentChart"), {
            type: "pie",
            data: {
                labels: paymentLabels,
                datasets: [{
                    data: paymentCounts,
                    backgroundColor: paymentLabels.map(label => paymentColors[label] || '#6C757D'),
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
        <?php endif; ?>

        // Monthly Bookings Chart
        const bookingsChart = new Chart(document.getElementById("bookingsChart"), {
            type: "bar",
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                    label: "Bookings",
                    data: bookingsData,
                    backgroundColor: "rgba(0, 123, 255, 0.7)",
                    borderColor: "#007BFF",
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Monthly Revenue Chart
        const revenueChart = new Chart(document.getElementById("revenueChart"), {
            type: "line",
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                    label: "Revenue (₱)",
                    data: revenueData,
                    borderColor: "#00BCD4",
                    backgroundColor: "rgba(0, 188, 212, 0.1)",
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: "#00BCD4",
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: ₱' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Resize charts on window resize
        window.addEventListener('resize', function() {
            if (typeof statusChart !== 'undefined') statusChart.resize();
            if (typeof paymentChart !== 'undefined') paymentChart.resize();
            bookingsChart.resize();
            revenueChart.resize();
        });
    </script>
</body>
</html>