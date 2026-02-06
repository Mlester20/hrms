<?php


require_once '../components/connection.php';
require_once '../models/client/specialOfferModel.php';

$specialOfferModel = new SpecialOfferModel($con);
$offers = $specialOfferModel->getMenus();