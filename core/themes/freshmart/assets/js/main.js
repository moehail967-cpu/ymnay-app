/* Freshmart Theme — Main JS */
(function ($) {
    "use strict";

    // Flash sale countdown
    function startCountdown(endTime) {
        var $el = $('.fm-flash-countdown');
        if (!$el.length) return;

        function tick() {
            var now  = new Date().getTime();
            var diff = endTime - now;
            if (diff <= 0) { $el.html('<span>Sale ended</span>'); return; }

            var h = Math.floor(diff / 3600000);
            var m = Math.floor((diff % 3600000) / 60000);
            var s = Math.floor((diff % 60000) / 1000);

            function pad(n) { return String(n).padStart(2, '0'); }

            $el.html(
                '<div class="fm-countdown-block"><div class="fm-countdown-num">' + pad(h) + '</div><div class="fm-countdown-lbl">hrs</div></div>' +
                '<span class="fm-countdown-sep">:</span>' +
                '<div class="fm-countdown-block"><div class="fm-countdown-num">' + pad(m) + '</div><div class="fm-countdown-lbl">min</div></div>' +
                '<span class="fm-countdown-sep">:</span>' +
                '<div class="fm-countdown-block"><div class="fm-countdown-num">' + pad(s) + '</div><div class="fm-countdown-lbl">sec</div></div>'
            );
            setTimeout(tick, 1000);
        }
        tick();
    }

    $(document).ready(function () {
        // Set flash sale end to midnight
        var midnight = new Date();
        midnight.setHours(23, 59, 59, 0);
        startCountdown(midnight.getTime());

        // Sticky navbar active category on scroll
        $(window).on('scroll', function () {
            var scrollTop = $(this).scrollTop();
            $('.fm-navbar').toggleClass('scrolled', scrollTop > 60);
        });

        // Category bar hover effect
        $('.fm-category-link').on('mouseenter', function () {
            $(this).closest('.fm-category-bar').find('.fm-category-link').removeClass('active');
        }).on('mouseleave', function () {
            $(this).closest('.fm-category-bar').find('.fm-category-link.was-active').addClass('active');
        });
    });

})(jQuery);
