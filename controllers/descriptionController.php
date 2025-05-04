<?php
session_start();
include '../components/config.php';

    if(isset($_POST['save'])){
        $description = mysqli_real_escape_string($con, $_POST['description']);
        $query = "INSERT INTO description (description_name) VALUES ('$description')" or die("Query Failed: " . mysqli_error($con));
        $result = mysqli_query($con, $query);
        if($result){
            echo "<script type='text/javascript'>alert('Home Description Added Successfully!');
            document.location='../admin/banners.php'</script>";  
        }
    }

?>