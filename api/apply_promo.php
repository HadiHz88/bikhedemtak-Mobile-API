<?php
header("Content-Type: application/json");
include 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$userId = $data['user_id'];
$promoCode = $data['promo_code'];

$stmt = $conn->prepare("INSERT INTO promos (user_id, promo_code) VALUES (?, ?)");
$stmt->bind_param("is", $userId, $promoCode);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>