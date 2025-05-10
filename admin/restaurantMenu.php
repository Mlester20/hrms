<?php
session_start();
include '../components/config.php';

//check if user is logged in
if(!isset($_SESSION['user_id'])){
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create new menu item
    if (isset($_POST['add_menu'])) {
        $menu_name = mysqli_real_escape_string($con, $_POST['menu_name']);
        $menu_description = mysqli_real_escape_string($con, $_POST['menu_description']);
        $price = mysqli_real_escape_string($con, $_POST['price']);
        
        // Image upload handling
        $image = '';
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $filename = $_FILES['image']['name'];
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);
            
            // Verify file extension
            if(in_array(strtolower($filetype), $allowed)) {
                // Create unique filename
                $new_filename = uniqid() . '.' . $filetype;
                $upload_dir = '../uploads';
                
                // Create directory if it doesn't exist
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $upload_path = $upload_dir . $new_filename;
                
                // Upload the file
                if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = $new_filename;
                } else {
                    $error = "Failed to upload image";
                }
            } else {
                $error = "Invalid file type. Only JPG, JPEG, PNG and WEBP files are allowed.";
            }
        }

        // Insert menu item
        if(!isset($error)) {
            $sql = "INSERT INTO restaurant_menu (menu_name, menu_description, image, price) VALUES ('$menu_name', '$menu_description', '$image', '$price')";
            if (mysqli_query($con, $sql)) {
                $_SESSION['success'] = "Menu item added successfully";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $error = "Error: " . mysqli_error($con);
            }
        }
    }
    
    // Update menu item
    if (isset($_POST['update_menu'])) {
        $menu_id = mysqli_real_escape_string($con, $_POST['menu_id']);
        $menu_name = mysqli_real_escape_string($con, $_POST['menu_name']);
        $menu_description = mysqli_real_escape_string($con, $_POST['menu_description']);
        $price = mysqli_real_escape_string($con, $_POST['price']);
        $current_image = mysqli_real_escape_string($con, $_POST['current_image']);
        
        // Image upload handling - only if a new image is uploaded
        $image = $current_image; // Default to current image
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $filename = $_FILES['image']['name'];
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);
            
            // Verify file extension
            if(in_array(strtolower($filetype), $allowed)) {
                // Create unique filename
                $new_filename = uniqid() . '.' . $filetype;
                $upload_dir = '../uploads/menu/';
                
                // Create directory if it doesn't exist
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $upload_path = $upload_dir . $new_filename;
                
                // Upload the file
                if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = $new_filename;
                    
                    // Delete old image if it exists
                    if($current_image && file_exists('../uploads/menu/' . $current_image)) {
                        unlink('../uploads/menu/' . $current_image);
                    }
                } else {
                    $error = "Failed to upload image";
                }
            } else {
                $error = "Invalid file type. Only JPG, JPEG, PNG and WEBP files are allowed.";
            }
        }

        // Update menu item
        if(!isset($error)) {
            $sql = "UPDATE restaurant_menu SET menu_name='$menu_name', menu_description='$menu_description', image='$image', price='$price' WHERE menu_id=$menu_id";
            if (mysqli_query($con, $sql)) {
                $_SESSION['success'] = "Menu item updated successfully";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $error = "Error: " . mysqli_error($con);
            }
        }
    }
    
    // Delete menu item
    if (isset($_POST['delete_menu'])) {
        $menu_id = mysqli_real_escape_string($con, $_POST['menu_id']);
        
        // Get image filename before deleting the record
        $query = "SELECT image FROM restaurant_menu WHERE menu_id = $menu_id";
        $result = mysqli_query($con, $query);
        if($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $image = $row['image'];
            
            // Delete the record
            $sql = "DELETE FROM restaurant_menu WHERE menu_id=$menu_id";
            if (mysqli_query($con, $sql)) {
                // Delete the image file
                if($image && file_exists('../uploads/menu/' . $image)) {
                    unlink('../uploads/menu/' . $image);
                }
                $_SESSION['success'] = "Menu item deleted successfully";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $error = "Error: " . mysqli_error($con);
            }
        }
    }
}

$edit_menu = null;
if(isset($_GET['edit']) && !empty($_GET['edit'])) {
    $menu_id = mysqli_real_escape_string($con, $_GET['edit']);
    $query = "SELECT * FROM restaurant_menu WHERE menu_id = $menu_id";
    $result = mysqli_query($con, $query);
    if($result && mysqli_num_rows($result) > 0) {
        $edit_menu = mysqli_fetch_assoc($result);
    }
}

// Fetch all menu items
$query = "SELECT * FROM restaurant_menu ORDER BY menu_id DESC";
$menu_items = mysqli_query($con, $query);
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
    <style>
        .menu-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .menu-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    
    <?php include '../components/header_admin.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h4 class="mb-4 text-center mt-4">Restaurant Menu Management</h4>
                
                <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if(isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
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
                                <img src="../uploads<?php echo $edit_menu['image']; ?>" class="preview-image" alt="Current menu image">
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
                                    <?php if(mysqli_num_rows($menu_items) > 0): ?>
                                        <?php while($item = mysqli_fetch_assoc($menu_items)): ?>
                                            <tr>
                                                <td><?php echo $item['menu_id']; ?></td>
                                                <td>
                                                    <?php if($item['image']): ?>
                                                        <img src="../uploads<?php echo $item['image']; ?>" class="menu-img" alt="<?php echo $item['menu_name']; ?>">
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
                                                            <div class="modal-content">
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
    <script>
        // Function to preview image before upload
        function previewImage(input) {
            var preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('preview-image');
                    preview.appendChild(img);
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html>