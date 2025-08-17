<?php
ob_start();
session_start();
include '../components/config.php';

header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    error_log("Notification API - Session data: " . print_r($_SESSION, true));

    // Check session
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not authenticated. Session user_id not found.');
    }

    $user_id = (int) $_SESSION['user_id'];
    $action = $_GET['action'] ?? 'fetch';
    error_log("Notification API - User ID: $user_id, Action: $action");

    switch ($action) {
        case 'fetch':
            $check_table = "SHOW TABLES LIKE 'notifications'";
            $table_result = $con->query($check_table);

            if ($table_result->num_rows == 0) {
                ob_clean();
                echo json_encode([
                    'success' => true,
                    'notifications' => [],
                    'unread_count' => 0,
                    'debug_message' => 'Notifications table does not exist'
                ]);
                exit;
            }

            $sql = "SELECT 
                        notification_id,
                        booking_id,
                        title,
                        message,
                        type,
                        is_read,
                        created_at
                    FROM notifications 
                    WHERE user_id = ? 
                    ORDER BY created_at DESC 
                    LIMIT 20";

            $stmt = $con->prepare($sql);
            if (!$stmt) {
                throw new Exception('Database prepare error: ' . $con->error);
            }

            $stmt->bind_param("i", $user_id);
            if (!$stmt->execute()) {
                throw new Exception('Database execute error: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $notifications = [];
            $unread_count = 0;

            while ($row = $result->fetch_assoc()) {
                $row['time_ago'] = timeAgo($row['created_at']);
                $notifications[] = $row;

                if (!$row['is_read']) {
                    $unread_count++;
                }
            }

            error_log("Notification API - Found " . count($notifications) . " notifications for user $user_id");

            ob_clean();
            echo json_encode([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unread_count
            ]);
            exit;

        case 'mark_read':
            $notification_id = $_POST['notification_id'] ?? null;
            if (!$notification_id) {
                throw new Exception('Notification ID required');
            }

            $sql = "UPDATE notifications 
                    SET is_read = 1, updated_at = NOW() 
                    WHERE notification_id = ? AND user_id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ii", $notification_id, $user_id);

            if ($stmt->execute()) {
                ob_clean();
                echo json_encode(['success' => true, 'message' => 'Notification marked as read']);
            } else {
                throw new Exception('Failed to update notification');
            }
            exit;

        case 'mark_all_read':
            $sql = "UPDATE notifications 
                    SET is_read = 1, updated_at = NOW() 
                    WHERE user_id = ? AND is_read = 0";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("i", $user_id);

            if ($stmt->execute()) {
                ob_clean();
                echo json_encode(['success' => true, 'message' => 'All notifications marked as read']);
            } else {
                throw new Exception('Failed to update notifications');
            }
            exit;

        case 'get_count':
            $sql = "SELECT COUNT(*) as unread_count 
                    FROM notifications 
                    WHERE user_id = ? AND is_read = 0";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            ob_clean();
            echo json_encode([
                'success' => true,
                'unread_count' => (int) $row['unread_count']
            ]);
            exit;

        default:
            throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    // Catch any errors and return JSON
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}

// Helper: Convert timestamp to "time ago"
function timeAgo($timestamp) {
    $time = time() - strtotime($timestamp);
    if ($time < 60) {
        return 'just now';
    } elseif ($time < 3600) {
        $minutes = floor($time / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($time < 86400) {
        $hours = floor($time / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($time < 2592000) {
        $days = floor($time / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', strtotime($timestamp));
    }
}
?>