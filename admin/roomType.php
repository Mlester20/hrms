<?php
session_start();
include '../components/config.php';

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Fetch all room types
$query = "SELECT * FROM room_type";
$result = $con->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Type - <?php include '../components/title.php'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8y+4g5e5c5e5c5e5c5e5c5e5c5e5c5e5c5e" crossorigin="anonymous" />
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <!-- Header admin component -->
    <?php include '../components/header_admin.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-center text-muted">Room Types</h3>
            <!-- Button to trigger Add Modal -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomTypeModal">
                <i class="fas fa-plus"></i> Add Room Type
            </button>
        </div>

        <!-- Room Types Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Detail</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['detail']); ?></td>
                        <td>
                            <!-- Edit Button -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editRoomTypeModal<?php echo $row['id']; ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <!-- Delete Button -->
                            <a href="../controllers/roomTypeController.php?deleteRoomType=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this room type?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>

                    <!-- Edit Room Type Modal -->
                    <div class="modal fade" id="editRoomTypeModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="editRoomTypeModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="../controllers/roomTypeController.php" method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editRoomTypeModalLabel<?php echo $row['id']; ?>">Edit Room Type</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Title</label>
                                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="detail" class="form-label">Detail</label>
                                            <textarea class="form-control" id="detail" name="detail" rows="3" required><?php echo htmlspecialchars($row['detail']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="updateRoomType">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Room Type Modal -->
    <div class="modal fade" id="addRoomTypeModal" tabindex="-1" aria-labelledby="addRoomTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="../controllers/roomTypeController.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addRoomTypeModalLabel">Add Room Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="detail" class="form-label">Detail</label>
                            <textarea class="form-control" id="detail" name="detail" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="addRoomType">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>