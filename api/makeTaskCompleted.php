<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../config/database.php";
require_once "../utils/functions.php";

// Read the raw input from the request
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['task_id'])) {
    $task_id = $data['task_id'];

    // SQL to update the task status to 'completed'
    $sql = "UPDATE tasks SET status = 'completed' WHERE task_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $task_id);
    $data = $stmt->execute();
    sendSuccess($data);

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Task ID not provided"]);
}

$conn->close();
