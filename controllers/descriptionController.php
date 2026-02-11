<?php

require_once '../components/connection.php';
require_once '../models/descriptionModel.php';

    /* =========================
        GET DESCRIPTION
    ========================= */
    $descriptionModel = new descriptionModel();
    $result = $descriptionModel->getAllDescriptions($con);
    

    /* =========================
        UPDATE DESCRIPTION
    ========================= */
    if (isset($_POST['updateDescription'])) {
        $description_id = $_POST['description_id'];
        $description_name = $_POST['description_name'];

        try {
            $descriptionModel->updateDescription($con, $description_id, $description_name);
            header('Location: ../admin/description.php?success=Description updated successfully');
            exit();
        } catch (Exception $e) {
            header('Location: ../admin/description.php?error=' . urlencode($e->getMessage()));
            exit();
        }
    }

?>