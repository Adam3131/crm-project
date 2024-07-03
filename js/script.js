document.addEventListener('DOMContentLoaded', function() {
    // Swiper initialization code
    var mySwiper = new Swiper('.swiper-container', {
        spaceBetween: 30,
        centeredSlides: true,
        loop: true,
        slidesPerView: 3,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
    });

    // Chatbox initialization code
    const floatingButton = document.querySelector('.floating-button');
    const chatboxContainer = document.querySelector('.chatbox-container');
    const closeChatbox = document.querySelector('.close-chatbox');
    const sendButton = document.getElementById('send-button');
    const chatInput = document.getElementById('chat-input');
    const chatboxContent = document.querySelector('.chatbox-content');

    if (floatingButton && chatboxContainer && closeChatbox && sendButton && chatInput && chatboxContent) {
        floatingButton.addEventListener('click', function() {
            chatboxContainer.classList.toggle('active');
        });

        closeChatbox.addEventListener('click', function() {
            chatboxContainer.classList.remove('active');
        });

        sendButton.addEventListener('click', function() {
            const messageText = chatInput.value.trim();
            if (messageText) {
                const chatBubble = document.createElement('div');
                chatBubble.classList.add('chat-bubble', 'sent-message');
                chatBubble.textContent = messageText;
                chatboxContent.appendChild(chatBubble);
                chatInput.value = '';
                chatboxContent.scrollTop = chatboxContent.scrollHeight;
            }
        });

        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendButton.click();
            }
        });
    }

    let userBox = document.querySelector('.header .flex .account-box');
    let navbar = document.querySelector('.header .flex .navbar');

    document.querySelector('#user-btn').onclick = () => {
        userBox.classList.toggle('active');
        navbar.classList.remove('active');
    };

    document.querySelector('#menu-btn').onclick = () => {
        navbar.classList.toggle('active');
        userBox.classList.remove('active');
    };

    window.onscroll = () => {
        userBox.classList.remove('active');
        navbar.classList.remove('active');
    };
});
