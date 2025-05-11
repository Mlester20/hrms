<?php
session_start();
include '../components/config.php';

// Check if user is not logged in
if (!isset($_SESSION['staff_id'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Dashboard - <?php include '../components/title.php'; ?> </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="../css/tasks-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/staffsTable.css">
</head>
<body>
    <!-- header admin component -->
    <?php include '../components/staffsHeader.php'; ?>
    
    <div id="tasksContainer" class="container mt-4">
        <h4 class='text-center card-title text-muted'>Your Tasks</h4>
        
        <div class="filter-controls">
            <div></div> <!-- Empty div for flex spacing -->
            <div class="filter-dropdown">
                <select id="statusFilter" class="form-select form-select-sm">
                    <option value="all">All Tasks</option>
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="text-center text-muted">
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Requested Date</th>
                    </tr>
                </thead>
                <tbody id="tasksList">
                    <!-- Tasks will be loaded here -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- external scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <!-- custom js scripts -->
    <script src="../js/tasks.js"></script>
</body>
</html>