<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../config/database.php";
require_once "../utils/functions.php";

// Initialize variables to null
$stmt = null;

try {
    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError("Invalid request method. Only POST is allowed.", 405);
    }

    // Get JSON input
    $data = json_decode(file_get_contents("php://input"));

    // Validate required fields
    if (!isset($data->requester_id) || !isset($data->tasker_id) ||
        !isset($data->task_description)) {
        sendError("Requester ID, Tasker ID, and task description are required", 400);
    }

    // Clean and validate input
    $requester_id = intval($data->requester_id);
    $tasker_id = intval($data->tasker_id);
    $category_id = isset($data->category_id) ? intval($data->category_id) : null;
    $task_description = trim($data->task_description);
    $booking_date = isset($data->booking_date) ? trim($data->booking_date) : null;

    // Begin transaction
    $conn->begin_transaction();

    // Check if the requester exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $requester_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        sendError("Requester not found", 404);
    }
    $stmt->close();
    $stmt = null;

    // Check if the tasker exists
    $stmt = $conn->prepare("SELECT user_id FROM taskers WHERE user_id = ?");
    $stmt->bind_param("i", $tasker_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        sendError("Tasker not found", 404);
    }
    $stmt->close();
    $stmt = null;

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
        $stmt = null;
    }

    // Insert the task
    $stmt = $conn->prepare("
        INSERT INTO tasks 
            (requester_id, tasker_id, category_id, task_description, status) 
        VALUES (?, ?, ?, ?, 'pending')
    ");

    if (!$stmt) {
        throw new Exception("Database error: " . $conn->error);
    }

    $stmt->bind_param("iiis", $requester_id, $tasker_id, $category_id, $task_description);
    if (!$stmt->execute()) {
        throw new Exception("Error inserting task: " . $stmt->error);
    }

    // Get the new task ID
    $task_id = $conn->insert_id;
    $stmt->close();
    $stmt = null;

    // Create a booking entry for this task
    if ($booking_date) {
        $date = new DateTime($booking_date);
        $formatted_date = $date->format('Y-m-d H:i:s');
        $stmt = $conn->prepare("
            INSERT INTO bookings 
                (task_id, requester_id, tasker_id, booking_date, status) 
            VALUES (?, ?, ?, ?, 'pending')
        ");
        $stmt->bind_param("iiis", $task_id, $requester_id, $tasker_id, $formatted_date);
    } else {
        $stmt = $conn->prepare("
            INSERT INTO bookings 
                (task_id, requester_id, tasker_id, status) 
            VALUES (?, ?, ?, 'scheduler')
        ");
        $stmt->bind_param("iii", $task_id, $requester_id, $tasker_id);
    }

    if (!$stmt->execute()) {
        throw new Exception("Error creating booking: " . $stmt->error);
    }

    // Get the new booking ID
    $booking_id = $conn->insert_id;
    $stmt->close();
    $stmt = null;

    // Commit the transaction
    $conn->commit();

    // Check if the tasker exists in past_taskers table for this user
    $stmt = $conn->prepare("
        SELECT id, completed_jobs 
        FROM past_taskers 
        WHERE user_id = ? AND tasker_id = ?
    ");

    if (!$stmt) {
        error_log("Database error while checking past taskers: " . $conn->error);
    } else {
        $stmt->bind_param("ii", $requester_id, $tasker_id);
        $stmt->execute();
        $pastTaskerResult = $stmt->get_result();

        if ($pastTaskerResult->num_rows === 0) {
            // First time using this tasker, insert new record with completed_jobs = 0
            $stmt->close();
            $stmt = null;

            $stmt = $conn->prepare("
                INSERT INTO past_taskers 
                    (user_id, tasker_id, completed_jobs) 
                VALUES (?, ?, 0)
            ");

            if (!$stmt) {
                error_log("Error preparing past_taskers insert: " . $conn->error);
            } else {
                $stmt->bind_param("ii", $requester_id, $tasker_id);
                if (!$stmt->execute()) {
                    error_log("Error inserting into past_taskers: " . $stmt->error);
                }
            }
        }
    }

    // Clean up resources before sending the response
    if ($stmt) $stmt->close();
    if ($conn) $conn->close();

    // Send success response
    sendSuccess([
        "task_id" => $task_id,
        "booking_id" => $booking_id,
        "requester_id" => $requester_id,
        "tasker_id" => $tasker_id,
        "category_id" => $category_id,
        "task_description" => $task_description,
        "booking_date" => isset($date) ? $date->format('Y-m-d H:i:s') : null,
        "status" => "pending",
        "message" => "Task created and booked successfully"
    ], 201);

} catch (Exception $e) {
    // Rollback transaction on error if connection is still valid
    if (isset($conn) && $conn->ping()) {
        $conn->rollback();
    }

    // Clean up resources before sending the error
    if ($stmt) $stmt->close();
    if ($conn) $conn->close();

    sendError("An error occurred: " . $e->getMessage(), 500);
}