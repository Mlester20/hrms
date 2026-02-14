<?php

require_once '../controllers/manageTablesController.php';
require_once '../includes/flash.php';
require_once '../middleware/authMiddleware.php';
requireAdmin();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tables -  <?php include '../components/title.php'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8y+4g5e5c5e5c5e5c5e5c5e5c5e5c5e5c5e5c5e" crossorigin="anonymous" />
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../css/notifications.css">
    <link rel="shortcut icon" href="../images/final.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/app.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include '../components/header_admin.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="card-title text-muted">Manage Restaurant Tables</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTableModal">
                <i class="fas fa-plus"></i> Add New Table
            </button>
        </div>

        <?php showFlash(); ?>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table">
                    <tr>
                        <th>Table Number</th>
                        <th>Capacity</th>
                        <th>Location</th>
                        <th>Position X</th>
                        <th>Position Y</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allTables as $table): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($table['table_number']); ?></td>
                            <td><?php echo htmlspecialchars($table['capacity']); ?> persons</td>
                            <td><?php echo htmlspecialchars($table['location']); ?></td>
                            <td><?php echo htmlspecialchars($table['position_x']); ?></td>
                            <td><?php echo htmlspecialchars($table['position_y']); ?></td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-outline-primary edit-table" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editTableModal"
                                        data-table='<?php echo json_encode($table); ?>'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-table"
                                        data-table-id="<?php echo $table['table_id']; ?>"
                                        data-table-number="<?php echo $table['table_number']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Table Modal -->
    <div class="modal fade" id="addTableModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content card">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Table</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../controllers/manageTablesController.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Table Number</label>
                            <input type="number" name="table_number" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Capacity</label>
                            <input type="number" name="capacity" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Location</label>
                            <input type="text" name="location" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Position X</label>
                                <input type="number" name="position_x" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Position Y</label>
                                <input type="number" name="position_y" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_table" class="btn btn-primary">Add Table</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Table Modal -->
    <div class="modal fade" id="editTableModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content card">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Table</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../controllers/manageTablesController.php" method="POST">
                    <input type="hidden" name="table_id" id="edit_table_id">
                    <div class="modal-body">
                        <!-- Same fields as add modal -->
                        <div class="mb-3">
                            <label>Table Number</label>
                            <input type="number" name="table_number" id="edit_table_number" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Capacity</label>
                            <input type="number" name="capacity" id="edit_capacity" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Location</label>
                            <input type="text" name="location" id="edit_location" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Position X</label>
                                <input type="number" name="position_x" id="edit_position_x" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Position Y</label>
                                <input type="number" name="position_y" id="edit_position_y" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_table" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteTableModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content card">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete Table #<span id="delete_table_number"></span>?
                </div>
                <div class="modal-footer">
                    <form action="../controllers/manageTablesController.php" method="POST">
                        <input type="hidden" name="table_id" id="delete_table_id">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete_table" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <!-- Custom Scripts -->
    <script src="../js/tables.js"></script>
    <script src="../js/notifications.js"></script>
    <script src="../js/darkTheme.js"></script>
</body>
</html>