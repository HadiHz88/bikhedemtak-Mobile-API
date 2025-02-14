<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../config/database.php";

// Validation functions
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function isValidPassword($password) {
    // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
    return strlen($password) >= 8
        && preg_match('/[A-Z]/', $password)
        && preg_match('/[a-z]/', $password)
        && preg_match('/[0-9]/', $password);
}

function isValidPhone($phone) {
    // Basic phone validation (can be adjusted based on your needs)
    return preg_match('/^[+]?[0-9]{8,}$/', $phone);
}

try {
    // Get the raw POST data
    $data = json_decode(file_get_contents("php://input"));

    // Validate required fields
    if (!isset($data->name) || !isset($data->email) || !isset($data->password)) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Name, email, and password are required"
        ]);
        exit;
    }

    // Clean and validate input
    $name = trim($data->name);
    $email = trim($data->email);
    $password = $data->password;
    $phone = isset($data->phone) ? trim($data->phone) : null;

    // Validate email
    if (!isValidEmail($email)) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Invalid email format"
        ]);
        exit;
    }

    // Validate password
    if (!isValidPassword($password)) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Password must be at least 8 characters long and contain uppercase, lowercase, and numbers"
        ]);
        exit;
    }

    // Validate phone if provided
    if ($phone !== null && !isValidPhone($phone)) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Invalid phone number format"
        ]);
        exit;
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        http_response_code(409);
        echo json_encode([
            "status" => "error",
            "message" => "Email already registered"
        ]);
        exit;
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

        http_response_code(201);
        echo json_encode([
            "status" => "success",
            "data" => [
                "user_id" => $userId,
                "name" => $name,
                "email" => $email,
                "phone" => $phone,
                "token" => $token
            ]
        ]);
    } else {
        throw new Exception("Failed to create user");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Registration failed: " . $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}