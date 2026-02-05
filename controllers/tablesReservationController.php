<?php
session_start();

require_once '../components/connection.php';
require_once '../models/tablesReservationModel.php';
require_once '../middleware/auth.php';
requireAdmin(); // Ensure the user is an admin

try {
    $tablesModel = new TablesModel();
    $reservations = $tablesModel->getReservations($con);
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}

?>