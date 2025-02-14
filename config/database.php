<?php
$host = "localhost";
$username = "root";
$password = "";
$db_name = "bikhedemtak_mb";

$conn = new mysqli($host, $username, $password, $db_name);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}
