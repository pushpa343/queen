<?php
$ds_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "newqueentailor_db";

$conn = mysqli_connect($ds_server, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>