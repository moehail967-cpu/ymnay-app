
        (function($){
            $(function () {
                // If counters store their target in data-count, copy it into the element text
                $('.counter').each(function(){
                    var $el = $(this);
                    var target = $el.data('count');
                    if (typeof target !== 'undefined' && target !== null && target.toString().trim() !== '') {
                        $el.text(target);
                    }
                });

                // Initialize counterUp (only if plugin is available)
                if ($.fn && $.fn.counterUp) {
                    $('.counter').counterUp({
                        delay: 10, // Delay in milliseconds
                        time: 2000 // Total animation time in milliseconds
                    });
                } else {
                    console.warn('counterUp plugin not found.');
                }
            });
        })(jQuery);
