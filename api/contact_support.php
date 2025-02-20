<?php
header("Content-Type: application/json");
include 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$userId = $data['user_id'];
$message = $data['message'];

// Simulate sending a support request
echo json_encode(["status" => "success", "message" => "Support request received"]);

$conn->close();
?>