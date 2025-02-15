<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../config/database.php";
require_once "../utils/functions.php";

try {
    // Get the raw POST data
    $data = json_decode(file_get_contents("php://input"));

    // Validate required fields
    if (!isset($data->requester_id) || !isset($data->tasker_id) || !isset($data->task_description)) {
        sendError("Requester ID, Tasker ID, and task description are required");
    }

    // Clean and validate input
    $requester_id = intval($data->requester_id);
    $tasker_id = intval($data->tasker_id);
    $category_id = isset($data->category_id) ? intval($data->category_id) : null;
    $task_description = trim($data->task_description);

    // Check if the requester and tasker exist
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $requester_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        sendError("Requester not found", 404);
    }
    $stmt->close();

    $stmt = $conn->prepare("SELECT user_id FROM taskers WHERE user_id = ?");
    $stmt->bind_param("i", $tasker_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        sendError("Tasker not found", 404);
    }
    $stmt->close();

    // Check if the category exists
    if ($category_id !== null) {
        $stmt = $conn->prepare("SELECT category_id FROM categories WHERE category_id = ?");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            sendError("Category not found", 404);
        }
        $stmt->close();
    }

    // Insert the task into the tasks table
    $stmt = $conn->prepare("INSERT INTO tasks (requester_id, tasker_id, category_id, task_description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $requester_id, $tasker_id, $category_id, $task_description);

    if ($stmt->execute()) {
        $task_id = $conn->insert_id;

        sendSuccess([
            "task_id" => $task_id,
            "requester_id" => $requester_id,
            "tasker_id" => $tasker_id,
            "category_id" => $category_id,
            "task_description" => $task_description,
            "status" => "pending",
            "message" => "Task created successfully"
        ], 201); // 201 Created status code
    } else {
        throw new Exception("Failed to create task");
    }

} catch (Exception $e) {
    sendError("An error occurred: " . $e->getMessage(), 500);
} finally {
    closeConnections($stmt, $conn); // Use the reusable function to close connections
}