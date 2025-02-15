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
    if (!isset($data->task_id) || !isset($data->requester_id) || !isset($data->tasker_id) || !isset($data->booking_date)) {
        sendError("Task ID, Requester ID, Tasker ID, and the Booking Date are required");
    }

    // Clean and validate input
    $task_id = intval($data->task_id);
    $requester_id = intval($data->requester_id);
    $tasker_id = intval($data->tasker_id);
    $booking_date = trim($data->booking_date);

    // Check if the task exists
    $stmt = $conn->prepare("SELECT task_id FROM tasks WHERE task_id = ?");
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        sendError("Task not found", 404);
    }
    $stmt->close();

    // Check if the requester exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $requester_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        sendError("Requester not found", 404);
    }
    $stmt->close();

    // Check if the tasker exists
    $stmt = $conn->prepare("SELECT user_id FROM taskers WHERE user_id = ?");
    $stmt->bind_param("i", $tasker_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        sendError("Tasker not found", 404);
    }
    $stmt->close();

    // Insert the booking into the bookings table
    $date = new DateTime($booking_date);
    $stmt = $conn->prepare("INSERT INTO bookings (task_id, requester_id, tasker_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $task_id, $requester_id, $tasker_id);

    if ($stmt->execute()) {
        $booking_id = $conn->insert_id;

        sendSuccess([
            "booking_id" => $booking_id,
            "task_id" => $task_id,
            "requester_id" => $requester_id,
            "tasker_id" => $tasker_id,
            "booking_date" => $date,
            "status" => "pending",
            "message" => "Task booked successfully"
        ], 201); // 201 Created status code
    } else {
        throw new Exception("Failed to book task");
    }

} catch (Exception $e) {
    sendError("An error occurred: " . $e->getMessage(), 500);
} finally {
    closeConnections($stmt, $conn);
}