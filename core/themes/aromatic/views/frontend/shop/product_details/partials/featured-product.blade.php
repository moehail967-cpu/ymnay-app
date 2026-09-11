@if($related_products->isNotEmpty())
<section class="ar-pd-related-section">
    <div class="container">
        <div class="ar-pd-related-head">
            <h2 class="ar-section-heading">{{ __('Related Product') }}</h2>
            <div class="append-featured"></div>
        </div>
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="global-slick-init ar-pd-related-slider"
                     data-appendArrows=".append-featured"
                     data-infinite="true" data-arrows="true" data-dots="false"
                     data-slidesToShow="4" data-swipeToSlide="true"
                     data-autoplay="true" data-autoplaySpeed="2500"
                     data-prevArrow='<div class="prev-icon"><i class="las la-angle-left"></i></div>'
                     data-nextArrow='<div class="next-icon"><i class="las la-angle-right"></i></div>'
                     data-responsive='[{"breakpoint":1600,"settings":{"slidesToShow":3}},{"breakpoint":1200,"settings":{"slidesToShow":3}},{"breakpoint":992,"settings":{"slidesToShow":2}},{"breakpoint":576,"settings":{"slidesToShow":1}}]'>

                    @foreach($related_products as $rp)
                        @php
                            $rp_data    = theme_product_price($rp);
                            $rp_img     = theme_product_image($rp->image_id ?? null, 'grid');
                            $rp_url     = theme_product_url($rp->slug);
                            $rp_disc    = $rp_data['discount'];
                        @endphp
                        <div class="slick-slider-items">
                            <div class="ar-card">
                                <div class="ar-card-img">
                                    @if($rp_img)
                                        <a href="{{ $rp_url }}">
                                            <img src="{{ $rp_img }}" alt="{{ $rp->name }}" loading="lazy">
                                        </a>
                                    @else
                                        <a href="{{ $rp_url }}" class="ar-card-placeholder">
                                            <i class="las la-flask"></i>
                                        </a>
                                    @endif

                                    @if($rp_disc)
                                        <span class="ar-card-badge">{{ $rp_disc }}% {{ __('off') }}</span>
                                    @elseif(!empty($rp->badge))
                                        <span class="ar-card-badge">{{ $rp->badge->name }}</span>
                                    @endif
                                </div>
                                <div class="ar-card-body">
                                    <div class="ar-card-cat">{{ $rp->category?->name ?? '' }}</div>
                                    <div class="ar-card-name">
                                        <a href="{{ $rp_url }}">{{ \Illuminate\Support\Str::words($rp->name, 7) }}</a>
                                    </div>
                                    <div class="ar-card-price">
                                        <span class="ar-price-sale">{{ amount_with_currency_symbol($rp_data['sale_price']) }}</span>
                                        @if(!empty($rp_data['regular_price']))
                                            <span class="ar-price-orig">{{ amount_with_currency_symbol($rp_data['regular_price']) }}</span>
                                        @endif
                                    </div>
                                    <a href="{{ $rp_url }}" class="ar-card-atc">
                                        <i class="mdi mdi-eye-outline"></i> {{ __('View') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</section>
@endif
