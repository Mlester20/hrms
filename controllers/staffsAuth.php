<?php
session_start();
include '../components/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = md5(trim($_POST['password'])); // Hash input password with MD5

    // Use prepared statements to prevent SQL Injection
    $query = "SELECT staff_id, name, position, address, profile, shift_type, phone_number FROM staffs WHERE email = ? AND password = ?";
    $stmt = mysqli_prepare($con, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $email, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // Store staff details in session
            $_SESSION['staff_id'] = $row['staff_id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['position'] = $row['position'];
            $_SESSION['address'] = $row['address'];
            $_SESSION['profile'] = $row['profile'];
            $_SESSION['shift_type'] = $row['shift_type'];
            $_SESSION['phone_number'] = $row['phone_number'];
            $_SESSION['email'] = $email;

            // Redirect to staff dashboard or home page
            header("Location: ../staffs/home.php");
            exit();
        } else {
            // Invalid credentials
            echo "<script type='text/javascript'>alert('Invalid Email or Password!');
            document.location='index.php'</script>";
        }

        mysqli_stmt_close($stmt);
    } else {
        // Database error
        echo "<script>alert('Database error. Please try again later.');</script>";
    }
}
?>