<?php
header("Content-Type: application/json");
include 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$userId = $data['user_id'];
$amount = $data['amount'];

$stmt = $conn->prepare("INSERT INTO gift_cards (user_id, amount) VALUES (?, ?)");
$stmt->bind_param("id", $userId, $amount);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>