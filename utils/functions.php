<?php

// Function to send a success response
function sendSuccess($data, $code = 200) {
    http_response_code($code);
    echo json_encode([
        "status" => "success",
        "data" => $data
    ]);
    exit;
}

// Function to send an error response
function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode([
        "status" => "error",
        "message" => $message
    ]);
    exit;
}

// Function to close database connections and statements
function closeConnections($stmt = null, $conn = null) {
    if ($stmt) {
        $stmt->close();
    }
    if ($conn) {
        $conn->close();
    }
}

// Function to validate email format
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate password
function isValidPassword($password) {
    // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
    return strlen($password) >= 8
        && preg_match('/[A-Z]/', $password)
        && preg_match('/[a-z]/', $password)
        && preg_match('/[0-9]/', $password);
}

// Function to validate phone number
function isValidPhone($phone) {
    // Basic phone validation (can be adjusted based on your needs)
    return preg_match('/^[+]?[0-9]{8,}$/', $phone);
}