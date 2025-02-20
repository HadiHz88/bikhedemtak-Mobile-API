<?php
require 'db_connect.php';
header("Content-Type: application/json");

$user_id = $_GET['user_id'];

$sql = "SELECT taskers.* FROM favorite_taskers 
        JOIN taskers ON favorite_taskers.tasker_id = taskers.id
        WHERE favorite_taskers.user_id = '$user_id'";

$result = $conn->query($sql);

$taskers = [];
while ($row = $result->fetch_assoc()) {
    $taskers[] = $row;
}

echo json_encode($taskers);
$conn->close();
?>
