<?php
session_start();

require_once '../components/connection.php';
require_once '../models/client/descriptionModel.php';

try{
    $descriptionModel = new DescriptionModel($con);
    $descriptions = $descriptionModel->getDescriptions();
}catch(Exception $e){
    throw new Exception("Error getting descriptions ". $e->getMessage(), 500);
}