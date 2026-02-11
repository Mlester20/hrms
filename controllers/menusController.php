<?php
session_start();


require_once '../components/connection.php';
require_once '../models/client/menusModel.php';

    /* =========================
        GET Menus
    ========================= */
    $menusModel = new menusModel();
    $menus = $menusModel->getMenus($con);

?>