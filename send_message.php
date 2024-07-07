<?php
session_start();
include 'config.php';

if (isset($_POST['message']) && isset($_POST['recipient_id'])) {
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $recipient_id = mysqli_real_escape_string($conn, $_POST['recipient_id']);
    $sender_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : $_SESSION['user_id'];

    $sql = "INSERT INTO chat (incoming_chat_id, outgoing_chat_id, chat) VALUES ('$recipient_id', '$sender_id', '$message')";
    
    if (mysqli_query($conn, $sql)) {
        $message_id = mysqli_insert_id($conn);
        echo json_encode(["status" => "success", "message_id" => $message_id, "message" => $message, "sender_id" => $sender_id, "recipient_id" => $recipient_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "Message could not be sent."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input."]);
}

$conn->close();
?>
