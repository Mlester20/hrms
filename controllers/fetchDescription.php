<?php
session_start();

require_once '../components/connection.php';
require_once '../models/client/descriptionModel.php';

$descriptionModel = new DescriptionModel($con);
$descriptions = $descriptionModel->getDescriptions();