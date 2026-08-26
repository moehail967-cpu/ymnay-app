{{-- Casual: Flash Sale Banner --}}
<section class="cs-flash-banner-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="cs-flash-banner-card">

            {{-- Wave decorations --}}
            <span class="cs-flash-wave cs-flash-wave-1" aria-hidden="true"></span>
            <span class="cs-flash-wave cs-flash-wave-2" aria-hidden="true"></span>

            {{-- Left: text content --}}
            <div class="cs-flash-banner-left">
                <h2 class="cs-flash-banner-title">{{ $title }}</h2>

                @if($countdown_end)
                    <div class="cs-flash-countdown" id="{{ $countdown_id }}">
                        <span class="cs-flash-count-item"><span class="cs-count-num" data-part="days">00</span><span class="cs-count-label">{{ __('Days') }}</span></span>
                        <span class="cs-count-sep">:</span>
                        <span class="cs-flash-count-item"><span class="cs-count-num" data-part="hours">00</span><span class="cs-count-label">{{ __('Hrs') }}</span></span>
                        <span class="cs-count-sep">:</span>
                        <span class="cs-flash-count-item"><span class="cs-count-num" data-part="mins">00</span><span class="cs-count-label">{{ __('Min') }}</span></span>
                        <span class="cs-count-sep">:</span>
                        <span class="cs-flash-count-item"><span class="cs-count-num" data-part="secs">00</span><span class="cs-count-label">{{ __('Sec') }}</span></span>
                    </div>
                    <p class="cs-flash-banner-sub cs-flash-finished-msg" id="{{ $countdown_id }}_msg" style="display:none;">{{ __('The countdown is finished!') }}</p>
                    <script>
                    (function(){
                        var end  = new Date("{{ $countdown_end }}").getTime();
                        var wrap = document.getElementById("{{ $countdown_id }}");
                        var msg  = document.getElementById("{{ $countdown_id }}_msg");
                        if(!wrap) return;
                        function tick(){
                            var now  = new Date().getTime();
                            var diff = end - now;
                            if(diff <= 0){
                                wrap.style.display = 'none';
                                if(msg) msg.style.display = 'block';
                                return;
                            }
                            var d = Math.floor(diff / 86400000);
                            var h = Math.floor((diff % 86400000) / 3600000);
                            var m = Math.floor((diff % 3600000)  / 60000);
                            var s = Math.floor((diff % 60000)    / 1000);
                            function pad(n){ return n < 10 ? '0'+n : n; }
                            wrap.querySelector('[data-part="days"]').textContent  = pad(d);
                            wrap.querySelector('[data-part="hours"]').textContent = pad(h);
                            wrap.querySelector('[data-part="mins"]').textContent  = pad(m);
                            wrap.querySelector('[data-part="secs"]').textContent  = pad(s);
                            setTimeout(tick, 1000);
                        }
                        tick();
                    })();
                    </script>
                @else
                    <p class="cs-flash-banner-sub">{{ $subtitle }}</p>
                @endif

                <a href="{{ $link_url }}" class="cs-flash-store-link">
                    <span class="cs-flash-store-icon"><i class="las la-arrow-right"></i></span>
                    <span class="cs-flash-store-text">{{ $link_text }}</span>
                </a>
            </div>

            {{-- Center: banner image --}}
            <div class="cs-flash-banner-center">
                @if($banner_url)
                    <img src="{{ $banner_url }}" alt="{{ $title }}" class="cs-flash-banner-img" loading="lazy">
                @else
                    <div class="cs-flash-banner-img-ph"><i class="las la-image"></i></div>
                @endif
            </div>

            {{-- Right: discount badge --}}
            @if($discount_text)
            <div class="cs-flash-banner-right">
                <div class="cs-flash-discount-badge">
                    <span>{{ $discount_text }}</span>
                </div>
            </div>
            @endif

        </div>
    </div>
</section>
