<?php

@include 'config.php';

session_start();

function logQuery($query) {
    error_log("Executing query: $query");
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $admin_id = 10;

    $query = "SELECT * FROM `chat` WHERE (incoming_chat_id = '$user_id' AND outgoing_chat_id = '$admin_id') OR (incoming_chat_id = '$admin_id' AND outgoing_chat_id = '$user_id') ORDER BY timestamp";
    logQuery($query);
    $result = mysqli_query($conn, $query) or die('Query failed: ' . mysqli_error($conn));

    $messages = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['sent'] = $row['outgoing_chat_id'] == $user_id;
        $messages[] = $row;
    }

    echo json_encode($messages);
} else if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];

    if (isset($_GET['user_id'])) {
        $user_id = $_GET['user_id'];

        $query = "SELECT * FROM `chat` WHERE (incoming_chat_id = '$user_id' AND outgoing_chat_id = '$admin_id') OR (incoming_chat_id = '$admin_id' AND outgoing_chat_id = '$user_id') ORDER BY timestamp";
        logQuery($query);
        $result = mysqli_query($conn, $query) or die('Query failed: ' . mysqli_error($conn));

        $messages = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['sent'] = $row['outgoing_chat_id'] == $admin_id;
            $messages[] = $row;
        }

        echo json_encode($messages);
    } else {
        $query = "SELECT DISTINCT incoming_chat_id FROM `chat` WHERE outgoing_chat_id = '$admin_id'";
        logQuery($query);
        $result = mysqli_query($conn, $query) or die('Query failed: ' . mysqli_error($conn));

        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row['incoming_chat_id'];
        }

        echo json_encode($users);
    }
}
?>
