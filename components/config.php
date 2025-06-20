<?php
$host = getenv('trolley.proxy.rlwy.net');
$port = getenv('9000');
$username = getenv('root');
$password = getenv('sxVdFpNGSRrjeCyKhGdAHfSMerHODfYl');
$database = getenv('railway');

$con = new mysqli($host, $username, $password, $database, $port);

if ($con->connect_error) {
  die("❌ Connection failed: " . $con->connect_error);
}
