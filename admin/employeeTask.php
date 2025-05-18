<?php
session_start();
include '../components/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Fetch all tasks with staff information
$query = "SELECT t.*, s.name as staff_name 
          FROM tasks t 
          LEFT JOIN staffs s ON t.staff_id = s.staff_id 
          ORDER BY t.created_at DESC";
$result = $con->query($query);

// Fetch all staff members for dropdown
$staff_query = "SELECT staff_id, name FROM staffs";
$staff_result = $con->query($staff_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Description - <?php include '../components/title.php'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8y+4g5e5c5e5c5e5c5e5c5e5c5e5c5e5c5e5c5e" crossorigin="anonymous" />
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../css/notifications.css">
    <link rel="shortcut icon" href="../images/final.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/darkTheme.css">
</head>
<body>
    <?php include '../components/header_admin.php';?>

    <div class="container mt-4">
        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="card-title text-muted">Employee Tasks</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                <i class="fas fa-plus"></i> Add Task
            </button>
        </div>

        <!-- Tasks Table -->
        <table class="table table-bordered">
            <thead class="table-light text-muted">
                <tr>
                    <th>ID</th>
                    <th>Staff Name</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['staff_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo $row['deadline']; ?></td>
                        <td>
                            <span class="badge bg-<?php echo $row['status'] == 'Completed' ? 'success' : 
                                ($row['status'] == 'In Progress' ? 'warning' : 'danger'); ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" 
                                    data-bs-target="#editTaskModal<?php echo $row['id']; ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="../controllers/taskController.php?deleteTask=<?php echo $row['id']; ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this task?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>

                    <!-- Edit Task Modal -->
                    <div class="modal fade" id="editTaskModal<?php echo $row['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="../controllers/taskController.php" method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Task</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="task_id" value="<?php echo $row['id']; ?>">
                                        
                                        <div class="mb-3">
                                            <label>Staff</label>
                                            <select name="staff_id" class="form-control" required>
                                                <?php 
                                                $staff_result->data_seek(0);
                                                while ($staff = $staff_result->fetch_assoc()): 
                                                ?>
                                                    <option value="<?php echo $staff['staff_id']; ?>" 
                                                        <?php echo ($staff['staff_id'] == $row['staff_id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($staff['name']); ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label>Title</label>
                                            <input type="text" name="title" class="form-control" 
                                                   value="<?php echo htmlspecialchars($row['title']); ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label>Description</label>
                                            <textarea name="description" class="form-control" required><?php echo htmlspecialchars($row['description']); ?></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label>Deadline</label>
                                            <input type="datetime-local" name="deadline" class="form-control" 
                                                   value="<?php echo date('Y-m-d\TH:i', strtotime($row['deadline'])); ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label>Status</label>
                                            <select name="status" class="form-control" required>
                                                <option value="Pending" <?php echo $row['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="In Progress" <?php echo $row['status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                                <option value="Done" <?php echo $row['status'] == 'Done' ? 'selected' : ''; ?>>Done</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" name="updateTask" class="btn btn-primary">Update Task</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Add Task Modal -->
        <div class="modal fade" id="addTaskModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="../controllers/taskController.php" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Task</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Staff</label>
                                <select name="staff_id" class="form-control" required>
                                    <option value="">Select Staff</option>
                                    <?php 
                                    $staff_result->data_seek(0);
                                    while ($staff = $staff_result->fetch_assoc()): 
                                    ?>
                                        <option value="<?php echo $staff['staff_id']; ?>">
                                            <?php echo htmlspecialchars($staff['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Description</label>
                                <textarea name="description" class="form-control" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label>Deadline</label>
                                <input type="datetime-local" name="deadline" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="Pending">Pending</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="addTask" class="btn btn-primary">Add Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- External Js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <script src="../js/notifications.js"></script>
    <script src="../js/darkTheme.js"></script>
</body>
</html>