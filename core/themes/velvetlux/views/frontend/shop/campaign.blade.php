@extends('tenant.frontend.frontend-page-master')

@section('title')
    {!! $campaign->title !!}
@endsection

@section('page-title')
    {!! $campaign->title !!}
@endsection

@section('content')
    <section class="shop-area padding-top-100 padding-bottom-50">
        <div class="container-one">
            <div class="shop-contents-wrapper">
                <div class="shop-grid-contents">
                    <div class="grid-product-list">
                        <div id="tab-grid2" class="tab-content-item active">
                            <div class="row mt-4 gy-5">
                                @foreach($products as $product)
                                    @php
                                        if (!$product) continue;
                                        $data = get_product_dynamic_price($product);
                                        $campaign_name = $data['campaign_name'];
                                        $regular_price = $data['regular_price'];
                                        $sale_price    = $data['sale_price'];
                                        $discount      = $data['discount'];
                                    @endphp

                                    <div class="col-xxl-4 col-lg-6 col-sm-6">
                                        <div class="global-card no-shadow radius-0 pb-0">
                                            <div class="global-card-thumb">
                                                <a href="{{ dynamicRoute($product->slug) }}">
                                                    {!! render_image_markup_by_attachment_id($product->image_id, '', 'grid') !!}
                                                </a>
                                                <div class="global-card-thumb-badge right-side">
                                                    @if($discount != null)
                                                        <span class="global-card-thumb-badge-box bg-color-two"> {{ $discount }}% {{ __('off') }} </span>
                                                    @endif
                                                    @if(!empty($product->badge))
                                                        <span class="global-card-thumb-badge-box bg-color-new"> {{ $product?->badge?->name }} </span>
                                                    @endif
                                                </div>

                                                @include(include_theme_path('shop.partials.product-options'))
                                            </div>

                                            <div class="flash-countdown-camp flash-countdown-style-1 flash-countdown index-02" data-date="{{ $campaign->end_date }}">
                                                <div class="single-box">
                                                    <span class="counter-days item"></span>
                                                    <span class="label item">{{ __('Day') }}</span>
                                                </div>
                                                <div class="single-box">
                                                    <span class="counter-hours item"></span>
                                                    <span class="label item">{{ __('Hour') }}</span>
                                                </div>
                                                <div class="single-box">
                                                    <span class="counter-minutes item"></span>
                                                    <span class="label item">{{ __('Minute') }}</span>
                                                </div>
                                                <div class="single-box">
                                                    <span class="counter-seconds item"></span>
                                                    <span class="label item">{{ __('Second') }}</span>
                                                </div>
                                            </div>

                                            <div class="global-card-contents">
                                                <div class="global-card-contents-flex">
                                                    <h5 class="global-card-contents-title">
                                                        <a href="{{ dynamicRoute($product->slug) }}"> {{ Str::words($product->name, 4) }} </a>
                                                    </h5>
                                                    {!! render_product_star_rating_markup_with_count($product) !!}
                                                </div>
                                                <div class="price-update-through mt-3">
                                                    <span class="flash-prices color-two"> {{ amount_with_currency_symbol($sale_price) }} </span>
                                                    <span class="flash-old-prices"> {{ $regular_price != null ? amount_with_currency_symbol($regular_price) : '' }} </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include(include_theme_path('shop.partials.shop-footer'))
@endsection

@section('scripts')
    <script src="{{ global_asset('assets/tenant/frontend/js/loopcounter.js') }}"></script>
    <script>
        $(function () {
            $(document).ready(function () {
                loopcounter('flash-countdown');
            });

            $(document).on('click', '.wishlist-btn', function (e) {
                let el = $(this);
                let product = el.data('product_id');
                $.ajax({
                    url: '{{ theme_add_to_wishlist_url() }}',
                    type: 'GET',
                    data: { product_id: product },
                    beforeSend: function () { $('.loader').show(); },
                    success: function (data) {
                        $('.loader').hide();
                        if (data.type === 'success') { toastr.success(data.msg); } else { toastr.error(data.msg); }
                    },
                    error: function () { $('.loader').hide(); }
                });
            });

            $(document).on('click', 'a.popup-modal', function (e) {
                let el = $(this).parent();
                let id = el.data('id');
                let modal = $('#product-modal');
                $.ajax({
                    type: 'GET',
                    url: '{{ theme_quick_view_url() }}',
                    data: { 'id': id },
                    beforeSend: function () { $('.loader').show(); },
                    success: function (data) {
                        modal.html(data.product_modal);
                        setInterval(() => { $('.loader').hide(); }, 700);
                    },
                    error: function () {}
                });
            });
        });
    </script>
@endsection
