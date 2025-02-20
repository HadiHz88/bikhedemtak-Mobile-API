<?php
header("Content-Type: application/json");
include 'db_connect.php';

$userId = $_GET['user_id'];

$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($name, $email);
$stmt->fetch();

if ($name) {
    echo json_encode(["status" => "success", "name" => $name, "email" => $email]);
} else {
    echo json_encode(["status" => "error", "message" => "User not found"]);
}

$stmt->close();
$conn->close();
?>