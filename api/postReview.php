<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../config/database.php";
require_once "../utils/functions.php";

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError("Invalid request method. Only POST is allowed.", 405);
}

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!isset($data['task_id']) || !isset($data['reviewer_id']) ||
    !isset($data['tasker_id']) || !isset($data['rating'])) {
    sendError("Missing required fields", 400);
}

// Validate rating (assuming 1-5 scale)
$rating = intval($data['rating']);
if ($rating < 1 || $rating > 5) {
    sendError("Rating must be between 1 and 5", 400);
}

try {
    // Begin transaction
    $conn->begin_transaction();

    // Insert the review
    $stmt = $conn->prepare("
        INSERT INTO reviews 
            (task_id, reviewer_id, tasker_id, rating, review_content) 
        VALUES (?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        throw new Exception("Database error: " . $conn->error);
    }

    $task_id = intval($data['task_id']);
    $reviewer_id = intval($data['reviewer_id']);
    $tasker_id = intval($data['tasker_id']);
    $review_content = isset($data['review_content']) ? $data['review_content'] : null;

    $stmt->bind_param("iiiis", $task_id, $reviewer_id, $tasker_id, $rating, $review_content);
    if (!$stmt->execute()) {
        throw new Exception("Error inserting review: " . $stmt->error);
    }

    // Get the new review ID
    $review_id = $conn->insert_id;

    // Calculate new average rating
    $ratingStmt = $conn->prepare("
        SELECT AVG(rating) as average_rating
        FROM reviews
        WHERE tasker_id = ?
    ");

    if (!$ratingStmt) {
        throw new Exception("Database error: " . $conn->error);
    }

    $ratingStmt->bind_param("i", $tasker_id);
    if (!$ratingStmt->execute()) {
        throw new Exception("Error calculating average rating: " . $ratingStmt->error);
    }

    $ratingResult = $ratingStmt->get_result();
    $avgRating = $ratingResult->fetch_assoc()['average_rating'];

    // Update tasker's rating
    $updateStmt = $conn->prepare("
        UPDATE taskers
        SET rating = ?
        WHERE user_id = ?
    ");

    if (!$updateStmt) {
        throw new Exception("Database error: " . $conn->error);
    }

    $updateStmt->bind_param("di", $avgRating, $tasker_id);
    if (!$updateStmt->execute()) {
        throw new Exception("Error updating tasker rating: " . $updateStmt->error);
    }

    // Commit the transaction
    $conn->commit();

    // Send success response
    sendSuccess([
        "review_id" => $review_id,
        "new_average_rating" => round($avgRating, 2)
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    sendError("An error occurred: " . $e->getMessage(), 500);
} finally {
    // Close all statements
    if (isset($stmt)) $stmt->close();
    if (isset($ratingStmt)) $ratingStmt->close();
    if (isset($updateStmt)) $updateStmt->close();
    if (isset($conn)) $conn->close();
}