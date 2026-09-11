@php
    $uid       = 'gb-sc-' . substr(md5(uniqid()), 0, 8);
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
<section id="{{ $uid }}" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;background:{{ $background_color }};">
    <div class="container">

        @if(!empty($title))
        <div class="{{ $uid }}-head">
            <h2 class="{{ $uid }}-title">{{ $title }}</h2>
            <div class="{{ $uid }}-divider"></div>
        </div>
        @endif

        @if(!empty($stats) && count($stats) > 0)
        <div class="row g-4 justify-content-center">
            @foreach($stats as $stat)
            @if(!empty($stat['number']) || !empty($stat['label']))
            <div class="col-6 col-md-3">
                <div class="{{ $uid }}-item">
                    @if(!empty($stat['icon']))
                        <div class="{{ $uid }}-icon"><i class="{{ $stat['icon'] }}"></i></div>
                    @endif
                    <div class="{{ $uid }}-number">
                        {{ $stat['number'] ?? '' }}<span class="{{ $uid }}-suffix">{{ $stat['suffix'] ?? '' }}</span>
                    </div>
                    <div class="{{ $uid }}-label">{{ $stat['label'] ?? '' }}</div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
        @endif

    </div>
</section>

<style>
#{{ $uid }} { }
.{{ $uid }}-head { text-align: center; margin-bottom: 40px; }
.{{ $uid }}-title { font-size: clamp(1.3rem, 2.5vw, 1.8rem); font-weight: 700; color: var(--heading-color, #1a1a2e); margin-bottom: 12px; }
.{{ $uid }}-divider { width: 48px; height: 3px; background: var(--main-color-one, #0d6efd); border-radius: 2px; margin: 0 auto; }
.{{ $uid }}-item { text-align: center; padding: 28px 16px; border-radius: 12px; background: rgba({{ $pr }},{{ $pg }},{{ $pb }},.06); background: color-mix(in srgb, var(--main-color-one, rgba({{ $pr }},{{ $pg }},{{ $pb }},1)) 6%, transparent); transition: box-shadow .2s; }
.{{ $uid }}-item:hover { box-shadow: 0 6px 24px rgba(0,0,0,.08); }
.{{ $uid }}-icon { font-size: 2rem; color: var(--main-color-one, #0d6efd); margin-bottom: 12px; display: block; }
.{{ $uid }}-number { font-size: 2.2rem; font-weight: 800; color: var(--heading-color, #1a1a2e); line-height: 1; margin-bottom: 8px; }
.{{ $uid }}-suffix { font-size: 1.5rem; font-weight: 700; color: var(--main-color-one, #0d6efd); }
.{{ $uid }}-label { font-size: 0.85rem; color: var(--body-color, #6c757d); font-weight: 500; }
@media (max-width: 575px) {
    .{{ $uid }}-number { font-size: 1.8rem; }
}
</style>
