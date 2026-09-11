@php
    $uid       = 'gb-tst-' . substr(md5(uniqid()), 0, 8);
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
    $chunks      = array_chunk($testimonials, 3);
    $totalSlides = count($chunks);
@endphp

<section id="{{ $uid }}" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;background:{{ $bg_color }};">
    <div class="container">

        @if(!empty($title))
        <div class="{{ $uid }}-head">
            <h2 class="{{ $uid }}-title">{{ $title }}</h2>
            @if(!empty($subtitle))
                <p class="{{ $uid }}-subtitle">{{ $subtitle }}</p>
            @endif
            <div class="{{ $uid }}-divider"></div>
        </div>
        @endif

        @if(!empty($chunks))
        <div class="{{ $uid }}-carousel">

            {{-- Slides --}}
            <div class="{{ $uid }}-slides-wrap" id="{{ $uid }}-slides">
                @foreach($chunks as $si => $group)
                <div class="{{ $uid }}-slide{{ $si === 0 ? ' active' : '' }}" data-slide="{{ $si }}">
                    <div class="{{ $uid }}-row">
                        @foreach($group as $t)
                        @php
                            $avatarUrl = '';
                            if (!empty($t['avatar'])) {
                                $av = is_array($t['avatar']) ? ($t['avatar']['id'] ?? null) : null;
                                if ($av) {
                                    $img = get_attachment_image_by_id($av);
                                    $avatarUrl = $img['img_url'] ?? '';
                                }
                            }
                            $initial = strtoupper(substr($t['name'] ?? 'A', 0, 1));
                        @endphp
                        <div class="{{ $uid }}-col">
                            <div class="{{ $uid }}-card">
                                <div class="{{ $uid }}-quote">&ldquo;</div>

                                <div class="{{ $uid }}-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="las la-star{{ $i <= (int)($t['rating'] ?? 5) ? '' : ' ' . $uid . '-star-empty' }}"></i>
                                    @endfor
                                </div>

                                @if(!empty($t['review']))
                                    <p class="{{ $uid }}-review">{{ $t['review'] }}</p>
                                @endif

                                <div class="{{ $uid }}-author">
                                    @if(!empty($avatarUrl))
                                        <img src="{{ $avatarUrl }}" alt="{{ $t['name'] ?? '' }}" class="{{ $uid }}-avatar">
                                    @else
                                        <div class="{{ $uid }}-avatar-initial">{{ $initial }}</div>
                                    @endif
                                    <div class="{{ $uid }}-author-info">
                                        <strong class="{{ $uid }}-name">{{ $t['name'] ?? '' }}</strong>
                                        @if(!empty($t['role']))
                                            <span class="{{ $uid }}-role">{{ $t['role'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Arrows (only if more than 1 slide) --}}
            @if($totalSlides > 1)
            <button class="{{ $uid }}-arr {{ $uid }}-prev" id="{{ $uid }}-prev" aria-label="{{ __('Previous') }}">
                <i class="las la-angle-left"></i>
            </button>
            <button class="{{ $uid }}-arr {{ $uid }}-next" id="{{ $uid }}-next" aria-label="{{ __('Next') }}">
                <i class="las la-angle-right"></i>
            </button>

            {{-- Dots --}}
            <div class="{{ $uid }}-dots" id="{{ $uid }}-dots">
                @foreach($chunks as $si => $group)
                <button class="{{ $uid }}-dot{{ $si === 0 ? ' active' : '' }}"
                        data-index="{{ $si }}" aria-label="{{ __('Slide') }} {{ $si + 1 }}"></button>
                @endforeach
            </div>
            @endif

        </div>
        @endif

    </div>
</section>

<style>
#{{ $uid }} { }
.{{ $uid }}-head { text-align: center; margin-bottom: 48px; }
.{{ $uid }}-title { font-size: clamp(1.4rem, 2.5vw, 2rem); font-weight: 700; color: var(--heading-color, #1a1a2e); margin-bottom: 10px; }
.{{ $uid }}-subtitle { font-size: 1rem; color: var(--body-color, #6c757d); margin-bottom: 16px; }
.{{ $uid }}-divider { width: 48px; height: 3px; background: var(--main-color-one, #0d6efd); border-radius: 2px; margin: 0 auto; }

/* Carousel */
.{{ $uid }}-carousel { position: relative; overflow: hidden; }
.{{ $uid }}-slides-wrap { position: relative; min-height: 100px; }
.{{ $uid }}-slide { display: none; }
.{{ $uid }}-slide.active { display: block; }

/* Card row */
.{{ $uid }}-row { display: flex; gap: 24px; align-items: stretch; padding-bottom: 48px; }
.{{ $uid }}-col { flex: 1; min-width: 0; }

/* Card */
.{{ $uid }}-card {
    background: #fff;
    border-radius: 16px;
    padding: 32px 28px 24px;
    box-shadow: 0 4px 24px rgba(0,0,0,.06);
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: box-shadow .25s, transform .25s;
}
.{{ $uid }}-card:hover {
    box-shadow: 0 8px 32px rgba({{ $pr }},{{ $pg }},{{ $pb }},.15); box-shadow: 0 8px 32px color-mix(in srgb, var(--main-color-one, rgba({{ $pr }},{{ $pg }},{{ $pb }},1)) 15%, transparent);
    transform: translateY(-3px);
}
.{{ $uid }}-quote {
    font-size: 4rem; line-height: 1;
    color: var(--main-color-one, #0d6efd); opacity: .18;
    font-family: Georgia, serif;
    position: absolute; top: 16px; right: 24px;
}
.{{ $uid }}-stars { margin-bottom: 14px; }
.{{ $uid }}-stars .las.la-star { color: #f59e0b; font-size: 0.9rem; }
.{{ $uid }}-star-empty { color: #d1d5db !important; }
.{{ $uid }}-review {
    font-size: 0.93rem; color: var(--body-color, #4b5563);
    line-height: 1.75; flex: 1; margin-bottom: 24px; font-style: italic;
}
.{{ $uid }}-author { display: flex; align-items: center; gap: 12px; padding-top: 20px; border-top: 1px solid rgba({{ $pr }},{{ $pg }},{{ $pb }},.12); border-top-color: color-mix(in srgb, var(--main-color-one, rgba({{ $pr }},{{ $pg }},{{ $pb }},1)) 12%, transparent); }
.{{ $uid }}-avatar { width: 46px; height: 46px; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 2px solid rgba({{ $pr }},{{ $pg }},{{ $pb }},.2); border-color: color-mix(in srgb, var(--main-color-one, rgba({{ $pr }},{{ $pg }},{{ $pb }},1)) 20%, transparent); }
.{{ $uid }}-avatar-initial {
    width: 46px; height: 46px; border-radius: 50%;
    background: var(--main-color-one, #0d6efd); color: #fff;
    font-weight: 700; font-size: 1.1rem;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.{{ $uid }}-author-info { display: flex; flex-direction: column; }
.{{ $uid }}-name { font-size: 0.9rem; font-weight: 700; color: var(--heading-color, #1a1a2e); line-height: 1.3; }
.{{ $uid }}-role { font-size: 0.78rem; color: var(--main-color-one, #0d6efd); font-weight: 500; margin-top: 2px; }

/* Arrows */
.{{ $uid }}-arr {
    position: absolute; top: calc(50% - 32px); transform: translateY(-50%);
    width: 38px; height: 38px; border-radius: 50%;
    border: 1.5px solid rgba({{ $pr }},{{ $pg }},{{ $pb }},.25); border-color: color-mix(in srgb, var(--main-color-one, rgba({{ $pr }},{{ $pg }},{{ $pb }},1)) 25%, transparent);
    background: #fff;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: var(--main-color-one, #0d6efd);
    font-size: 14px; z-index: 2;
    transition: background .2s, border-color .2s, color .2s;
    box-shadow: 0 2px 8px rgba(0,0,0,.08); padding: 0;
}
.{{ $uid }}-arr:hover { background: var(--main-color-one, #0d6efd); border-color: var(--main-color-one, #0d6efd); color: #fff; }
.{{ $uid }}-prev { left: -8px; }
.{{ $uid }}-next { right: -8px; }

/* Dots */
.{{ $uid }}-dots { display: flex; align-items: center; justify-content: center; gap: 8px; padding-bottom: 4px; }
.{{ $uid }}-dot {
    width: 8px; height: 8px; border-radius: 50%;
    border: none; background: rgba({{ $pr }},{{ $pg }},{{ $pb }},.2); background: color-mix(in srgb, var(--main-color-one, rgba({{ $pr }},{{ $pg }},{{ $pb }},1)) 20%, transparent);
    cursor: pointer; padding: 0; transition: background .2s, width .2s;
}
.{{ $uid }}-dot.active { background: var(--main-color-one, #0d6efd); width: 22px; border-radius: 4px; }

@media (max-width: 767px) {
    .{{ $uid }}-row { flex-direction: column; gap: 16px; }
    .{{ $uid }}-card { padding: 24px 20px 20px; }
    .{{ $uid }}-prev { left: 0; }
    .{{ $uid }}-next { right: 0; }
}
</style>

<script>
(function () {
    var total = {{ $totalSlides }};
    if (total < 2) return;
    var current = 0;
    var slides  = document.querySelectorAll('#{{ $uid }}-slides .{{ $uid }}-slide');
    var dots    = document.querySelectorAll('#{{ $uid }}-dots .{{ $uid }}-dot');
    var prev    = document.getElementById('{{ $uid }}-prev');
    var next    = document.getElementById('{{ $uid }}-next');
    var timer   = null;

    function goTo(idx) {
        slides[current].classList.remove('active');
        if (dots[current]) dots[current].classList.remove('active');
        current = (idx + total) % total;
        slides[current].classList.add('active');
        if (dots[current]) dots[current].classList.add('active');
    }

    function startAuto() {
        timer = setInterval(function () { goTo(current + 1); }, 5000);
    }

    if (prev) prev.addEventListener('click', function () { clearInterval(timer); goTo(current - 1); startAuto(); });
    if (next) next.addEventListener('click', function () { clearInterval(timer); goTo(current + 1); startAuto(); });

    dots.forEach(function (dot, i) {
        dot.addEventListener('click', function () { clearInterval(timer); goTo(i); startAuto(); });
    });

    startAuto();
})();
</script>
