<section class="fp-promo">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <h2 class="fp-promo-title">{!! $title !!}</h2>
                @if(!empty($text))
                    <p class="fp-promo-text">{{ $text }}</p>
                @endif
                <a href="{{ $button_url }}" class="fp-btn fp-btn-dark">
                    <i class="mdi mdi-lightning-bolt"></i> {{ $button_text }}
                </a>
            </div>
            <div class="col-lg-5 text-center">
                @if(!empty($promo_image))
                    <img src="{{ $promo_image }}" alt="Promo" class="img-fluid rounded" style="width: 300px; height: 300px; object-fit: cover; margin: 0 auto; display: block;">
                @else
                    <div class="fp-promo-icons">
                    <i class="mdi mdi-arm-flex fp-promo-icon"></i>
                    <i class="mdi mdi-lightning-bolt fp-promo-icon fp-promo-icon-accent"></i>
                    <i class="mdi mdi-weight-lifter fp-promo-icon"></i>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
