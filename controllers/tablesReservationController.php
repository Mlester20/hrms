<?php
session_start();

require_once '../components/connection.php';
require_once '../models/tablesReservationModel.php';

try {
    $tablesModel = new TablesModel();
    $reservations = $tablesModel->getReservations($con);
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}

?>