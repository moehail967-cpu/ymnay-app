<script src="{{global_asset('assets/landlord/common/js/spectrum.min.js')}}"></script>
<script>
    (function(){
       "use strict";

   var spectrum_color_picker =  $('.spectrum_color_picker');
    $.each(spectrum_color_picker,function(){
        var el = $(this);
        var hexDisplay = el.closest('.color-picker-card').find('.color-hex-display');

        function updateColor(color) {
            var val = color ? color.toRgbString() : '';
            var hex = color ? color.toHexString() : '—';
            el.next('input').val(val);
            el.css({'background-color': val});
            if (hexDisplay.length) hexDisplay.text(hex);
        }

        el.spectrum({
            preferredFormat: "hex",
            showAlpha: true,
            showPalette: true,
            cancelText : '',
            showInput: true,
            allowEmpty:true,
            chooseText : '',
            maxSelectionSize: 2,
            color: el.next('input').val(),
            change: function(color) { updateColor(color); },
            move: function(color) { updateColor(color); },
            palette: [
                [
                    "{{get_static_option('site_color')}}",
                    "{{get_static_option('site_main_color_two')}}",
                    "{{get_static_option('site_secondary_color')}}",
                    "{{get_static_option('site_heading_color')}}",
                    "{{get_static_option('site_paragraph_color')}}",
                ]
            ]
        });

        el.on("dragstop.spectrum", function(e, color) {
            updateColor(color);
        });
    });


    })(jQuery);
</script>
