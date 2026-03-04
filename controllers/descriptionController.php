<?php
session_start();

require_once '../components/connection.php';
require_once '../models/descriptionModel.php';
require_once '../includes/flash.php';

    /* =========================
        GET DESCRIPTION
    ========================= */
    $descriptionModel = new descriptionModel($con);
    $result = $descriptionModel->getAllDescriptions();
    

    /* =========================
        UPDATE DESCRIPTION
    ========================= */
    if (isset($_POST['updateDescription'])) {
        $description_id = $_POST['description_id'];
        $description_name = $_POST['description_name'];

        try {
            $descriptionModel->updateDescription($description_id, $description_name);
            setFlash('success', "Description Updated Successfully!");
            header('Location: ../admin/description.php?success=Description updated successfully');
            exit();
        } catch (Exception $e) {
            header('Location: ../admin/description.php?error=' . urlencode($e->getMessage()));
            exit();
        }
    }

?>