<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../config/database.php";
require_once "../utils/functions.php";

try {
    // Get query parameters
    $searchQuery = isset($_GET['query']) ? $_GET['query'] : '';
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    $hourlyRate = isset($_GET['hourlyRate']) ? intval($_GET['hourlyRate']) : 0;

    // Validate hourly rate
    if ($hourlyRate < 0) {
        sendError("Invalid hourly rate value. Hourly rate must be a non-negative integer.", 400);
    }

    // Prepare the SQL query to fetch taskers based on search criteria
    $sql = "
        SELECT 
            u.name, 
            t.skill, 
            t.hourly_rate, 
            u.profile_picture, 
            t.rating, 
            t.description, 
            t.availability_status
        FROM 
            taskers t
        JOIN 
            users u ON t.user_id = u.user_id
        WHERE 
            (u.name LIKE ? OR t.skill LIKE ?)
            AND (t.skill = ? OR ? = '')
            AND (t.hourly_rate <= ? OR ? = 0)
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        sendError("Database error", 500);
    }

    // Bind parameters
    $searchParam = "%$searchQuery%";
    $categoryParam = $category;
    $hourlyRateParam = $hourlyRate;

    $stmt->bind_param("sssiii", $searchParam, $searchParam, $categoryParam, $categoryParam, $hourlyRateParam, $hourlyRateParam);

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any taskers were found
    if ($result->num_rows === 0) {
        sendError("No taskers found", 404);
    }

    // Fetch all taskers and format the response
    $taskers = [];
    while ($row = $result->fetch_assoc()) {
        $taskers[] = [
            "name" => $row["name"],
            "skill" => $row["skill"],
            "hourly_rate" => floatval($row["hourly_rate"]),
            "profile_picture" => $row["profile_picture"],
            "rating" => floatval($row["rating"]),
            "description" => $row["description"],
            "availability_status" => boolval($row["availability_status"])
        ];
    }

    // Send the response
    sendSuccess($taskers);

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