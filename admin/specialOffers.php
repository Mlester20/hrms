<?php
session_start();
include '../components/config.php';
include '../controllers/offersController.php';

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Get all offers
$offers = getAllOffers($con);

// Get offer for editing if ID is provided
$edit_offer = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_offer = getOfferById($con, $_GET['edit']);
    if (!$edit_offer) {
        $_SESSION['error_message'] = "Offer not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Offers - <?php include '../components/title.php'; ?> </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8y+4g5e5c5e5c5e5c5e5c5e5c5e5c5e5c5e5c5e" crossorigin="anonymous" />
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../css/notifications.css">
    <link rel="shortcut icon" href="../images/final.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/app.css">
</head>
<body>
    
    <?php include '../components/header_admin.php'; ?>

    <div class="container py-4">
        <div class="d-flex align-items-center mb-4 card p-3">
            <h3 class="text-white mb-0">Manage Offers</h3>

            <button
                type="button"
                class="btn btn-primary ms-auto"
                data-bs-toggle="modal"
                data-bs-target="#addOfferModal"
            >
                <i class="fas fa-plus"></i> Add New Offer
            </button>
        </div>
        <!-- Display messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                    echo $_SESSION['error_message']; 
                    unset($_SESSION['error_message']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Offers Table -->
        <div class="card shadow">
            <div class="card-body">
                <?php if (count($offers) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($offers as $offer): ?>
                                    <tr>
                                        <td><?php echo $offer['offers_id']; ?></td>
                                        <td>
                                            <?php if (!empty($offer['image'])): ?>
                                                <img src="../uploads/<?php echo $offer['image']; ?>" alt="<?php echo $offer['title']; ?>" style="width: 60px; height: 60px; object-fit: cover;" class="img-thumbnail">
                                            <?php else: ?>
                                                <span class="text-muted">No image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $offer['title']; ?></td>
                                        <td><?php echo strlen($offer['description']) > 50 ? substr($offer['description'], 0, 50) . '...' : $offer['description']; ?></td>
                                        <td>₱<?php echo number_format($offer['price'], 2); ?></td>
                                        <td>
                                            <div class="btn-group gap-2 border-0">
                                                <a href="?edit=<?php echo $offer['offers_id']; ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $offer['offers_id']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal<?php echo $offer['offers_id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content card">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete offer: <strong><?php echo $offer['title']; ?></strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="../controllers/process_offer.php" method="post">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="offers_id" value="<?php echo $offer['offers_id']; ?>">
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <p class="lead">No offers found. Start by adding a new offer.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Offer Modal -->
    <div class="modal fade" id="addOfferModal" tabindex="-1" aria-labelledby="addOfferModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content card">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOfferModalLabel">Add New Offer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../controllers/process_offer.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="price" class="form-label">Price (₱)</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <small class="text-muted">Recommended size: 800x600px, Max: 5MB</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Offer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Offer Modal -->
    <?php if ($edit_offer): ?>
    <div class="modal fade" id="editOfferModal" tabindex="-1" aria-labelledby="editOfferModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content card">
                <div class="modal-header">
                    <h5 class="modal-title" id="editOfferModalLabel">Edit Offer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../controllers/process_offer.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="offers_id" value="<?php echo $edit_offer['offers_id']; ?>">
                        
                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="edit_title" name="title" value="<?php echo $edit_offer['title']; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="4" required><?php echo $edit_offer['description']; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_price" class="form-label">Price (₱)</label>
                            <input type="number" class="form-control" id="edit_price" name="price" step="0.01" min="0" value="<?php echo $edit_offer['price']; ?>" required>
                        </div>
                        
                        <?php if (!empty($edit_offer['image'])): ?>
                            <div class="mb-3">
                                <label class="form-label">Current Image</label>
                                <div>
                                    <img src="../uploads/<?php echo $edit_offer['image']; ?>" alt="<?php echo $edit_offer['title']; ?>" style="max-width: 200px; max-height: 200px;" class="img-thumbnail">
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="edit_image" class="form-label">New Image <?php echo empty($edit_offer['image']) ? '(Required)' : '(Optional)'; ?></label>
                            <input type="file" class="form-control" id="edit_image" name="image" accept="image/*" <?php echo empty($edit_offer['image']) ? 'required' : ''; ?>>
                            <small class="text-muted">Recommended size: 800x600px, Max: 5MB</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Offer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Auto open edit modal if edit parameter is present -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var editModal = new bootstrap.Modal(document.getElementById('editOfferModal'));
            editModal.show();
        });
    </script>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <script src="../js/notifications.js"></script>
</body>
</html>