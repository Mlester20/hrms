<?php

$query = "SELECT user_id, name, address, email, password, role, phone FROM users WHERE role = 'user'";
$result = mysqli_query($con, $query);

if(!$result) {
    die("Query failed: " . mysqli_error($con));
}

// Fetch all users into an array
$users = [];
while($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}
?>