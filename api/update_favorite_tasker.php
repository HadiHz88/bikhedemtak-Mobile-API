<?php
require 'db_connect.php';
header("Content-Type: application/json");

$user_id = $_GET['user_id'];
$tasker_id = $_GET['tasker_id'];
$status = $_GET['status'];

if ($status == "add") {
    $query = "INSERT IGNORE INTO favorite_taskers (user_id, tasker_id) VALUES ('$user_id', '$tasker_id')";
} else {
    $query = "DELETE FROM favorite_taskers WHERE user_id = '$user_id' AND tasker_id = '$tasker_id'";
}

if (mysqli_query($conn, $query)) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
}

mysqli_close($conn);
?>
