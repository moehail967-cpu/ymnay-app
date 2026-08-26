/* Techzone Theme — Main JS */
(function ($) {
    "use strict";

    $(document).ready(function () {

        // Product tab filter
        $(document).on('click', '.tz-tab', function () {
            var $tabs = $(this).closest('.tz-tabs');
            $tabs.find('.tz-tab').removeClass('active');
            $(this).addClass('active');

            var target = $(this).data('target');
            if (target) {
                var $container = $(target).closest('[data-tab-group]') || $(target).parent();
                $container.find('[data-tab-content]').hide();
                $(target).fadeIn(200);
            }
        });

        // Sticky navbar shadow
        $(window).on('scroll', function () {
            var scrolled = $(this).scrollTop() > 40;
            $('.tz-navbar').toggleClass('scrolled', scrolled);
            if (scrolled) {
                $('.tz-navbar').css('box-shadow', '0 2px 16px rgba(0,0,0,.1)');
            } else {
                $('.tz-navbar').css('box-shadow', '0 1px 4px rgba(0,0,0,.06)');
            }
        });

        // Quick-add animation
        $(document).on('click', '.tz-cart-btn', function () {
            var $btn = $(this);
            var originalHtml = $btn.html();
            $btn.html('<i class="mdi mdi-check"></i> Added!').css('background', '#3FB950');
            setTimeout(function () {
                $btn.html(originalHtml).css('background', '');
            }, 1400);
        });

    });

})(jQuery);
