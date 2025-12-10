<?php
session_start();
include '../components/config.php';
include '../controllers/dashboardData.php';
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
        :root {
            --bg-primary: #0a0e27;
            --bg-secondary: #141937;
            --bg-card: #1a1f3a;
            --accent-blue: #00d4ff;
            --accent-cyan: #00bcd4;
            --accent-purple: #6b5ce7;
            --text-primary: #ffffff;
            --text-secondary: #8b92b8;
            --success: #00ff88;
            --warning: #ffb800;
            --danger: #ff4757;
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .main-content {
            padding: 2rem;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--bg-card) 0%, rgba(26, 31, 58, 0.8) 100%);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--accent-blue), var(--accent-cyan));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: rgba(0, 212, 255, 0.3);
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.2);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-icon.blue { background: rgba(0, 212, 255, 0.1); color: var(--accent-blue); }
        .stat-icon.green { background: rgba(0, 255, 136, 0.1); color: var(--success); }
        .stat-icon.yellow { background: rgba(255, 184, 0, 0.1); color: var(--warning); }
        .stat-icon.purple { background: rgba(107, 92, 231, 0.1); color: var(--accent-purple); }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.5rem 0;
            background: linear-gradient(135deg, var(--text-primary) 0%, var(--accent-blue) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-change {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            margin-top: 0.5rem;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
        }

        .stat-change.positive {
            background: rgba(0, 255, 136, 0.1);
            color: var(--success);
        }

        .stat-change.negative {
            background: rgba(255, 71, 87, 0.1);
            color: var(--danger);
        }

        /* Chart Containers */
        .chart-container {
            background: linear-gradient(135deg, var(--bg-card) 0%, rgba(26, 31, 58, 0.8) 100%);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .chart-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .chart-filters {
            display: flex;
            gap: 0.5rem;
        }

        .filter-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-secondary);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn:hover, .filter-btn.active {
            background: var(--accent-blue);
            color: var(--text-primary);
            border-color: var(--accent-blue);
        }

        /* Recent Bookings */
        .booking-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            transition: all 0.3s ease;
        }

        .booking-item:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(0, 212, 255, 0.3);
            transform: translateX(5px);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.confirmed { background: rgba(0, 255, 136, 0.1); color: var(--success); }
        .status-badge.pending { background: rgba(255, 184, 0, 0.1); color: var(--warning); }
        .status-badge.cancelled { background: rgba(255, 71, 87, 0.1); color: var(--danger); }
        .status-badge.completed { background: rgba(0, 212, 255, 0.1); color: var(--accent-cyan); }

        /* Circular Progress */
        .circular-progress {
            position: relative;
            width: 200px;
            height: 200px;
            margin: 2rem auto;
        }

        .progress-circle {
            transform: rotate(-90deg);
        }

        .progress-circle circle {
            fill: none;
            stroke-width: 10;
            stroke-linecap: round;
        }

        .progress-bg {
            stroke: rgba(255, 255, 255, 0.05);
        }

        .progress-bar {
            stroke: var(--accent-blue);
            stroke-dasharray: 565;
            stroke-dashoffset: 565;
            animation: progress 2s ease-out forwards;
        }

        @keyframes progress {
            to { stroke-dashoffset: 0; }
        }

        .progress-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .progress-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--accent-blue);
        }

        .progress-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        /* Action Button */
        .btn-generate {
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.3);
        }

        /* Scrollbar */
        .recent-bookings {
            max-height: 500px;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .recent-bookings::-webkit-scrollbar {
            width: 6px;
        }

        .recent-bookings::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        .recent-bookings::-webkit-scrollbar-thumb {
            background: var(--accent-blue);
            border-radius: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
            
            .stat-value {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>

    <?php include '../components/header_admin.php'; ?>
    
    <div class="container-fluid main-content">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Dashboard Analytics</h2>
                <p class="text-secondary mb-0">Welcome back! Here's what's happening today.</p>
            </div>
            <button class="btn btn-generate">
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
                <div class="stat-value">₱<?php echo number_format($stats['total_revenue'], 2); ?></div>
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
    <script src="../js/darkTheme.js"></script>
    
    <script>
        // Animate stat values on load
        document.addEventListener('DOMContentLoaded', function() {
            const statValues = document.querySelectorAll('.stat-value');
            statValues.forEach(stat => {
                const target = parseInt(stat.getAttribute('data-target') || stat.textContent.replace(/[^0-9]/g, ''));
                if (!isNaN(target)) {
                    animateValue(stat, 0, target, 2000);
                }
            });
        });

        function animateValue(element, start, end, duration) {
            const range = end - start;
            const increment = range / (duration / 16);
            let current = start;
            const timer = setInterval(() => {
                current += increment;
                if (current >= end) {
                    current = end;
                    clearInterval(timer);
                }
                const formatted = Math.floor(current).toLocaleString();
                if (element.textContent.includes('₱')) {
                    element.textContent = '₱' + formatted + '.00';
                } else {
                    element.textContent = formatted;
                }
            }, 16);
        }

        function animateCard(card) {
            card.style.transform = 'scale(0.95)';
            setTimeout(() => {
                card.style.transform = 'translateY(-5px)';
            }, 100);
        }

        function setTimeFilter(btn, period) {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            // Add your filter logic here
            console.log('Filter set to:', period);
        }

        // Chart Data
        const statusLabels = <?php echo json_encode($pie_statuses); ?>;
        const statusCounts = <?php echo json_encode($pie_counts); ?>;
        const bookingsData = <?php echo json_encode($bar_data); ?>;
        const revenueData = <?php echo json_encode($line_data); ?>;

        // Chart.js default settings for dark theme
        Chart.defaults.color = '#8b92b8';
        Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.05)';

        // Booking Status Doughnut Chart
        <?php if (count($pie_statuses) > 0): ?>
        const statusChart = new Chart(document.getElementById("statusChart"), {
            type: "doughnut",
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusCounts,
                    backgroundColor: [
                        'rgba(0, 212, 255, 0.8)',
                        'rgba(0, 255, 136, 0.8)',
                        'rgba(255, 184, 0, 0.8)',
                        'rgba(255, 71, 87, 0.8)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            color: '#8b92b8',
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                },
                cutout: '70%'
            }
        });
        <?php endif; ?>

        // Monthly Bookings Bar Chart
        const bookingsChart = new Chart(document.getElementById("bookingsChart"), {
            type: "bar",
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                    label: "Bookings",
                    data: bookingsData,
                    backgroundColor: (context) => {
                        const gradient = context.chart.ctx.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, 'rgba(0, 212, 255, 0.8)');
                        gradient.addColorStop(1, 'rgba(107, 92, 231, 0.8)');
                        return gradient;
                    },
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#8b92b8'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)',
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            color: '#8b92b8'
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Monthly Revenue Line Chart
        const revenueChart = new Chart(document.getElementById("revenueChart"), {
            type: "line",
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                    label: "Revenue (₱)",
                    data: revenueData,
                    borderColor: "#00d4ff",
                    backgroundColor: (context) => {
                        const gradient = context.chart.ctx.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, 'rgba(0, 212, 255, 0.2)');
                        gradient.addColorStop(1, 'rgba(0, 212, 255, 0)');
                        return gradient;
                    },
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: "#00d4ff",
                    pointBorderColor: "#0a0e27",
                    pointBorderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            },
                            color: '#8b92b8'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)',
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            color: '#8b92b8'
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(26, 31, 58, 0.95)',
                        titleColor: '#ffffff',
                        bodyColor: '#8b92b8',
                        borderColor: 'rgba(0, 212, 255, 0.5)',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
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
            bookingsChart.resize();
            revenueChart.resize();
        });
    </script>
</body>
</html>