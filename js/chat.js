document.addEventListener('DOMContentLoaded', function() {
    const floatingButton = document.querySelector('.floating-button');
    const chatboxContainer = document.querySelector('.chatbox-container');
    const closeChatbox = document.querySelector('.close-chatbox');
    const sendButton = document.getElementById('send-button');
    const chatInput = document.getElementById('chat-input');
    const chatboxContent = document.getElementById('chatbox-content');

    const userId = "<?php echo json_encode($_SESSION['user_id'] ?? null); ?>";
    const adminId = "<?php echo json_encode($_SESSION['admin_id'] ?? null); ?>";

    function fetchMessages() {
        fetch('fetch_messages.php')
            .then(response => response.json())
            .then(data => {
                chatboxContent.innerHTML = '';
                data.forEach(message => {
                    const chatBubble = document.createElement('div');
                    chatBubble.classList.add('chat-bubble');
                    if (message.sent) {
                        // Message sent by the current user/admin
                        chatBubble.classList.add('sent-message');
                        console.log('Message sent:', message.chat);
                    } else {
                        // Message received by the current user/admin
                        chatBubble.classList.add('received-message');
                        console.log('Message received:', message.chat);
                    }
                    chatBubble.textContent = message.chat;
                    chatboxContent.appendChild(chatBubble);
                });
                chatboxContent.scrollTop = chatboxContent.scrollHeight;
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
            });
    }

    if (floatingButton && chatboxContainer && closeChatbox && sendButton && chatInput && chatboxContent) {
        floatingButton.addEventListener('click', function() {
            chatboxContainer.classList.toggle('active');
            fetchMessages();
        });

        closeChatbox.addEventListener('click', function() {
            chatboxContainer.classList.remove('active');
        });

        sendButton.addEventListener('click', function() {
            const messageText = chatInput.value.trim();
            if (messageText) {
                fetch('send_message.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        message: messageText,
                        recipient_id: 10
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const chatBubble = document.createElement('div');
                        chatBubble.classList.add('chat-bubble', 'sent-message');
                        chatBubble.textContent = data.message;
                        chatboxContent.appendChild(chatBubble);
                        chatInput.value = '';
                        chatboxContent.scrollTop = chatboxContent.scrollHeight;

                        console.log('Message sent:', data.message);
                    }
                });
            }
        });

        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendButton.click();
            }
        });
    }
});
