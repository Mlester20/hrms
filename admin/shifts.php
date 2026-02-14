<?php

require_once '../controllers/shiftsController.php';
require_once '../includes/flash.php';
require_once '../middleware/authMiddleware.php';
requireAdmin();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shifts - <?php include '../components/title.php'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-center text-muted">Shifts</h3>
            <!-- Button to trigger Add Modal -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addShiftModal">
                <i class="fas fa-plus"></i> Add Shift
            </button>
        </div>

        <?php showFlash(); ?>

        <!-- Shifts Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Staff Name</th>
                    <th>Position</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Date Start</th>
                    <th>Date End</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($shifts as $row): ?>
                    <tr>
                        <td><?php echo $row['shift_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['position']); ?></td>
                        <td><?php echo htmlspecialchars($row['start_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['end_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['date_start']); ?></td>
                        <td><?php echo htmlspecialchars($row['date_end']); ?></td>
                        <td><?php echo ucfirst($row['status']); ?></td>
                        <td>
                            <!-- Mark as Done Button -->
                            <?php if ($row['status'] === 'pending'): ?>
                                <a href="../controllers/shiftsController.php?markDone=<?php echo $row['shift_id']; ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-check"></i> Mark as Done
                                </a>
                            <?php endif; ?>
                            <!-- Edit Button -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editShiftModal<?php echo $row['shift_id']; ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <!-- Delete Button -->
                            <a href="../controllers/shiftsController.php?deleteShift=<?php echo $row['shift_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this shift?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>

                    <!-- Edit Shift Modal -->
                    <div class="modal fade" id="editShiftModal<?php echo $row['shift_id']; ?>" tabindex="-1" aria-labelledby="editShiftModalLabel<?php echo $row['shift_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content card">
                                <form action="../controllers/shiftsController.php" method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editShiftModalLabel<?php echo $row['shift_id']; ?>">Edit Shift</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="shift_id" value="<?php echo $row['shift_id']; ?>">
                                        <div class="mb-3">
                                            <label for="staff_id" class="form-label">Staff</label>
                                            <select class="form-control" id="staff_id" name="staff_id" required>
                                                <?php foreach ($staffResult as $staff): ?>
                                                    <option class="card" value="<?php echo $staff['staff_id']; ?>" <?php echo $staff['staff_id'] == $row['staff_id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($staff['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="start_time" class="form-label">Start Time</label>
                                            <input type="time" class="form-control" id="start_time" name="start_time" value="<?php echo htmlspecialchars($row['start_time']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="end_time" class="form-label">End Time</label>
                                            <input type="time" class="form-control" id="end_time" name="end_time" value="<?php echo htmlspecialchars($row['end_time']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="date_start" class="form-label">Date Start</label>
                                            <input type="date" class="form-control" id="date_start" name="date_start" value="<?php echo htmlspecialchars($row['date_start']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="date_end" class="form-label">Date End</label>
                                            <input type="date" class="form-control" id="date_end" name="date_end" value="<?php echo htmlspecialchars($row['date_end']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="updateShift">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Shift Modal -->
    <div class="modal fade" id="addShiftModal" tabindex="-1" aria-labelledby="addShiftModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content card">
                <form action="../controllers/shiftsController.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addShiftModalLabel">Add Shift</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="staff_id" class="form-label">Staff</label>
                            <select class="form-control" id="staff_id" name="staff_id" required>
                                <?php foreach ($staffResult as $staff): ?>
                                    <option class="card" value="<?php echo $staff['staff_id']; ?>"><?php echo htmlspecialchars($staff['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="start_time" class="form-label">Start Time</label>
                            <input type="time" class="form-control" id="start_time" name="start_time" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_time" class="form-label">End Time</label>
                            <input type="time" class="form-control" id="end_time" name="end_time" required>
                        </div>
                        <div class="mb-3">
                            <label for="date_start" class="form-label">Date Start</label>
                            <input type="date" class="form-control" id="date_start" name="date_start" required>
                        </div>
                        <div class="mb-3">
                            <label for="date_end" class="form-label">Date End</label>
                            <input type="date" class="form-control" id="date_end" name="date_end" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="addShift">Add Shift</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <script src="../js/notifications.js"></script>
    <script src="../js/darkTheme.js"></script>
</body>
</html>