<?php

require_once '../components/connection.php';
require_once '../models/concernsModel.php';

$concernsModel = new concernsModel();


    // Handle bulk delete
    if (isset($_POST['bulk_delete']) && isset($_POST['selected_concerns'])) {
        try {
            $selected_ids = array_map('intval', $_POST['selected_concerns']);
            $ids = implode(',', $selected_ids);
            
            $delete_query = "DELETE FROM concerns WHERE id IN ($ids)";
            if (mysqli_query($con, $delete_query)) {
                $_SESSION['success'] = "Selected concerns have been deleted successfully.";
            } else {
                throw new Exception("Error deleting concerns: " . mysqli_error($con));
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: ../admin/concerns.php');
        exit();
    }

    $concerns = $concernsModel->getAllConcerns($con);


?>