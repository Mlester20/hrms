<?php
session_start();

require '../controllers/descriptionController.php';
require_once '../middleware/authMiddleware.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Description - <?php include '../includes/title.php'; ?></title>
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

    <div class="container my-4">
        <h4 class="text-center card-title">Description</h4>
    </div>

    <div class="container mt-4">
        <table class="table table-bordered text-white">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Description Title</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

            <?php foreach ($result as $row): ?>
                <tr>
                    <td><?= $row['description_id'] ?></td>

                    <!-- Trimmed title -->
                    <td class="text-truncate" style="max-width:300px;">
                        <?= htmlspecialchars($row['description_name']) ?>
                    </td>

                    <td>
                        <!-- Edit Button -->
                        <button
                            class="btn btn-warning btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#editDescriptionModal<?= $row['description_id'] ?>">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>

                <!-- ✅ EDIT DESCRIPTION MODAL -->
                <div class="modal fade"
                    id="editDescriptionModal<?= $row['description_id'] ?>"
                    tabindex="-1"
                    aria-hidden="true">

                    <div class="modal-dialog">
                        <div class="modal-content card">
                            <form action="../controllers/descriptionController.php" method="POST">

                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Description</h5>
                                    <button type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <!-- Hidden ID -->
                                    <input type="hidden"
                                        name="description_id"
                                        value="<?= $row['description_id'] ?>">

                                    <div class="mb-3">
                                        <label class="form-label">Description Title</label>
                                        <input
                                            type="text"
                                            name="description_name"
                                            class="form-control"
                                            value="<?= htmlspecialchars($row['description_name']) ?>"
                                            required>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button"
                                            class="btn btn-secondary"
                                            data-bs-dismiss="modal">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                            name="updateDescription"
                                            class="btn btn-primary">
                                        Save Changes
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

            </tbody>
        </table>
    </div>
    

    <!-- External Js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <script src="../js/notifications.js"></script>
</body>
</html>