<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $admin_id = 10;

    $sql = "SELECT * FROM chat WHERE incoming_chat_id = '$admin_id' OR outgoing_chat_id = '$user_id' ORDER BY timestamp";
    $result = mysqli_query($conn, $sql);

    $messages = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['sent'] = $row['outgoing_chat_id'] == $user_id;
        $messages[] = $row;
    }

    echo json_encode($messages);
} else {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
}

$conn->close();
?>
