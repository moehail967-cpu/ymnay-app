{{-- SportZone: Promo Banner Widget --}}
<section class="sz-promo">
    <div class="container">
        <div class="row align-items-center">

            {{-- Left: Text + CTA --}}
            <div class="col-lg-7 mb-4 mb-lg-0">
                @if($tag)
                    <span class="sz-section-tag">{{ $tag }}</span>
                @endif
                <h2>{!! $title !!}</h2>
                @if($text)
                    <p>{{ $text }}</p>
                @endif
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ $button_url }}" class="sz-btn sz-btn-navy">
                        <i class="mdi mdi-account-group"></i> {{ $button_text }}
                    </a>
                    @if($button2_text)
                        <a href="{{ $button2_url }}" class="sz-btn sz-btn-outline">
                            {{ $button2_text }}
                        </a>
                    @endif
                </div>
            </div>

            {{-- Right: Trophy decoration --}}
            <div class="col-lg-5 text-center d-none d-lg-block">
                @if(!empty($promo_image))
                    <img src="{{ $promo_image }}" alt="Promo" class="img-fluid rounded" style="width: 300px; height: 300px; object-fit: cover; margin: 0 auto; display: block;">
                @else
                    <div class="sz-promo-icons">
                    <i class="mdi mdi-trophy-variant"></i>
                    <i class="mdi mdi-soccer"></i>
                    <i class="mdi mdi-medal"></i>
                </div>
                @endif
            </div>

        </div>
    </div>
</section>
