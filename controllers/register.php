<?php
include '../components/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $role = 'user'; // Default role for registered users

    //connection
    $db = new Database();
    $con = $db->getConnection();

    // Validate required fields
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        die('All fields are required.');
    }

    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo "<script type='text/javascript'>alert('Passwords do not match!');
        document.location='../register.php'</script>";  
    }

    //perform query using try, catch and finally block
    try{
        // Check if email already exists
            $query = "SELECT * FROM users WHERE email = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<script type='text/javascript'>alert('Email already exists!');
                document.location='../register.php'</script>";  
            }

            // Hash the password using md5
            $hashedPassword = md5($password);

            // Insert user into the database
            $query = "INSERT INTO users (name, address, email, password, role, phone) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $con->prepare($query);
            $stmt->bind_param('ssssss', $name, $address, $email, $hashedPassword, $role, $phone);

            if ($stmt->execute()) {
                echo "<script type='text/javascript'>alert('Registration successful!');
                document.location='../index.php'</script>";  
            } else {
                echo 'Error: ' . $stmt->error;
            }
    }catch( Exception $e ){
        throw new Exception("Error creating account". $e->getMessage(), 500);
    } finally{
        $db->closeConnection();
    }

}
?>