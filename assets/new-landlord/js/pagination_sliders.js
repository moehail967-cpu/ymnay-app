(() => {
    const slider = document.querySelector(".mySwiper");

    if (!slider || typeof Swiper === "undefined") {
        return;
    }

    new Swiper(slider, {
            slidesPerView: 4,
            spaceBetween: 24,
            loop: true,
            autoplay: true,
            loopAdditionalSlides: 1,
            speed: 700,
            grabCursor: true,
            watchSlidesProgress: true,

            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },

            navigation: {
                nextEl: "#nextBtn",
                prevEl: "#prevBtn",
            },

            keyboard: {
                enabled: true,
            },

            breakpoints: {
                320: {
                    slidesPerView: 1,
                    spaceBetween: 16,
                    centeredSlides: true,
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                    centeredSlides: false,
                },
                960: {
                    slidesPerView: 3,
                    spaceBetween: 22,
                    centeredSlides: false,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 24,
                    centeredSlides: false,
                },
                1280: {
                    slidesPerView: 4,
                    spaceBetween: 24,
                    centeredSlides: false,
                },
            },
    });
})();

