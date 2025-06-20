<?php
$host = getenv('trolley.proxy.rlwy.net');
$port = getenv('28768');
$username = getenv('root');
$password = getenv('sxVdFpNGSRrjeCyKhGdAHfSMerHODfYl');
$database = getenv('hoteldb');

$con = new mysqli($host, $username, $password, $database, $port);

if ($con->connect_error) {
  die("❌ Connection failed: " . $con->connect_error);
}