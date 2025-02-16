<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../config/database.php";
require_once "../utils/functions.php";

try {
    if (!isset($_GET['tasker_id'])) {
        sendError("Tasker ID not provided");
    }

    $tasker_id = intval($_GET['tasker_id']);

    // First, get the basic tasker details
    $stmt = $conn->prepare("
        SELECT 
            u.name, 
            u.profile_picture, 
            t.skill, 
            t.availability_status, 
            t.rating,
            t.description,
            t.hourly_rate
        FROM 
            users u
        INNER JOIN 
            taskers t ON u.user_id = t.user_id
        WHERE 
            u.user_id = ?
    ");

    if (!$stmt) {
        sendError("Database error", 500);
    }

    $stmt->bind_param("i", $tasker_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        sendError("Tasker not found", 404);
    }

    $tasker = $result->fetch_assoc();

    // Now get the count of completed tasks
    $taskStmt = $conn->prepare("
        SELECT 
            COUNT(*) as completed_tasks_count
        FROM 
            tasks
        WHERE 
            tasker_id = ? 
            AND status = 'completed'
    ");

    if (!$taskStmt) {
        sendError("Database error while counting tasks", 500);
    }

    $taskStmt->bind_param("i", $tasker_id);
    $taskStmt->execute();
    $taskResult = $taskStmt->get_result();
    $taskCount = $taskResult->fetch_assoc();

    // Create response data
    $responseData = [
        "name" => $tasker['name'],
        "profile_picture" => $tasker['profile_picture'],
        "skill" => $tasker['skill'],
        "availability_status" => (bool)$tasker['availability_status'],
        "rating" => floatval($tasker['rating']),
        "description" => $tasker['description'],
        "hourly_rate" => intval($tasker['hourly_rate']),
        "completed_tasks_count" => intval($taskCount['completed_tasks_count'])
    ];

    sendSuccess($responseData);

} catch (Exception $e) {
    sendError("An error occurred: " . $e->getMessage(), 500);
} finally {
    // Close both statements
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($taskStmt)) {
        $taskStmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}