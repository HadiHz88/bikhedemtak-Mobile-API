<?php

include '../config/database.php';

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    $sql = "
    SELECT 
        r.review_id,
        r.rating,
        r.review_content,
        r.created_at,
        u.user_id AS reviewer_id,
        u.name AS reviewer_name,
        u.profile_picture AS reviewer_profile_picture
    FROM 
        reviews r
    INNER JOIN 
        users u ON r.reviewer_id = u.user_id
    WHERE 
        r.tasker_id = ?;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }

    if (count($reviews) > 0) {
        echo json_encode([
            "success" => true,
            "reviews" => $reviews
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "No reviews found"]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Tasker ID not provided"]);
}

$conn->close();
