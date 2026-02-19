<?php
require_once '../controllers/roomsController.php';
require_once '../middleware/authMiddleware.php';
require_once '../includes/flash.php';

requireAdmin();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms - <?php include '../includes/title.php'; ?></title>
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
    <!-- Header admin component -->
    <?php include '../components/header_admin.php'; ?>

    <div class="container-fluid mt-4" id="mainContent">
        
            <?php showFlash(); ?>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-center">Rooms</h3>
                <!-- Button to trigger Add Modal -->
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                    <i class="fas fa-plus"></i> Add Room
                </button>
            </div>

            <!-- Rooms Table -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Room Type</th>
                        <th>Images</th>
                        <th>Price</th>
                        <th>Room Freebies</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['room_type_title']); ?></td>
                            
                            <td>
                                <?php
                                $imagesArray = json_decode($row['images'], true);
                                if (!empty($imagesArray)) {
                                    echo '<img src="../uploads/' . htmlspecialchars($imagesArray[0]) . '" alt="Room Image" style="width: 100px; height: auto;">';
                                } else {
                                    echo 'No image available';
                                }
                                ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($row['price']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($row['includes'])?>
                            </td>
                            <td>
                                <!-- Edit Button -->
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editRoomModal<?php echo $row['id']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <!-- Delete Button -->
                                <a href="../controllers/roomsController.php?deleteRoom=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this room?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Edit Room Modal -->
                        <div class="modal fade" id="editRoomModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="editRoomModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content card">
                                    <form action="../controllers/roomsController.php" method="POST" enctype="multipart/form-data">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editRoomModalLabel<?php echo $row['id']; ?>">Edit Room</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <div class="mb-3">
                                                <label for="title" class="form-label">Title</label>
                                                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="room_type_id" class="form-label">Room Type</label>
                                                <select class="form-control" id="room_type_id" name="room_type_id" required>
                                                    <?php 
                                                    $roomTypesResult2 = $roomModel->getAllRoomTypes();
                                                    while ($roomType = $roomTypesResult2->fetch_assoc()): 
                                                    ?>
                                                        <option class="card" value="<?php echo $roomType['id']; ?>" <?php echo $roomType['id'] == $row['room_type_id'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($roomType['title']); ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="images" class="form-label">Images (Leave empty to keep current images)</label>
                                                <input type="file" class="form-control" id="images" name="images[]" multiple>
                                            </div>
                                            <div class="mb-3">
                                                <label for="price" class="form-label">Price</label>
                                                <input type="text" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($row['price']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary" name="updateRoom">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </tbody>
            </table>

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
        </div>
    </div>

    <!-- Add Room Modal -->
    <div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content card">
                <form action="../controllers/roomsController.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addRoomModalLabel">Add Room</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="room_type_id" class="form-label">Room Type</label>
                            <select class="form-control" id="room_type_id" name="room_type_id" required>
                                <?php 
                                $roomTypesResult3 = $roomModel->getAllRoomTypes();
                                while ($roomType = $roomTypesResult3->fetch_assoc()): 
                                ?>
                                    <option class="card" value="<?php echo $roomType['id']; ?>"><?php echo htmlspecialchars($roomType['title']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="images" class="form-label">Images</label>
                            <input type="file" class="form-control" id="images" name="images[]" multiple required>
                        </div>
                        <div class="mb-3">
                            <label for="package" class="form-label">Package Name</label>
                            <input type="text" class="form-control" id="package" name="package_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="text" class="form-control" id="price" name="price" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="addRoom">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <script src="../js/notifications.js"></script>

</body>
</html>