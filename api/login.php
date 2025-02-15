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

    // Validate input
    if (!isset($data->email) || !isset($data->password)) {
        sendError("Email and password are required");
    }

    $email = trim($data->email);
    $password = trim($data->password);

    // Validate email format
    if (!isValidEmail($email)) {
        sendError("Invalid email format");
    }

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT u.user_id, u.name, u.email, u.password, u.phone, u.profile_picture, 
                                  t.user_id as tasker_id, t.skill, t.availability_status, t.rating
                           FROM users u 
                           LEFT JOIN taskers t ON u.user_id = t.user_id 
                           WHERE u.email = ?");

    if (!$stmt) {
        sendError("Database error", 500);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        sendError("Invalid credentials", 401);
    }

    $user = $result->fetch_assoc();

    // Verify password
    if (!password_verify($password, $user['password'])) {
        sendError("Invalid credentials", 401);
    }

    // Create response data (excluding sensitive information)
    $responseData = [
        "user_id" => $user['user_id'],
        "name" => $user['name'],
        "email" => $user['email'],
        "phone" => $user['phone'],
        "profile_picture" => $user['profile_picture'],
        "is_tasker" => !is_null($user['tasker_id']),
    ];

    // Add tasker information if the user is a tasker
    if ($user['tasker_id']) {
        $responseData['tasker'] = [
            "skill" => $user['skill'],
            "availability_status" => (bool)$user['availability_status'],
            "rating" => floatval($user['rating'])
        ];
    }

    // Generate a simple token for authentication
    $token = bin2hex(random_bytes(32));
    $responseData['token'] = $token;

    sendSuccess($responseData);

} catch (Exception $e) {
    sendError("An error occurred: " . $e->getMessage(), 500);
} finally {
    closeConnections($stmt, $conn);
}