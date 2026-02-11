# Flash Notifications Guide

## Overview
Flash notifications ay isang simple notification system na gumagamit ng SweetAlert2 para sa fancy pop-up modals. Messages ay nag-display once lang at automatically nag-disappear.

## Setup

### 1. Include flash.php sa controller
```php
<?php
session_start();
require_once '../includes/flash.php';
require_once '../components/connection.php';
require_once '../models/yourModel.php';
?>
```

### 2. Include flash.php sa view/page
```php
<?php
session_start();
require_once 'includes/flash.php';
?>
<!DOCTYPE html>
<html>
...
```

## Usage

### Setting Flash Messages (sa Controller)

**Success Message:**
```php
setFlash('success', 'Record added successfully!');
header('Location: ../admin/yourpage.php');
exit();
```

**Error Message:**
```php
setFlash('error', 'Email already exists!');
header('Location: ../yourpage.php');
exit();
```

**Warning Message:**
```php
setFlash('warning', 'Please review before submitting!');
header('Location: ../yourpage.php');
exit();
```

**Info Message:**
```php
setFlash('info', 'This is an informational message');
header('Location: ../yourpage.php');
exit();
```

### Displaying Flash Messages (sa View)

Add this sa loob ng `<body>` tag (preferably sa container):

```html
<?php showFlash(); ?>
```

## Complete Example

### Controller (addTaskController.php)
```php
<?php
session_start();
require_once '../includes/flash.php';
require_once '../components/connection.php';
require_once '../models/taskModel.php';

$taskModel = new taskModel();

if (isset($_POST['addTask'])) {
    try {
        $title = $_POST['title'];
        $description = $_POST['description'];
        
        // Add task logic
        $taskModel->addTask($con, $title, $description);
        
        setFlash('success', 'Task added successfully!');
    } catch (Exception $e) {
        setFlash('error', $e->getMessage());
    }
    
    header('Location: ../admin/tasks.php');
    exit();
}
?>
```

### View (tasks.php)
```php
<?php
session_start();
require_once 'includes/flash.php';
?>
<!DOCTYPE html>
<html>
<head>
    <!-- your head content -->
</head>
<body>
    <div class="container mt-4">
        <!-- Display flash messages -->
        <?php showFlash(); ?>
        
        <!-- Your page content -->
        <h1>Tasks</h1>
        <!-- Rest of content -->
    </div>
</body>
</html>
```

## Message Types

| Type | Icon | Use Case |
|------|------|----------|
| `success` | ✓ | Successful operations (added, updated, deleted) |
| `error` | ✗ | Errors and failures |
| `warning` | ⚠ | Warnings and cautions |
| `info` | ℹ | General information |

## Where to Use

### ✅ Perfect for:
- Form submissions (add, edit, delete)
- Login/Register operations
- Upload confirmations
- Validation errors
- Successful database operations

### Example Pages to Update:
- `/admin/employeeTask.php` - ✓ Already using flash
- `/admin/manageRooms.php` - Should use flash
- `/admin/manageTables.php` - Should use flash
- `/admin/manageUsers.php` - Should use flash
- Any admin page with CRUD operations

## Try It Out

1. Go to registration page
2. Fill in form with invalid data (passwords don't match)
3. See the beautiful error modal pop up!

## Features

✅ **Beautiful Design** - Uses SweetAlert2 for modern alerts  
✅ **One-time Display** - Messages auto-clear after showing  
✅ **Type-based Icons** - Different icons for different message types  
✅ **Customizable** - Easy to modify colors and styles  
✅ **Mobile Friendly** - Responsive on all devices  

## Advanced Customization

Edit `flash.php` to change button colors, animation, etc:

```php
Swal.fire({
    icon: 'success',
    title: 'Success',
    text: 'Message here',
    confirmButtonColor: '#3085d6',  // Change button color
    confirmButtonText: 'OK',
    timer: 3000  // Auto-close after 3 seconds (optional)
});
```
