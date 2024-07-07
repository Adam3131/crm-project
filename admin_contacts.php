<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
    exit;
}

// Log the SQL query
function logQuery($query) {
    error_log("Executing query: $query");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Contacts</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
   <link rel="stylesheet" href="css/chat.css">
   <style>
      
   </style>
</head>
<body>
   
<?php @include 'admin_header.php'; ?>

<section class="messages">

   <h1 class="title">Messages</h1>

   <div class="box-container">
      <?php
      $query = "SELECT DISTINCT outgoing_chat_id FROM `chat` WHERE incoming_chat_id = '$admin_id'";
      logQuery($query); // Log the query
      $select_message = mysqli_query($conn, $query) or die('Query failed: ' . mysqli_error($conn));

      if (mysqli_num_rows($select_message) > 0) {
         while ($fetch_message = mysqli_fetch_assoc($select_message)) {
            $user_id = $fetch_message['outgoing_chat_id'];
            $user_query = "SELECT * FROM `users` WHERE id = '$user_id'";
            logQuery($user_query); // Log the user query
            $user_result = mysqli_query($conn, $user_query) or die('Query failed: ' . mysqli_error($conn));
            $user_data = mysqli_fetch_assoc($user_result);
            if ($user_data) {
      ?>
      <div class="box" data-user-id="<?php echo $user_id; ?>" onclick="openChatModal('<?php echo $user_id; ?>')">
         <p>user id : <span><?php echo $user_id; ?></span> </p>
         <p>name : <span><?php echo $user_data['name']; ?></span> </p>
         <p>email : <span><?php echo $user_data['email']; ?></span> </p>
      </div>
      <?php
            } else {
                logQuery("No user data found for user ID: $user_id");
            }
         }
      } else {
         logQuery("No messages found for admin ID: $admin_id");
         echo '<p class="empty">You have no messages!</p>';
      }
      ?>
   </div>

</section>

<!-- Modal for Chatbox -->
<div id="chatModal" class="modal">
   <div class="modal-content">
      <div class="modal-header">
         <h3>Chat with user</h3>
         <span class="close" onclick="closeChatModal()">&times;</span>
      </div>
      <div class="chatbox-content" id="chatbox-content-modal"></div>
      <div class="chatbox-input">
         <input type="text" placeholder="Type a message..." id="chat-input-modal" class="form-control">
         <button type="button" id="send-button-modal" class="btn btn-primary mt-2">Send</button>
      </div>
   </div>
</div>

<script src="js/admin_script.js"></script>
<script src="js/admin_script.js"></script>
<script>
function openChatModal(userId) {
    console.log('Opening chat modal for user ID:', userId);
    const modal = document.getElementById('chatModal');
    modal.style.display = "block";
    fetchMessages(userId);
}

function closeChatModal() {
    const modal = document.getElementById('chatModal');
    modal.style.display = "none";
}

function fetchMessages(userId) {
    console.log('Fetching messages for user ID:', userId);
    fetch('fetch_messages.php?user_id=' + userId)
        .then(response => response.json())
        .then(data => {
            const chatboxContent = document.getElementById('chatbox-content-modal');
            chatboxContent.innerHTML = '';
            const adminId = <?php echo json_encode($admin_id); ?>;
            console.log('Fetched messages:', data);
            data.forEach(message => {
                const chatBubble = document.createElement('div');
                chatBubble.classList.add('chat-bubble');
                if (message.sent) {
                    chatBubble.classList.add('sent-message');
                    console.log('Message sent by admin:', message.chat);
                } else {
                    chatBubble.classList.add('received-message');
                    console.log('Message received from user:', message.chat);
                }
                chatBubble.textContent = message.chat;
                chatboxContent.appendChild(chatBubble);
            });
            chatboxContent.scrollTop = chatboxContent.scrollHeight;
        })
        .catch(error => {
            console.error('Error fetching messages:', error);
        });
}

document.getElementById('send-button-modal').addEventListener('click', function() {
    sendMessage();
});

document.getElementById('chat-input-modal').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
});

function sendMessage() {
    const chatInput = document.getElementById('chat-input-modal');
    const messageText = chatInput.value.trim();
    if (messageText) {
        const userId = document.querySelector('.box-container .box.selected').dataset.userId;
        console.log('Sending message to user ID:', userId);
        fetch('send_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                message: messageText,
                recipient_id: userId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const chatboxContent = document.getElementById('chatbox-content-modal');
                const chatBubble = document.createElement('div');
                chatBubble.classList.add('chat-bubble', 'sent-message');
                chatBubble.textContent = data.message;
                chatboxContent.appendChild(chatBubble);
                chatInput.value = '';
                chatboxContent.scrollTop = chatboxContent.scrollHeight;
                console.log('Admin sent message:', data.message);
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
        });
    }
}

window.onclick = function(event) {
    const modal = document.getElementById('chatModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

document.querySelectorAll('.box-container .box').forEach(box => {
    box.addEventListener('click', function() {
        document.querySelectorAll('.box-container .box').forEach(b => b.classList.remove('selected'));
        this.classList.add('selected');
        console.log('Selected user ID:', this.dataset.userId);
    });
});
</script>

</body>
</html>
