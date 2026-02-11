<?php

require_once '../controllers/restaurantMenuController.php';
require_once '../includes/flash.php';
require_once '../middleware/auth.php';

requireAdmin();

$error = null;
$edit_menu = null;

// Handle Add Menu
if (isset($_POST['add_menu'])) {
    try {
        $success_message = handleAddMenu($con, $restaurantMenuModel);
        $_SESSION['success'] = $success_message;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle Update Menu
if (isset($_POST['update_menu'])) {
    try {
        $success_message = handleUpdateMenu($con, $restaurantMenuModel);
        $_SESSION['success'] = $success_message;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle Delete Menu
if (isset($_POST['delete_menu'])) {
    try {
        $success_message = handleDeleteMenu($con, $restaurantMenuModel);
        $_SESSION['success'] = $success_message;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}

// Get menu item for editing
if(isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_menu = $restaurantMenuModel->getMenuById($con, $_GET['edit']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menus | <?php include '../components/title.php'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="../css/notifications.css">
    <link rel="shortcut icon" href="../images/final.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/app.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    
    <?php include '../components/header_admin.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h4 class="mb-4 text-center mt-4">Restaurant Menu Management</h4>
            </div>
        </div>

        <?php showFlash(); ?>
        
        <div class="row">
            <div class="col-md-4">
                <div class="menu-form">
                    <h3><?php echo $edit_menu ? 'Edit Menu Item' : 'Add New Menu Item'; ?></h3>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                        <?php if($edit_menu): ?>
                            <input type="hidden" name="menu_id" value="<?php echo $edit_menu['menu_id']; ?>">
                            <input type="hidden" name="current_image" value="<?php echo $edit_menu['image']; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="menu_name" class="form-label">Menu Name</label>
                            <input type="text" class="form-control" id="menu_name" name="menu_name" value="<?php echo $edit_menu ? $edit_menu['menu_name'] : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="menu_description" class="form-label">Description</label>
                            <textarea class="form-control" id="menu_description" name="menu_description" rows="3" required><?php echo $edit_menu ? $edit_menu['menu_description'] : ''; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo $edit_menu ? $edit_menu['price'] : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(this);" <?php echo $edit_menu ? '' : 'required'; ?>>
                            <small class="form-text text-muted">Supported formats: JPG, JPEG, PNG, WEBP</small>
                            
                            <div id="imagePreview" class="mt-2">
                                <?php if($edit_menu && $edit_menu['image']): ?>
                                <img src="../uploads/<?php echo $edit_menu['image']; ?>" class="preview-image" alt="Current menu image">
                                <p class="mt-1">Current image</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if($edit_menu): ?>
                            <button type="submit" name="update_menu" class="btn btn-primary">Update Menu Item</button>
                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">Cancel</a>
                        <?php else: ?>
                            <button type="submit" name="add_menu" class="btn btn-success">Add Menu Item</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title text-center">Menu Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($restaurantMenus) > 0): ?>
                                        <?php while($item = mysqli_fetch_assoc($restaurantMenus)): ?>
                                            <tr>
                                                <td><?php echo $item['menu_id']; ?></td>
                                                <td>
                                                    <?php if($item['image']): ?>
                                                        <img src="../uploads/<?php echo $item['image']; ?>" class="menu-img" alt="<?php echo $item['menu_name']; ?>">
                                                    <?php else: ?>
                                                        <span class="text-muted">No image</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $item['menu_name']; ?></td>
                                                <td><?php echo substr($item['menu_description'], 0, 50) . (strlen($item['menu_description']) > 50 ? '...' : ''); ?></td>
                                                <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                                <td>
                                                    <a href="<?php echo $_SERVER['PHP_SELF'] . '?edit=' . $item['menu_id']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $item['menu_id']; ?>">
                                                        <i class="fas fa-trash"></i> 
                                                    </button>
                                                    
                                                    <!-- Delete Confirmation Modal -->
                                                    <div class="modal fade" id="deleteModal<?php echo $item['menu_id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content card">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Are you sure you want to delete <strong><?php echo $item['menu_name']; ?></strong>?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                                                        <input type="hidden" name="menu_id" value="<?php echo $item['menu_id']; ?>">
                                                                        <button type="submit" name="delete_menu" class="btn btn-danger">Delete</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No menu items found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="../js/notifications.js"></script>
    <script src="../js/restaurantPreviewImage.js"></script>
</body>
</html>