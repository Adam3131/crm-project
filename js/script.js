document.addEventListener('DOMContentLoaded', function() {
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
