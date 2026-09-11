@php
    $uid = 'xgpb_cb_' . uniqid();
    $bg_style = '';
    if (!empty($bg_image_url)) {
        $bg_style = "background-image: linear-gradient(rgba(0,0,0,.5), rgba(0,0,0,.5)), url('{$bg_image_url}'); background-size: cover; background-position: center;";
    } else {
        $bg_style = "background-color: {$bg_color};";
    }
@endphp

<section class="xgpb-campaign-banner" id="{{ $uid }}"
         style="padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px; {{ $bg_style }}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center text-white">
                @if(!empty($badge_text))
                    <span class="xgpb-campaign-badge badge mb-3">{{ $badge_text }}</span>
                @endif
                @if(!empty($title))
                    <h2 class="xgpb-campaign-title">{{ $title }}</h2>
                @endif
                @if(!empty($subtitle))
                    <p class="xgpb-campaign-subtitle">{{ $subtitle }}</p>
                @endif

                @if($show_countdown && !empty($end_date))
                    <div class="xgpb-countdown d-flex justify-content-center gap-3 my-4" data-end="{{ $end_date }}">
                        @foreach(['days' => 'Days', 'hours' => 'Hours', 'mins' => 'Mins', 'secs' => 'Secs'] as $key => $label)
                            <div class="xgpb-countdown-unit text-center">
                                <div class="xgpb-countdown-num" id="{{ $uid }}_{{ $key }}">00</div>
                                <div class="xgpb-countdown-label">{{ $label }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if(!empty($button_text))
                    <a href="{{ $button_url }}" class="btn btn-light btn-lg xgpb-campaign-btn mt-2 fw-semibold px-5">
                        {{ $button_text }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>

@if($show_countdown && !empty($end_date))
<script>
(function() {
    var end = new Date("{{ $end_date }}").getTime();
    var uid = "{{ $uid }}";
    var timer = setInterval(function() {
        var diff = end - Date.now();
        if (diff <= 0) { clearInterval(timer); return; }
        var d = Math.floor(diff / 86400000);
        var h = Math.floor((diff % 86400000) / 3600000);
        var m = Math.floor((diff % 3600000) / 60000);
        var s = Math.floor((diff % 60000) / 1000);
        var pad = function(n) { return String(n).padStart(2, '0'); };
        document.getElementById(uid + '_days').textContent  = pad(d);
        document.getElementById(uid + '_hours').textContent = pad(h);
        document.getElementById(uid + '_mins').textContent  = pad(m);
        document.getElementById(uid + '_secs').textContent  = pad(s);
    }, 1000);
})();
</script>
@endif

<style>
.xgpb-campaign-banner { position: relative; }
.xgpb-campaign-badge { background: rgba(255,255,255,.2); color: #fff; font-size: .875rem; padding: .4em .9em; border-radius: 20px; letter-spacing: .5px; }
.xgpb-campaign-title { font-size: clamp(1.5rem, 3.5vw, 2.5rem); font-weight: 700; margin-bottom: .75rem; }
.xgpb-campaign-subtitle { font-size: 1.1rem; opacity: .85; }
.xgpb-countdown-num { font-size: 2.5rem; font-weight: 700; background: rgba(255,255,255,.15); border-radius: 8px; padding: .25em .6em; min-width: 70px; display: block; }
.xgpb-countdown-label { font-size: .8rem; opacity: .8; margin-top: .25rem; }
.xgpb-campaign-btn { border-radius: 8px; color: var(--main-color-one, #0d6efd); }
</style>
