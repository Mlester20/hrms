<?php
session_start();
include '../components/connection.php';

    if(isset($_POST['save'])){
        $db = new Database();
        $con = $db->getConnection();
        $stmt = null;
        try{
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $stmt = $con->prepare("INSERT INTO description (description_name) VALUES (?)");
            if(!$stmt){
                throw new Exception('Prepare failed: '.$con->error);
            }
            $stmt->bind_param('s', $description);
            if($stmt->execute()){
                echo "<script type='text/javascript'>alert('Home Description Added Successfully!');
                document.location='../admin/banners.php'</script>";  
            }else{
                throw new Exception('Execute failed: '.$stmt->error);
            }
        }catch(Exception $e){
            echo 'Error: '.$e->getMessage();
        }finally{
            if($stmt){
                $stmt->close();
            }
            $db->closeConnection();
        }
    }

?>