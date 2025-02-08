<?php
header("Content-Type: application/json");
include "../config/database.php";

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->name) || !isset($data->email) || !isset($data->password)) {
    echo json_encode(["error" => "Missing fields"]);
    exit;
}

$name = $conn->real_escape_string($data->name);
$email = $conn->real_escape_string($data->email);
$password = password_hash($data->password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (name, email, password) VALUES ($name, $email, $password)";
if ($conn->query($sql)) {
    echo json_encode(["success" => "User registered"]);
} else {
    echo json_encode(["error" => "Registration failed"]);
}

$conn->close();
