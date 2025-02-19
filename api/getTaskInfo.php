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

    // First, get the basic tasker details
    $stmt = $conn->prepare("
        SELECT 
            *
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
    $task = $stmt->get_result();

    if ($task->num_rows === 0) {
        sendError("Task not found", 404);
    }

    $task = $task->fetch_assoc();

    // Now get the tasker info
    $taskStmt = $conn->prepare("
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

    if (!$taskStmt) {
        sendError("Database error", 500);
    }

    $taskStmt->bind_param("i", $task['tasker_id']);

    $taskStmt->execute();

    $tasker = $taskStmt->get_result();

    if ($tasker->num_rows === 0) {
        sendError("Tasker not found", 404);
    }

    $tasker = $tasker->fetch_assoc();

    // get booking time
    $booking_time = $conn->prepare("
        SELECT 
            booking_date
        FROM 
            bookings
        WHERE 
            task_id = ?
                
    ");

    if (!$booking_time) {
        sendError("Database error", 500);
    }

    $booking_time->bind_param("i", $task_id);
    $booking_time->execute();
    $booking_time = $booking_time->get_result();
    $booking_time = $booking_time->fetch_assoc();


    $result = [
        'task_id' => $task['task_id'],
        'tasker_name' => $tasker['name'],
        'tasker_profile_picture' => $tasker['profile_picture'],
        'booking_time' => $booking_time['booking_date'],
        'tasker_availability_status' => (bool)$tasker['availability_status'],
        'tasker_rate' => intval($tasker['hourly_rate']),
        'task_description' => $task['task_description'],
    ];

    sendSuccess($result);

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