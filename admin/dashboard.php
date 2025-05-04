<?php
session_start();
include '../components/config.php';

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - <?php include '../components/title.php'; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/customAdminHeader.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <style>
    canvas {
      max-height: 300px;
      width: 100% !important;
    }
    @media (max-width: 767.98px) {
      .chart-container {
        margin-bottom: 2rem;
      }
    }
  </style>
</head>
<body>
    <!-- header admin component -->
    <?php include '../components/header_admin.php'; ?>

    <div class="container py-5">
        <h2 class="text-center text-muted mb-4">Reservations Analytics</h2>
        <div class="row">
        <div class="col-12 col-md-6 chart-container">
            <canvas id="lineChart"></canvas>
        </div>
        <div class="col-12 col-md-6 chart-container">
            <canvas id="pieChart"></canvas>
        </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/analytics.js"></script>

</body>
</html>
