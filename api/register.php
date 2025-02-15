<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../config/database.php";
require_once "../utils/functions.php"; // Include the functions file

try {
    // Get the raw POST data
    $data = json_decode(file_get_contents("php://input"));

    // Validate required fields
    if (!isset($data->name) || !isset($data->email) || !isset($data->password)) {
        sendError("Name, email, and password are required");
    }

    // Clean and validate input
    $name = trim($data->name);
    $email = trim($data->email);
    $password = $data->password;
    $phone = isset($data->phone) ? trim($data->phone) : null;

    // Validate email
    if (!isValidEmail($email)) {
        sendError("Invalid email format");
    }

    // Validate password
    if (!isValidPassword($password)) {
        sendError("Password must be at least 8 characters long and contain uppercase, lowercase, and numbers");
    }

    // Validate phone if provided
    if ($phone !== null && !isValidPhone($phone)) {
        sendError("Invalid phone number format");
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        sendError("Email already registered", 409);
    }
    $stmt->close();

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashedPassword, $phone);

    if ($stmt->execute()) {
        $userId = $conn->insert_id;

        // Generate token (in production, use JWT or proper token system)
        $token = bin2hex(random_bytes(32));

        sendSuccess([
            "user_id" => $userId,
            "name" => $name,
            "email" => $email,
            "phone" => $phone,
            "token" => $token
        ], 201); // 201 Created status code
    } else {
        throw new Exception("Failed to create user");
    }

} catch (Exception $e) {
    sendError("Registration failed: " . $e->getMessage(), 500);
} finally {
    closeConnections($stmt, $conn); // Use the reusable function to close connections
}