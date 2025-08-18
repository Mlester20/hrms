<?php
session_start();
include '../components/config.php';

// Throws a 401 if the user is not admin
if(!isset($_SESSION['user_id'])){
    header('Location: ../index.php');
    exit();
}

// Process user deletion
if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $user_id = mysqli_real_escape_string($con, $_GET['id']);
    
    // First check if the user has any reservations
    $check_reservations = "SELECT COUNT(*) as count FROM table_reservations WHERE user_id = '$user_id'";
    $reservation_result = mysqli_query($con, $check_reservations);
    $reservation_count = mysqli_fetch_assoc($reservation_result)['count'];
    
    if($reservation_count > 0) {
        // User has reservations, cannot delete
        $_SESSION['message'] = "Cannot delete user: This user has $reservation_count existing reservation(s). Please delete the reservations first.";
        $_SESSION['message_type'] = "warning";
    } else {
        // Safe to delete the user
        $delete_query = "DELETE FROM users WHERE user_id = '$user_id' AND role = 'user'";
        
        if(mysqli_query($con, $delete_query)) {
            $_SESSION['message'] = "User deleted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting user: " . mysqli_error($con);
            $_SESSION['message_type'] = "danger";
        }
    }
    
    // Redirect to avoid resubmission on refresh
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Process user edit
if(isset($_POST['edit_user'])) {
    $user_id = mysqli_real_escape_string($con, $_POST['user_id']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    
    $update_query = "UPDATE users SET 
                    name = '$name', 
                    email = '$email', 
                    phone = '$phone', 
                    address = '$address' 
                    WHERE user_id = '$user_id' AND role = 'user'";
    
    if(mysqli_query($con, $update_query)) {
        $_SESSION['message'] = "User updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating user: " . mysqli_error($con);
        $_SESSION['message_type'] = "danger";
    }
    
    // Redirect to avoid resubmission on refresh
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch user for editing
$edit_user = null;
if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $user_id = mysqli_real_escape_string($con, $_GET['id']);
    $edit_query = "SELECT * FROM users WHERE user_id = '$user_id' AND role = 'user'";
    $edit_result = mysqli_query($con, $edit_query);
    
    if($edit_result && mysqli_num_rows($edit_result) > 0) {
        $edit_user = mysqli_fetch_assoc($edit_result);
    }
}

// Pagination settings
$records_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total number of users
$total_query = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$total_result = mysqli_query($con, $total_query);
$total_rows = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_rows / $records_per_page);

// Fetch users with pagination
$query = "SELECT u.user_id, u.name, u.address, u.email, u.role, u.phone, 
         (SELECT COUNT(*) FROM table_reservations r WHERE r.user_id = u.user_id) as reservation_count 
         FROM users u WHERE u.role = 'user'
         LIMIT $offset, $records_per_page";
$result = mysqli_query($con, $query);

// Check if query was successful
if(!$result) {
    die("Query failed: " . mysqli_error($con));
}

// Store users in an array
$users = [];
while($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Manage Users | <?php include '../components/title.php'; ?> </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/notifications.css">
    <link rel="stylesheet" href="../css/darkTheme.css">
</head>
<body>
    
    <!-- header -->
    <?php include '../components/header_admin.php'; ?>

    <!-- Main Content -->
    <div class="container mt-5">
        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php 
                unset($_SESSION['message']); 
                unset($_SESSION['message_type']); 
            ?>
        <?php endif; ?>

        <!-- Edit User Modal -->
        <?php if($edit_user): ?>
        <div class="modal fade show" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                        <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn-close" aria-label="Close"></a>
                    </div>
                    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="user_id" value="<?= $edit_user['user_id'] ?>">
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($edit_user['name']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($edit_user['email']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($edit_user['phone']) ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($edit_user['address']) ?></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" name="edit_user" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="text-end col-md-4 mb-3">
                <input type="text" class="form-control form-control-sm mb-3" id="searchInput" placeholder="Search Users" onkeyup="filterTable()">
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-white">
                        <h4 class="mb-0 text-center text-muted">Manage Users</h4>
                    </div>
                    <div class="card-body">
                        <?php if(count($users) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="userTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Contact</th>
                                            <th>Address</th>
                                            <th>Role</th>
                                            <th>Reservations</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($users as $user): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                                <td><?php echo htmlspecialchars($user['address']); ?></td>
                                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                                <td>
                                                    <?php if($user['reservation_count'] > 0): ?>
                                                        <span class="badge bg-info"><?= $user['reservation_count'] ?> reservation(s)</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">None</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="<?= $_SERVER['PHP_SELF'] ?>?action=edit&id=<?= $user['user_id'] ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <?php if($user['reservation_count'] == 0): ?>
                                                        <a href="<?= $_SERVER['PHP_SELF'] ?>?action=delete&id=<?= $user['user_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-danger" disabled title="Cannot delete: User has active reservations">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo ($page-1); ?>" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo ($page+1); ?>" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php else: ?>
                            <div class="alert alert-info">No users found.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- js external scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/notifications.js"></script>
    <script src="../js/darkTheme.js"></script>
    <script src="../js/searchUsers.js"></script>
</body>
</html>