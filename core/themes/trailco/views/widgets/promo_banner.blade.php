{{-- TrailCo: Promo Banner Widget --}}
<section class="tc-promo-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <h2>{!! $title !!}</h2>
                @if($subtitle)
                    <p>{{ $subtitle }}</p>
                @endif
                @if($button_text)
                    <a href="{{ $button_url }}" class="tc-btn tc-btn-terra">
                        <i class="mdi mdi-map-marker-outline"></i> {{ $button_text }}
                    </a>
                @endif
            </div>
            <div class="col-lg-5 text-center">
                @if(!empty($promo_image))
                    <img src="{{ $promo_image }}" alt="Promo" class="img-fluid rounded" style="width: 300px; height: 300px; object-fit: cover; margin: 0 auto; display: block;">
                @else
                    <div class="tc-promo-emoji" aria-hidden="true">⛺🏔️🎒</div>
                @endif
            </div>
        </div>
    </div>
</section>
