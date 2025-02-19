<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../config/database.php";
require_once "../utils/functions.php";

try {
    $stmt = $conn->prepare("
        SELECT 
            category_id, 
            category_name 
        FROM 
            categories
    ");

    if (!$stmt) {
        sendError("Database error", 500);
    }

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any categories were found
    if ($result->num_rows === 0) {
        sendError("No categories found", 404);
    }

    // Fetch all categories and format the response
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = [
            "category_id" => intval($row["category_id"]),
            "category_name" => $row["category_name"]
        ];
    }

    // Send the response
    sendSuccess($categories);

} catch (Exception $e) {
    sendError("An error occurred: " . $e->getMessage(), 500);
} finally {
    // Close the statement and connection
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
