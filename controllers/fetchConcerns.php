<?php
session_start();
include '../components/config.php';


// Handle bulk delete
if (isset($_POST['bulk_delete']) && isset($_POST['selected_concerns'])) {
    try {
        $selected_ids = array_map('intval', $_POST['selected_concerns']);
        $ids = implode(',', $selected_ids);
        
        $delete_query = "DELETE FROM concerns WHERE id IN ($ids)";
        if (mysqli_query($con, $delete_query)) {
            $_SESSION['success'] = "Selected concerns have been deleted successfully.";
        } else {
            throw new Exception("Error deleting concerns: " . mysqli_error($con));
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    header('Location: ../admin/concerns.php');
    exit();
}

//perform query to fetch all concerns
try {
    // Fetch concerns with user details using JOIN
    $query = "SELECT 
        c.id,
        c.subject,
        c.message,
        c.created_at,
        u.name,
        u.email,
        u.address
    FROM concerns c
    LEFT JOIN users u ON c.user_id = u.user_id
    ORDER BY c.created_at DESC";

    $result = mysqli_query($con, $query);
    
    if (!$result) {
        throw new Exception("Database error: " . mysqli_error($con));
    }

    $concerns = mysqli_fetch_all($result, MYSQLI_ASSOC);

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    $concerns = [];
}
?>