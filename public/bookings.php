<?php
session_start();
include '../components/config.php';

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$query = "SELECT name, address, email, phone FROM users WHERE user_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verify current password
    $passwordQuery = "SELECT password FROM users WHERE user_id = ?";
    $stmt = $con->prepare($passwordQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();
    $stmt->close();

    // Use MD5 for verification instead of password_verify
    if (md5($current_password) === $hashedPassword) {
        // Update user details
        $updateQuery = "UPDATE users SET name = ?, address = ?, email = ?, phone = ? WHERE user_id = ?";
        $stmt = $con->prepare($updateQuery);
        $stmt->bind_param("ssssi", $name, $address, $email, $phone, $user_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Profile updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update profile.";
        }
        $stmt->close();

        // Handle password change if new password is provided
        if (!empty($new_password) && !empty($confirm_password)) {
            if ($new_password === $confirm_password) {
                // Use MD5 for new password hashing instead of password_hash
                $new_hashed_password = md5($new_password);
                $passwordUpdateQuery = "UPDATE users SET password = ? WHERE user_id = ?";
                $stmt = $con->prepare($passwordUpdateQuery);
                $stmt->bind_param("si", $new_hashed_password, $user_id);

                if ($stmt->execute()) {
                    $_SESSION['success'] = "Password updated successfully!";
                } else {
                    $_SESSION['error'] = "Failed to update password.";
                }
                $stmt->close();
            } else {
                $_SESSION['error'] = "New password and confirm password do not match.";
            }
        }
    } else {
        $_SESSION['error'] = "Incorrect current password. Please try again.";
    }

    header('Location: profile.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?php include '../components/title.php'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8y+4g5e5c5e5c5e5c5e5c5e5c5e5c5e5c5e5c5e" crossorigin="anonymous" />
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../css/clientNavbar.css">
    <link rel="stylesheet" href="../css/home.css">
</head>
<body>
    <!-- Header admin component -->
    <?php include '../components/header.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Your Bookings</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Booking ID</th>
                        <th>Room Type</th>
                        <th>Check-in Date</th>
                        <th>Check-out Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>BK001</td>
                        <td>Deluxe Room</td>
                        <td>2025-05-10</td>
                        <td>2025-05-15</td>
                        <td><span class="badge bg-success">Confirmed</span></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>BK002</td>
                        <td>Standard Room</td>
                        <td>2025-06-01</td>
                        <td>2025-06-05</td>
                        <td><span class="badge bg-warning">Pending</span></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>BK003</td>
                        <td>Suite</td>
                        <td>2025-07-20</td>
                        <td>2025-07-25</td>
                        <td><span class="badge bg-danger">Cancelled</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>