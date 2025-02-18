<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../config/database.php";
require_once "../utils/functions.php";

try {
    // Get the limit from the query parameter (default to 10 if not provided)
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

    // Validate the limit
    if ($limit <= 0) {
        sendError("Invalid limit value. Limit must be a positive integer.", 400);
    }

    // Prepare the SQL query to fetch recommended profiles
    $stmt = $conn->prepare("
        SELECT 
            u.user_id,
            u.name,
            u.profile_picture,
            t.rating,
            t.hourly_rate,
            t.availability_status
        FROM 
            users u
        JOIN 
            taskers t ON u.user_id = t.user_id
        WHERE 
            t.availability_status = 1 -- Only fetch available taskers
        ORDER BY 
            t.rating DESC -- Order by rating (highest first)
        LIMIT ?
    ");

    if (!$stmt) {
        sendError("Database error", 500);
    }

    // Bind the limit parameter
    $stmt->bind_param("i", $limit);

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any profiles were found
    if ($result->num_rows === 0) {
        sendError("No profiles found", 404);
    }

    // Fetch all profiles and format the response
    $profiles = [];
    while ($row = $result->fetch_assoc()) {
        $profiles[] = [
            "user_id" => intval($row["user_id"]),
            "name" => $row["name"],
            "profile_picture" => $row["profile_picture"],
            "rating" => floatval($row["rating"]),
            "hourly_rate" => intval($row["hourly_rate"]),
            "availability_status" => boolval($row["availability_status"])
        ];
    }

    // Send the response
    sendSuccess($profiles);

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
?>