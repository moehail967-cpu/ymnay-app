function initThemeSlider(container) {
    const paginationEl = container.querySelector('.swiper-pagination');
    const sectionEl = container.closest('section');
    const nextBtn = sectionEl ? sectionEl.querySelector('#nextBtn') : null;
    const prevBtn = sectionEl ? sectionEl.querySelector('#prevBtn') : null;

    new Swiper(container, {
        slidesPerView: 1,
        spaceBetween: 10,
        loop: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        loopAdditionalSlides: 1,
        speed: 700,
        grabCursor: true,
        watchSlidesProgress: true,
        pagination: {
            el: paginationEl,
            clickable: true,
        },
        navigation: {
            nextEl: nextBtn || undefined,
            prevEl: prevBtn || undefined,
        },
        keyboard: {
            enabled: true,
        },
        breakpoints: {
            320:  { slidesPerView: 1, spaceBetween: 16, centeredSlides: true },
            768:  { slidesPerView: 1, spaceBetween: 20, centeredSlides: false },
            960:  { slidesPerView: 1, spaceBetween: 22, centeredSlides: false },
            1024: { slidesPerView: 1, spaceBetween: 24, centeredSlides: false },
            1280: { slidesPerView: 1, spaceBetween: 24, centeredSlides: false },
        },
    });
}

document.querySelectorAll('.myTheme').forEach(function(container) {
    initThemeSlider(container);
});
