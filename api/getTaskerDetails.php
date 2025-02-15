<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../config/database.php";
require_once "../utils/functions.php";

try {
    if (!isset($_GET['user_id'])) {
        sendError("User ID not provided");
    }

    $user_id = intval($_GET['user_id']);

    $stmt = $conn->prepare("
        SELECT 
            u.name, 
            u.profile_picture, 
            t.skill, 
            t.availability_status, 
            t.rating
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

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        sendError("Tasker not found", 404);
    }

    $tasker = $result->fetch_assoc();

    // Create response data
    $responseData = [
        "name" => $tasker['name'],
        "profile_picture" => $tasker['profile_picture'],
        "skill" => $tasker['skill'],
        "availability_status" => (bool)$tasker['availability_status'],
        "rating" => floatval($tasker['rating'])
    ];

    sendSuccess($responseData);

} catch (Exception $e) {
    sendError("An error occurred: " . $e->getMessage(), 500);
} finally {
    closeConnections($stmt, $conn);
}