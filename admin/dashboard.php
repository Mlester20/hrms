<?php
session_start();

require_once '../components/connection.php';
require_once '../controllers/dashboardController.php';
require_once '../middleware/authMiddleware.php';
requireAdmin();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Dashboard | <?php require_once '../includes/title.php'; ?> </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/notifications.css">
    <link rel="stylesheet" href="../css/app.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <?php require_once '../components/header_admin.php'; ?>

    <div class="container-fluid main-content">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Dashboard Analytics</h2>
                <p class="text-secondary mb-0">Welcome back! Here's what's happening today.</p>
            </div>
            <button class="btn btn-generate" onclick="downloadReport()">
                <i class="fas fa-download me-2"></i>Generate Report
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card" onclick="animateCard(this)">
                <div class="stat-icon blue">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-label">Today's Bookings</div>
                <div class="stat-value" data-target="<?php echo $stats['today_bookings']; ?>">0</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>12% vs yesterday</span>
                </div>
            </div>

            <div class="stat-card" onclick="animateCard(this)">
                <div class="stat-icon green">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-label">Total Customers</div>
                <div class="stat-value" data-target="<?php echo $stats['total_users']; ?>">0</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>8% this month</span>
                </div>
            </div>

            <div class="stat-card" onclick="animateCard(this)">
                <div class="stat-icon yellow">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-label">Pending Bookings</div>
                <div class="stat-value" data-target="<?php echo $stats['pending_tables']; ?>">0</div>
                <div class="stat-change negative">
                    <i class="fas fa-arrow-down"></i>
                    <span>3% vs last week</span>
                </div>
            </div>

            <div class="stat-card" onclick="animateCard(this)">
                <div class="stat-icon purple">
                    <i class="fas fa-peso-sign"></i>
                </div>
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value">₱<?php echo number_format($stats['total_revenue'] / 100, 2); ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>15% this month</span>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Main Charts -->
            <div class="col-lg-8">
                <!-- Monthly Revenue Line Chart -->
                <div class="chart-container">
                    <div class="chart-header">
                        <h5 class="chart-title">Revenue Overview</h5>
                        <div class="chart-filters">
                            <button class="filter-btn active" onclick="setTimeFilter(this, 'year')">Year</button>
                            <button class="filter-btn" onclick="setTimeFilter(this, 'month')">Month</button>
                            <button class="filter-btn" onclick="setTimeFilter(this, 'week')">Week</button>
                        </div>
                    </div>
                    <canvas id="revenueChart" height="80"></canvas>
                </div>

                <!-- Monthly Bookings Bar Chart -->
                <div class="chart-container">
                    <div class="chart-header">
                        <h5 class="chart-title">Bookings Trend (<?php echo date('Y'); ?>)</h5>
                        <div class="chart-filters">
                            <button class="filter-btn active">All Rooms</button>
                            <button class="filter-btn">Standard</button>
                            <button class="filter-btn">Deluxe</button>
                        </div>
                    </div>
                    <canvas id="bookingsChart" height="80"></canvas>
                </div>
            </div>

            <!-- Right Column - Status & Recent Bookings -->
            <div class="col-lg-4">
                <!-- Booking Status Circular Progress -->
                <div class="chart-container">
                    <h5 class="chart-title text-center">Booking Status</h5>
                    <?php if (count($pie_statuses) > 0): ?>
                        <div class="circular-progress">
                            <svg class="progress-circle" width="200" height="200">
                                <circle class="progress-bg" cx="100" cy="100" r="90"></circle>
                                <circle class="progress-bar" cx="100" cy="100" r="90" 
                                    style="stroke-dashoffset: <?php echo 565 - (565 * 0.75); ?>"></circle>
                            </svg>
                            <div class="progress-text">
                                <div class="progress-value">75%</div>
                                <div class="progress-label">Occupancy</div>
                            </div>
                        </div>
                        <canvas id="statusChart" height="150"></canvas>
                    <?php else: ?>
                        <div class="text-center p-5">
                            <i class="fas fa-chart-pie fa-3x mb-3" style="color: var(--text-secondary);"></i>
                            <p class="text-secondary">No bookings data available</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Recent Bookings -->
                <div class="chart-container">
                    <h5 class="chart-title mb-3">Recent Activity</h5>
                    <div class="recent-bookings">
                        <?php foreach ($recent_bookings as $booking): ?>
                            <div class="booking-item">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($booking['customer_name'] ?: 'Guest'); ?></div>
                                        <small class="text-secondary">
                                            Room <?php echo htmlspecialchars($booking['room_id'] ?: 'N/A'); ?> • 
                                            <?php echo date('M j', strtotime($booking['check_in_date'])); ?> - 
                                            <?php echo date('M j', strtotime($booking['check_out_date'])); ?>
                                        </small>
                                    </div>
                                    <span class="status-badge <?php echo strtolower($booking['status']); ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-secondary">#<?php echo $booking['booking_id']; ?></small>
                                    <strong style="color: var(--accent-blue);">
                                        ₱<?php echo number_format($booking['total_price'], 2); ?>
                                    </strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (empty($recent_bookings)): ?>
                            <div class="text-center p-4">
                                <i class="fas fa-inbox fa-3x mb-3" style="color: var(--text-secondary);"></i>
                                <p class="text-secondary">No recent bookings found</p>
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
    <script>
        window.statusLabels = <?php echo json_encode($pie_statuses); ?>;
        window.statusCounts = <?php echo json_encode($pie_counts); ?>;
        window.bookingsData = <?php echo json_encode($bar_data); ?>;
        window.revenueData = <?php echo json_encode($line_data); ?>;
    </script>
    <script src="../js/dashboard.js"></script>
</body>
</html>