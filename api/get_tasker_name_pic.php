<?php
include '../config/database.php'; 

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    
    $sql = "
        SELECT u.name, u.profile_picture
        FROM users u
        INNER JOIN taskers t ON u.user_id = t.user_id
        WHERE u.user_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($name, $profile_picture);
        $stmt->fetch();
        
        // Prepare data in JSON format
        $taskerData = [
            "success" => true,
            "name" => $name,
            "profile_picture" => $profile_picture
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
?>
