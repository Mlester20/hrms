<?php

    class dashboardModel{
        public function getStatistics($con){
                try{
                    $pie_query = "SELECT status, COUNT(*) as count FROM bookings GROUP BY status";
                    $pie_stmt = $con->prepare($pie_query);
                    $pie_stmt->execute();
                    $pie_result = $pie_stmt->get_result();

                    $pie_statuses = [];
                    $pie_counts = [];
                    while ($row = $pie_result->fetch_assoc()) {
                        $pie_statuses[] = ucfirst($row['status']);
                        $pie_counts[] = (int)$row['count'];
                    }
                    $pie_stmt->close();

                    $bar_query = "SELECT MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count 
                                FROM bookings 
                                WHERE YEAR(created_at) = YEAR(CURDATE())
                                GROUP BY YEAR(created_at), MONTH(created_at)
                                ORDER BY month";
                    $bar_stmt = $con->prepare($bar_query);
                    $bar_stmt->execute();
                    $bar_result = $bar_stmt->get_result();


                    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    $bar_data = array_fill(0, 12, 0);

                    while ($row = $bar_result->fetch_assoc()) {
                        $bar_data[$row['month'] - 1] = (int)$row['count'];
                    }
                    $bar_stmt->close();

                    $line_query = "SELECT MONTH(created_at) as month, YEAR(created_at) as year, SUM(total_price) as monthly_revenue 
                                FROM bookings 
                                WHERE YEAR(created_at) = YEAR(CURDATE()) AND status != 'canceled'
                                GROUP BY YEAR(created_at), MONTH(created_at)
                                ORDER BY month";
                    $line_stmt = $con->prepare($line_query);
                    $line_stmt->execute();
                    $line_result = $line_stmt->get_result();

                    $line_data = array_fill(0, 12, 0);
                    while ($row = $line_result->fetch_assoc()) {
                        $line_data[$row['month'] - 1] = round((float)$row['monthly_revenue'] / 100, 2);
                    }
                    $line_stmt->close();


                    $payment_query = "SELECT payment_status, COUNT(*) as count FROM bookings 
                                    WHERE status != 'canceled' 
                                    GROUP BY payment_status 
                                    ORDER BY count DESC";
                    $payment_stmt = $con->prepare($payment_query);
                    $payment_stmt->execute();
                    $payment_result = $payment_stmt->get_result();

                    $payment_statuses = [];
                    $payment_counts = [];
                    while ($row = $payment_result->fetch_assoc()) {
                        $payment_statuses[] = ucfirst($row['payment_status'] ?: 'Pending');
                        $payment_counts[] = (int)$row['count'];
                    }
                    $payment_stmt->close();

                    $stats_query = "SELECT 
                                    COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_bookings,
                                    COUNT(DISTINCT user_id) as total_users,
                                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_tables,
                                    SUM(CASE WHEN status != 'canceled' THEN total_price ELSE 0 END) as total_revenue,
                                    COUNT(*) as total_bookings,
                                    AVG(CASE WHEN status != 'canceled' THEN total_price END) as avg_booking_value
                                    FROM bookings";
                    $stats_stmt = $con->prepare($stats_query);
                    $stats_stmt->execute();
                    $stats_result = $stats_stmt->get_result();
                    $stats = $stats_result->fetch_assoc();
                    $stats_stmt->close();

                    $recent_query = "SELECT b.booking_id, b.total_price, b.status, b.created_at, b.check_in_date, b.check_out_date,
                                            u.name as customer_name, r.id as room_id
                                    FROM bookings b 
                                    LEFT JOIN users u ON b.user_id = u.user_id 
                                    LEFT JOIN rooms r ON b.room_id = r.id 
                                    ORDER BY b.created_at DESC 
                                    LIMIT 5";
                    $recent_stmt = $con->prepare($recent_query);
                    $recent_stmt->execute();
                    $recent_result = $recent_stmt->get_result();
                    $recent_bookings = $recent_result->fetch_all(MYSQLI_ASSOC);
                    $recent_stmt->close();
                    return [
                        'pie_statuses' => $pie_statuses,
                        'pie_counts' => $pie_counts,
                        'bar_data' => $bar_data,
                        'line_data' => $line_data,
                        'payment_statuses' => $payment_statuses,
                        'payment_counts' => $payment_counts,
                        'stats' => $stats,
                        'recent_bookings' => $recent_bookings
                    ];

                }catch( Exception $e ){
                    throw new Exception("Database Error" .$e->getMessage(), 500);
                }
        }

        public function getChartData($con){
                try{
                    $pie_query = "SELECT status, COUNT(*) as count FROM bookings GROUP BY status";
                    $pie_stmt = $con->prepare($pie_query);
                    $pie_stmt->execute();
                    $pie_result = $pie_stmt->get_result();

                    $pie_statuses = [];
                    $pie_counts = [];
                    while ($row = $pie_result->fetch_assoc()) {
                        $pie_statuses[] = ucfirst($row['status']);
                        $pie_counts[] = (int)$row['count'];
                    }
                    $pie_stmt->close();

                    $bar_query = "SELECT MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count 
                                FROM bookings 
                                WHERE YEAR(created_at) = YEAR(CURDATE())
                                GROUP BY YEAR(created_at), MONTH(created_at)
                                ORDER BY month";
                    $bar_stmt = $con->prepare($bar_query);
                    $bar_stmt->execute();
                    $bar_result = $bar_stmt->get_result();

                    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    $bar_data = array_fill(0, 12, 0);

                    while ($row = $bar_result->fetch_assoc()) {
                        $bar_data[$row['month'] - 1] = (int)$row['count'];
                    }
                    $bar_stmt->close();

                    $line_query = "SELECT MONTH(created_at) as month, YEAR(created_at) as year, SUM(total_price) as monthly_revenue 
                                FROM bookings 
                                WHERE YEAR(created_at) = YEAR(CURDATE()) AND status != 'canceled'
                                GROUP BY YEAR(created_at), MONTH(created_at)
                                ORDER BY month";
                    $line_stmt = $con->prepare($line_query);
                    $line_stmt->execute();
                    $line_result = $line_stmt->get_result();

                    $line_data = array_fill(0, 12, 0);
                    while ($row = $line_result->fetch_assoc()) {
                        $line_data[$row['month'] - 1] = round((float)$row['monthly_revenue'] / 100, 2);
                    }
                    $line_stmt->close();

                    return [$pie_statuses, $pie_counts, $bar_data, $line_data];
                }catch( Exception $e ){
                    throw new Exception("Database Error" .$e->getMessage(), 500);
                }
        }
    }

?>