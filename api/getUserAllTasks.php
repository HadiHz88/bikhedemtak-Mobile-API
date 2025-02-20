<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include '../config/database.php';

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Updated SQL query to join tables and fetch the required details
    $sql = "
    SELECT 
        tasks.task_id, 
        tasks.tasker_id, 
        users.name AS tasker_name, 
        users.profile_picture AS tasker_profile_picture, 
        bookings.booking_date, 
        tasks.status AS task_status
    FROM 
        tasks
    INNER JOIN 
        users ON tasks.tasker_id = users.user_id
    INNER JOIN 
        bookings ON tasks.task_id = bookings.task_id
    WHERE 
        tasks.requester_id = ?;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }

    echo json_encode([
        "success" => true,
        "tasks" => $tasks
    ]);

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "User ID not provided"]);
}

$conn->close();
