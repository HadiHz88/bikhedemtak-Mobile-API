<?php
header("Content-Type: application/json");
include 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$userId = $data['user_id'];
$profession = $data['profession'];
$availability = $data['availability'];
$policyAccepted = $data['policy_accepted'];

$stmt = $conn->prepare("INSERT INTO taskers (user_id, profession, availability, policy_accepted) VALUES (?, ?, ?, ?)");
$stmt->bind_param("issi", $userId, $profession, $availability, $policyAccepted);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>