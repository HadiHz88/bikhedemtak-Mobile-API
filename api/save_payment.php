<?php
header("Content-Type: application/json");
include 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$userId = $data['user_id'];
$cardNumber = $data['card_number'];
$expiryDate = $data['expiry_date'];
$cvv = $data['cvv'];

$stmt = $conn->prepare("INSERT INTO payments (user_id, card_number, expiry_date, cvv) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $userId, $cardNumber, $expiryDate, $cvv);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>