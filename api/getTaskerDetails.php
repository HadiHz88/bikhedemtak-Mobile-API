<?php

include '../config/database.php';

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Get Tasker info
    $sql = "
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
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $tasker = $result->fetch_assoc();

        $taskerData = [
            "success" => true,
            "name" => $tasker['name'],
            "profile_picture" => $tasker['profile_picture'],
            "skill" => $tasker['skill'],
            "availability_status" => $tasker['availability_status'],
            "rating" => $tasker['rating']
        ];
        echo json_encode($taskerData);
    } else {
        echo json_encode(["success" => false, "message" => "Tasker not found"]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "User ID not provided"]);
}

$conn->close();
