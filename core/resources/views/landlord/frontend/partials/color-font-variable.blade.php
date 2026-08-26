@php
    // Convert #HEX or rgb(r,g,b) color to "r, g, b" string for use in rgba()
    $toRgb = function($c, $default) {
        $c = trim($c ?? '');
        if (!$c) return $default;
        if (preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $c, $m)) {
            $hex = strlen($m[1]) === 3
                ? str_repeat($m[1][0],2).str_repeat($m[1][1],2).str_repeat($m[1][2],2)
                : $m[1];
            return hexdec(substr($hex,0,2)).', '.hexdec(substr($hex,2,2)).', '.hexdec(substr($hex,4,2));
        }
        // Handle rgb(r,g,b) format
        return preg_replace('/[^0-9,\s]/', '', $c);
    };
    $c1  = get_static_option('main_color_one',      '#0D4D54');
    $c2  = get_static_option('main_color_two',      '#93E720');
    $c3  = get_static_option('main_color_three',    '#599A8D');
    $c4  = get_static_option('main_color_four',     '#1E88E5');
    $cs1 = get_static_option('secondary_color',     '#FFFFFF');
    $cs2 = get_static_option('secondary_color_two', '#373F53');
    $cr  = get_static_option('review_color',        '#FABE50');
@endphp
<style>
    :root {
        --main-color-one:           {{ $c1 }};
        --main-color-one-rgb:       {{ $toRgb($c1,  '13, 77, 84') }};
        --main-color-two:           {{ $c2 }};
        --main-color-two-rgb:       {{ $toRgb($c2,  '147, 231, 32') }};
        --main-color-three:         {{ $c3 }};
        --main-color-three-rgb:     {{ $toRgb($c3,  '89, 154, 141') }};
        --main-color-four:          {{ $c4 }};
        --main-color-four-rgb:      {{ $toRgb($c4,  '30, 136, 229') }};
        --secondary-color:          {{ $cs1 }};
        --secondary-color-rgb:      {{ $toRgb($cs1, '255, 255, 255') }};
        --secondary-color-two:      {{ $cs2 }};
        --secondary-color-two-rgb:  {{ $toRgb($cs2, '55, 63, 83') }};
        --section-bg-1:  {{ get_static_option('section_bg_1', '#FFFFFF') }};
        --section-bg-2:  {{ get_static_option('section_bg_2', '#FFF6EE') }};
        --section-bg-3:  {{ get_static_option('section_bg_3', '#F4F8FB') }};
        --section-bg-4:  {{ get_static_option('section_bg_4', '#F2F3FB') }};
        --section-bg-5:  {{ get_static_option('section_bg_5', '#F9F5F2') }};
        --section-bg-6:  {{ get_static_option('section_bg_6', '#E5EFF8') }};
        --heading-color:     {{ get_static_option('heading_color',     '#000000') }};
        --body-color:        {{ get_static_option('body_color',        '#666666') }};
        --light-color:       {{ get_static_option('light_color',       '#FFFFFF') }};
        --extra-light-color: {{ get_static_option('extra_light_color', '#888888') }};
        --review-color:      {{ $cr }};
        --review-color-rgb:  {{ $toRgb($cr, '250, 190, 80') }};
        --new-color:         {{ get_static_option('new_color',         '#5AB27E') }};
        --sectionC:          {{ $c1 }};
        --color-bg-sidebar:  {{ get_static_option('admin_sidebar_bg',  '#E7F7F7') }};

        --heading-font: "{{ get_static_option('heading_font_family', 'Roboto Slab') }}",serif;
        --body-font:    "{{ get_static_option('body_font_family',    'Manrope')     }}",sans-serif;
    }
</style>
