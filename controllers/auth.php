<?php   
session_start();
include '../components/config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = md5(trim($_POST['password'])); // Hash input password with MD5

    // Use prepared statements to prevent SQL Injection
    $query = "SELECT user_id, name, role FROM users WHERE email = ? AND password = ?";
    $stmt = mysqli_prepare($con, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $email, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $_SESSION['user_id'] = $row['user_id']; // Store user_id in session
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $row['name'];
            $_SESSION['address'] = $row['address'];
            $_SESSION['phone'] = $row['phone'];
            $_SESSION['role'] = $row['role'];

            // Redirect based on role
            if ($row['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../public/home.php");
            }
            exit();
        } else {
            echo "<script type='text/javascript'>alert('Invalid Username or Password!');
            document.location='../index.php'</script>";  
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Database error. Please try again later.');</script>";
    }
}
?>