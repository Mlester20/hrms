<?php
session_start();

require_once '../models/client/tableBookingModel.php';
require_once '../components/connection.php';

    try{
        $table = new tableBookingModel($con);
        $tables = $table->getTables();
    }catch(Exception $e){
        throw new Error("Someting went wrong ". $e->getMessage());
    }

?>