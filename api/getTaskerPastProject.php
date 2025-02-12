<?php

include '../config/database.php';

// Check if tasker_id is provided in the GET request
if (isset($_GET['tasker_id'])) {
    $taskerId = $_GET['tasker_id'];

    // Query to get the tasker's completed projects
    $query = "SELECT t.task_id, c.category_name, t.task_description, t.created_at
              FROM tasks t
              LEFT JOIN categories c ON t.category_id = c.category_id
              WHERE t.tasker_id = ? AND t.status = 'completed'
              ORDER BY t.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $taskerId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($taskId, $categoryName, $taskDescription, $createdAt);

        $completedTasks = [];
        while ($stmt->fetch()) {
            $completedTasks[] = [
                "task_id" => $taskId,
                "category_name" => $categoryName,
                "task_description" => $taskDescription,
                "created_at" => $createdAt
            ];
        }

        echo json_encode(["success" => true, "completed_tasks" => $completedTasks]);
    } else {
        echo json_encode(["success" => false, "message" => "No completed tasks found for this tasker."]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Tasker ID not provided."]);
}

$conn->close();
