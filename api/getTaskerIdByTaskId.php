<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../config/database.php";
require_once "../utils/functions.php";

try {
    if (!isset($_GET['task_id'])) {
        sendError("Task ID not provided");
    }

    $task_id = intval($_GET['task_id']);

    // Get the tasker ID based on the task_id
    $stmt = $conn->prepare("
        SELECT 
            tasker_id, status
        FROM 
            tasks
        WHERE 
            task_id = ?
    ");

    if (!$stmt) {
        sendError("Database error", 500);
    }

    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        sendError("Task not found", 404);
    }

    $task = $result->fetch_assoc();

    $tasker_id = $task['tasker_id'];
    $status = $task['status'];

    // Send the tasker ID as a response
    $response = [
        'tasker_id' => $tasker_id,
        'status' => $status
    ];

    // Ensure this function is called to send the response
    sendSuccess($response);

} catch (Exception $e) {
    sendError("An error occurred: " . $e->getMessage(), 500);
} finally {
    closeConnections($stmt, $conn);
}
