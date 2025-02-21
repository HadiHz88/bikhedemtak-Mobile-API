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
    if (!isset($data->user_id) || !isset($data->skill) || !isset($data->hourly_rate) || !isset($data->description)) {
        sendError("User ID, skill and Hourly Rate are required");
    }

    // Clean and validate input
    $user_id = intval($data->user_id);
    $skill = trim($data->skill);
    $availability_status = !isset($data->availability_status) || (bool)$data->availability_status;
    $hourly_rate = floatval($data->hourly_rate);
    $description = trim($data->description);

    // Check if the user already exists in the taskers table
    $stmt = $conn->prepare("SELECT user_id FROM taskers WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        sendError("User is already a Tasker", 409);
    }
    $stmt->close();

    // Insert the user into the taskers table
    $stmt = $conn->prepare("INSERT INTO taskers (user_id, skill, availability_status, hourly_rate, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issis", $user_id, $skill, $availability_status, $hourly_rate, $description);

    if ($stmt->execute()) {
        sendSuccess([
            "user_id" => $user_id,
            "skill" => $skill,
            "availability_status" => $availability_status,
            "hourly_rate" => $hourly_rate,
            'description' => $description,
            "message" => "User successfully registered as a Tasker"
        ], 201); // 201 Created status code
    } else {
        throw new Exception("Failed to register as a Tasker");
    }

} catch (Exception $e) {
    sendError("An error occurred: " . $e->getMessage(), 500);
} finally {
    closeConnections($stmt, $conn);
}