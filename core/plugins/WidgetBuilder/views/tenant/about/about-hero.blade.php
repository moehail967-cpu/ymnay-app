@php
    $uid       = 'gb-ah-' . substr(md5(uniqid()), 0, 8);
    $themeSlug = tenant()?->theme_slug ?? 'default';
    $rawColor  = get_static_option('main_color_one_' . $themeSlug) ?? '#0d6efd';

    // DB may store color as 'rgb(r, g, b)' or '#rrggbb' — handle both
    if (preg_match('/rgb\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/i', $rawColor, $m)) {
        [$pr, $pg, $pb] = [(int)$m[1], (int)$m[2], (int)$m[3]];
    } else {
        $hex = ltrim(trim($rawColor), '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $pr = hexdec(substr($hex, 0, 2));
        $pg = hexdec(substr($hex, 2, 2));
        $pb = hexdec(substr($hex, 4, 2));
    }
@endphp
<section id="{{ $uid }}" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="row align-items-center g-5">

            {{-- Image --}}
            <div class="col-lg-6">
                @if(!empty($image_url))
                    <div class="{{ $uid }}-img-wrap">
                        <img src="{{ $image_url }}" alt="{{ $title }}" class="{{ $uid }}-img" loading="lazy">
                    </div>
                @else
                    <div class="{{ $uid }}-img-placeholder">
                        <i class="las la-image"></i>
                    </div>
                @endif
            </div>

            {{-- Content --}}
            <div class="col-lg-6">
                @if(!empty($section_tag))
                    <span class="{{ $uid }}-tag">{{ $section_tag }}</span>
                @endif

                @if(!empty($title))
                    <h2 class="{{ $uid }}-title">{{ $title }}</h2>
                @endif

                @if(!empty($description))
                    <p class="{{ $uid }}-desc">{{ $description }}</p>
                @endif
            </div>

        </div>
    </div>
</section>

<style>

.{{ $uid }}-img-wrap { border-radius: 12px; overflow: hidden; box-shadow: 0 12px 40px rgba(0,0,0,.1); }
.{{ $uid }}-img { width: 100%; height: auto; display: block; object-fit: cover; }
.{{ $uid }}-img-placeholder { width: 100%; height: 360px; background: var(--extra-light-color, #f0f0f0); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: var(--light-color, #ccc); }
.{{ $uid }}-tag { display: inline-block; font-size: 0.8rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: var(--main-color-one, #0d6efd); background: rgba({{ $pr }},{{ $pg }},{{ $pb }},.1); background: color-mix(in srgb, var(--main-color-one, rgba({{ $pr }},{{ $pg }},{{ $pb }},1)) 10%, transparent); padding: 4px 14px; border-radius: 20px; margin-bottom: 16px; }
.{{ $uid }}-title { font-size: clamp(1.5rem, 3vw, 2.2rem); font-weight: 700; color: var(--heading-color, #1a1a2e); line-height: 1.25; margin-bottom: 16px; }
.{{ $uid }}-desc { font-size: 1rem; color: var(--body-color, #5a6474); line-height: 1.75; margin-bottom: 0; }
@media (max-width: 767px) {
    .{{ $uid }}-img-wrap { margin-bottom: 24px; }
}
</style>
