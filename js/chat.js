document.addEventListener('DOMContentLoaded', function() {
    const floatingButton = document.querySelector('.floating-button');
    const chatboxContainer = document.querySelector('.chatbox-container');
    const closeChatbox = document.querySelector('.close-chatbox');
    const sendButton = document.getElementById('send-button');
    const chatInput = document.getElementById('chat-input');
    const chatboxContent = document.getElementById('chatbox-content');

    function fetchMessages() {
        fetch('fetch_messages.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                chatboxContent.innerHTML = '';
                data.forEach(message => {
                    const chatBubble = document.createElement('div');
                    chatBubble.classList.add('chat-bubble');
                    if (message.sent) {
                        chatBubble.classList.add('sent-message');
                    } else {
                        chatBubble.classList.add('received-message');
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
                        recipient_id: 10 // Assuming the recipient is always the admin with ID 10
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
})