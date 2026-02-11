# Authentication & Role-Based Access Control Guide

## Overview
Complete authentication system with MySQLi, password_verify support, and role-based access control using middleware functions.

## Features

✅ **Secure Password Hashing** - Uses bcrypt (password_hash/password_verify)  
✅ **Backward Compatible** - Still supports MD5 for existing passwords  
✅ **Role-Based Access** - Admin, user, and custom roles  
✅ **Middleware Functions** - Reusable access control  
✅ **Flash Messages** - Beautiful notifications for auth events  
✅ **Session Management** - Stores user data in sessions  

---

## Database Setup

Table structure already exists with these columns:
```sql
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'user',
    phone VARCHAR(20)
);
```

---

## Usage Examples

### 1. Admin-Only Page

**File: admin/dashboard.php**
```php
<?php
// Include middleware - checks if logged in AND is admin
require_once '../middleware/auth.php';
requireAdmin();

// If we reach here, user is admin
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome <?php echo getCurrentUserName(); ?></h1>
    <!-- Admin content -->
</body>
</html>
```

### 2. Any Logged-In User Page

**File: public/home.php**
```php
<?php
// Include middleware - checks only if logged in
require_once '../middleware/auth.php';
requireLogin();

// If we reach here, user is logged in
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
</head>
<body>
    <h1>Welcome <?php echo getCurrentUserName(); ?></h1>
    <!-- User content -->
</body>
</html>
```

### 3. Specific Role Page

**File: admin/reports.php**
```php
<?php
// Only allow admin and moderator roles
require_once '../middleware/auth.php';
requireRole(['admin', 'moderator']);

// If we reach here, user has one of the allowed roles
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports</title>
</head>
<body>
    <h1>Reports Dashboard</h1>
</body>
</html>
```

### 4. Optional Access Control (no redirect)

**File: components/navbar.php**
```php
<?php
require_once __DIR__ . '/../middleware/auth.php';
?>

<nav>
    <h1>Site Name</h1>
    
    <?php if (isLoggedIn()): ?>
        <!-- Show if logged in -->
        <span>Welcome <?php echo getCurrentUserName(); ?></span>
        
        <?php if (isAdmin()): ?>
            <!-- Show only for admins -->
            <a href="/admin/dashboard.php">Admin Panel</a>
        <?php endif; ?>
        
        <a href="/controllers/logout.php">Logout</a>
    <?php else: ?>
        <!-- Show if NOT logged in -->
        <a href="/index.php">Login</a>
        <a href="/register.php">Register</a>
    <?php endif; ?>
</nav>
```

---

## Middleware Functions Reference

### `requireLogin()`
Checks if user is logged in. Redirects to index.php if not.
```php
requireLogin();
```

### `requireAdmin()`
Checks if user is admin. Redirects if not admin.
```php
requireAdmin();
```

### `requireRole($roles)`
Checks if user has specific role(s). Accepts string or array.
```php
// Single role
requireRole('admin');

// Multiple roles
requireRole(['admin', 'moderator', 'staff']);
```

### `isLoggedIn()`
Returns true/false without redirecting.
```php
if (isLoggedIn()) {
    echo "User is logged in";
}
```

### `isAdmin()`
Returns true/false if user is admin.
```php
if (isAdmin()) {
    echo "User is admin";
}
```

### `getCurrentUserId()`
Gets current user's ID.
```php
$userId = getCurrentUserId();
```

### `getCurrentUserRole()`
Gets current user's role.
```php
$role = getCurrentUserRole();
```

### `getCurrentUserName()`
Gets current user's name.
```php
$name = getCurrentUserName();
```

---

## Session Variables Available

After login, these are automatically set:

```php
$_SESSION['user_id']   // User ID from database
$_SESSION['email']     // User email
$_SESSION['name']      // User full name
$_SESSION['address']   // User address
$_SESSION['phone']     // User phone
$_SESSION['role']      // User role (admin, user, etc.)
```

---

## Login Flow

1. **User fills login form** at `index.php`
2. **Submits to** `controllers/auth.php`
3. **authModel::login()** verifies credentials:
   - Fetches user by email
   - Uses `password_verify()` to check password
   - Falls back to MD5 for backward compatibility
4. **Sets session variables** if login successful
5. **Redirects** based on role (admin → dashboard, user → home)
6. **Flash message** displayed on redirect

---

## Password Hashing

### Old Passwords (MD5)
Existing passwords stored as MD5 still work:
```php
md5($password) === stored_hash
```

### New Passwords (Bcrypt)
New registrations use secure bcrypt:
```php
password_hash($password, PASSWORD_BCRYPT)
password_verify($password, stored_hash)
```

The system **automatically** detects which type and verifies correctly!

---

## Logout Functionality

**File: controllers/logout.php**
```php
<?php
session_start();
require_once '../includes/flash.php';

// Destroy session
session_destroy();

// Set flash message
setFlash('success', 'You have been logged out successfully.');

// Redirect to login
header('Location: ../index.php');
exit();
?>
```

---

## File Structure

```
hrms/
├── middleware/
│   └── auth.php                 ← Reusable middleware functions
├── models/
│   └── authModel.php            ← Login/Register logic
├── controllers/
│   ├── auth.php                 ← Login handler
│   └── logout.php               ← Logout handler
├── admin/
│   ├── dashboard.php            ← Admin only (requireAdmin)
│   └── profile.php              ← Any logged in (requireLogin)
├── public/
│   └── home.php                 ← Any logged in (requireLogin)
└── index.php                    ← Login page
```

---

## Complete Example: Protected Admin Page

**File: admin/settings.php**
```php
<?php
session_start();
require_once '../middleware/auth.php';
require_once '../includes/flash.php';
require_once '../components/connection.php';

// This redirects to index.php if not admin
requireAdmin();

// From here on, we know user is admin
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../components/header_admin.php'; ?>
    
    <div class="container mt-4">
        <?php showFlash(); ?>
        
        <h1>Admin Settings</h1>
        <p>Current user: <?php echo getCurrentUserName(); ?></p>
        <p>User ID: <?php echo getCurrentUserId(); ?></p>
        
        <!-- Admin settings form -->
    </div>
</body>
</html>
```

---

## Security Best Practices

✅ Always use `requireLogin()` or `requireAdmin()` on protected pages  
✅ Never expose user ID in URLs without validation  
✅ Use prepared statements for all database queries  
✅ Store sensitive data in sessions, not cookies  
✅ Always sanitize user input  
✅ Use HTTPS in production  
✅ Set secure session cookies in production  

---

## Troubleshooting

### User can't login
- Check if email exists in database
- Verify password is correct
- Check if account is active

### Middleware redirect not working
- Make sure `require_once '../middleware/auth.php'` is at TOP of page
- Verify path is correct from file location
- Check session_start() is called

### Flash messages not showing
- Ensure `showFlash()` is called in view
- Make sure flash.php is included
- Check if session is started

---

## Next Steps

1. Update existing admin pages to use `requireAdmin()`
2. Update user pages to use `requireLogin()`
3. Create `/controllers/logout.php` with session destroy
4. Test access control on different roles
5. Implement password reset functionality
