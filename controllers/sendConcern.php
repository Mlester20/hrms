<?php
session_start();
include '../components/config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $subject = mysqli_real_escape_string($con, $_POST['subject']);
    $message = mysqli_real_escape_string($con, $_POST['message']);

    // Validate inputs
    if (empty($subject) || empty($message)) {
        echo "All fields are required.";
        exit();
    }

    // Insert concern into the database
    $query = "INSERT INTO concerns (user_id, email, subject, message, created_at) VALUES ('$user_id', '$email' ,'$subject', '$message', NOW())";
    if (mysqli_query($con, $query)) {
        echo "<script type='text/javascript'>alert('Your Concern has been sent successfully!');
            document.location='../public/contact.php'</script>";  
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>