<section class="gc-widget-promo">
<div class="container">
    <div class="gc-promo-inner">
        <div class="row align-items-center">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <h2 class="gc-promo-title">{!! $title !!}</h2>
                @if(!empty($text))
                    <p class="gc-promo-text">{{ $text }}</p>
                @endif
                <div class="gc-promo-actions">
                    @if(!empty($button_text))
                        <a href="{{ $button_url }}" class="gc-btn gc-btn-rose">
                            <i class="mdi mdi-pencil-outline"></i> {{ $button_text }}
                        </a>
                    @endif
                    @if(!empty($button2_text))
                        <a href="{{ $button2_url }}" class="gc-btn gc-btn-ghost-light">
                            {{ $button2_text }}
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-lg-5">
                @if(!empty($promo_image))
                    <img src="{{ $promo_image }}" alt="Promo" class="img-fluid rounded" style="width: 300px; height: 300px; object-fit: cover; margin: 0 auto; display: block;">
                @else
                    <div class="gc-promo-emojis">
                    @if(!empty($icon1))<span class="gc-promo-emoji">{{ $icon1 }}</span>@endif
                    @if(!empty($icon2))<span class="gc-promo-emoji gc-promo-emoji-lg">{{ $icon2 }}</span>@endif
                </div>
            </div>
                @endif
            </div>
    </div>
</div>
</section>
