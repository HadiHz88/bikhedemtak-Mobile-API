<?php
// Include database connection
include'../config/database.php';

// Get task_id from GET parameters
$task_id = isset($_GET['task_id']) ? $_GET['task_id'] : die('Task ID not provided');

// Query to get task details
$query = "SELECT t.task_id, t.task_description, t.status, t.created_at, 
                 u.name AS requester_name, u.profile_picture AS requester_profile,
                 u2.name AS tasker_name, u2.profile_picture AS tasker_profile, 
                 ts.category_name, b.booking_date, r.review_id, r.rating, r.review_content
          FROM tasks t
          JOIN users u ON t.requester_id = u.user_id
          LEFT JOIN taskers tk ON t.tasker_id = tk.user_id
          LEFT JOIN users u2 ON tk.user_id = u2.user_id  -- Corrected join to get tasker details
          LEFT JOIN categories ts ON t.category_id = ts.category_id
          LEFT JOIN bookings b ON t.task_id = b.task_id
          LEFT JOIN reviews r ON t.task_id = r.task_id
          WHERE t.task_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $task_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch result as associative array
    $task_details = $result->fetch_assoc();

    // Return data as JSON
    echo json_encode([
        'status' => 'success',
        'data' => $task_details
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Task not found'
    ]);
}

// Close the connection
$conn->close();
?>
