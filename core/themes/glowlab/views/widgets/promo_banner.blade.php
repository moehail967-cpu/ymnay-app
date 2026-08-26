<section class="gl-widget-promo">
<div class="container">
    <div class="row align-items-center">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <h2 class="gl-promo-title">{!! $title !!}</h2>
                @if(!empty($text))
                    <p class="gl-promo-text">{{ $text }}</p>
                @endif
                <div class="gl-promo-actions">
                    @if(!empty($button_text))
                        <a href="{{ $button_url }}" class="gl-btn gl-btn-gold">
                            <i class="las la-flask"></i> {{ $button_text }}
                        </a>
                    @endif
                    @if(!empty($button2_text))
                        <a href="{{ $button2_url }}" class="gl-btn gl-btn-ghost">{{ $button2_text }}</a>
                    @endif
                </div>
            </div>
            <div class="col-lg-5 d-flex align-items-center justify-content-center">
                @if(!empty($promo_image))
                    <img src="{{ $promo_image }}" alt="Promo" class="img-fluid rounded" style="width: 300px; height: 300px; object-fit: cover; margin: 0 auto; display: block;">
                @else
                    <div class="gl-promo-emojis">
                    @if(!empty($icon1))<span class="gl-promo-emoji">{{ $icon1 }}</span>@endif
                    @if(!empty($icon2))<span class="gl-promo-emoji gl-promo-emoji-mid">{{ $icon2 }}</span>@endif
                </div>
                @endif
            </div>
    </div>
</div>
</section>
