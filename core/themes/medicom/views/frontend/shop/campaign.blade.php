@extends('tenant.frontend.frontend-page-master')

@section('title')
    {!! $campaign->title !!}
@endsection

@section('page-title')
    {!! $campaign->title !!}
@endsection

@section('content')
    <!-- Campaign Page -->
    <section class="mc-page-section">
        <div class="container">

            <!-- Campaign Hero -->
            <div class="mc-campaign-hero">
                <div class="mc-campaign-meta">
                    <h1 class="mc-campaign-title">{!! $campaign->title !!}</h1>
                    @if(!empty($campaign->description))
                        <p class="mc-campaign-desc">{!! $campaign->description !!}</p>
                    @endif
                </div>
                <div class="mc-countdown flash-countdown-camp flash-countdown" data-date="{{ $campaign->end_date }}">
                    <div class="mc-countdown-item">
                        <span class="mc-countdown-value counter-days item"></span>
                        <span class="mc-countdown-label item">{{ __('Days') }}</span>
                    </div>
                    <div class="mc-countdown-sep">:</div>
                    <div class="mc-countdown-item">
                        <span class="mc-countdown-value counter-hours item"></span>
                        <span class="mc-countdown-label item">{{ __('Hours') }}</span>
                    </div>
                    <div class="mc-countdown-sep">:</div>
                    <div class="mc-countdown-item">
                        <span class="mc-countdown-value counter-minutes item"></span>
                        <span class="mc-countdown-label item">{{ __('Mins') }}</span>
                    </div>
                    <div class="mc-countdown-sep">:</div>
                    <div class="mc-countdown-item">
                        <span class="mc-countdown-value counter-seconds item"></span>
                        <span class="mc-countdown-label item">{{ __('Secs') }}</span>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="grid-product-list">
                <div class="mc-shop-grid mt-5">
                    @foreach($products ?? [] as $product)
                        @php
                            if (!$product) continue;
                            $data = get_product_dynamic_price($product);
                            $campaign_name = $data['campaign_name'];
                            $regular_price = $data['regular_price'];
                            $sale_price    = $data['sale_price'];
                            $discount      = $data['discount'];
                            $final_price   = calculatePrice($sale_price, $product);

                            $img_data = get_attachment_image_by_id($product->image_id, 'grid');
                            $img_url  = !empty($img_data) ? $img_data['img_url'] : '';
                            $img_alt  = !empty($img_data) ? $img_data['img_alt'] : $product->name;
                        @endphp

                        <div class="mc-card">
                            <div class="mc-card-img-wrap">
                                <a href="{{ to_product_details($product->slug) }}">
                                    <img class="mc-card-img" src="{{ $img_url }}" alt="{{ $img_alt }}">
                                </a>

                                <!-- Wishlist -->
                                <button class="mc-card-wish add-to-wishlist-btn" data-product_id="{{ $product->id }}" title="{{ __('Add to Wishlist') }}">
                                    <i class="lar la-heart"></i>
                                </button>

                                <!-- Discount badge -->
                                @if(!empty($discount))
                                    <span class="mc-card-badge">{{ $discount }}% {{ __('off') }}</span>
                                @endif

                                <!-- Custom badge -->
                                @if(!empty($product->badge))
                                    <span class="mc-card-badge mc-card-badge-new">{{ $product->badge->name }}</span>
                                @endif

                                @include(include_theme_path('shop.partials.product-options'))
                            </div>

                            <div class="mc-card-body">
                                <a href="{{ to_product_details($product->slug) }}" class="mc-card-name">
                                    {{ product_limited_text($product->name, 'title') }}
                                </a>

                                <!-- Countdown per card -->
                                <div class="mc-card-countdown flash-countdown-camp flash-countdown" data-date="{{ $campaign->end_date }}" style="display:flex;gap:6px;font-size:11px;color:#1A85ED;font-weight:700;margin:4px 0;">
                                    <span class="counter-days item"></span><span>d</span>
                                    <span class="counter-hours item"></span><span>h</span>
                                    <span class="counter-minutes item"></span><span>m</span>
                                    <span class="counter-seconds item"></span><span>s</span>
                                </div>

                                <div class="mc-card-footer">
                                    <div class="mc-card-prices">
                                        <span class="mc-card-price flash-prices">{{ amount_with_currency_symbol($final_price) }}</span>
                                        @if(!empty($regular_price) && $regular_price != $final_price)
                                            <span class="mc-card-old flash-old-prices">{{ amount_with_currency_symbol($regular_price) }}</span>
                                        @endif
                                    </div>

                                    @if($product->inventory_detail_count < 1)
                                        <button class="mc-card-atc add-to-cart-btn cart-loading"
                                                data-product_id="{{ $product->id }}"
                                                title="{{ __('Add to Cart') }}">
                                            <i class="las la-shopping-bag"></i>
                                        </button>
                                    @else
                                        <button class="mc-card-atc product-quick-view-ajax cart-loading"
                                                data-action-route="{{ theme_quick_view_url($product->slug) }}"
                                                title="{{ __('Quick View') }}">
                                            <i class="las la-shopping-bag"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
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

            $(document).on('click', 'ul.pagination .page-item a', function (e) {
                e.preventDefault();
                filter_product_request($(this).data('page'));
            });

            $(document).on('click', '.ad-values, .active-list .list, .price-search-btn', function (e) {
                let currentPage = $(".pagination .page-item .page-link.active").attr("data-page");
                filter_product_request(currentPage);
            });

            // .add-to-wishlist-btn handled globally in footer.blade.php

            $(document).on('click', '.click-hide-filter .click-hide', function () {
                let filter_name = '.' + $(this).parent().data('filter') + ' .active';
                $(filter_name).removeClass('active');
                filter_product_request();
                $(this).parent().remove();
                let filter_children = $('.selected-flex-list').children();
                if (filter_children.length === 0) {
                    $('.selectder-filter-contents').fadeOut();
                }
            });

            $(document).on('click', '.click-hide-filter .click-hide-parent', function () {
                let filter_name = $(this).data('filter');
                if (filter_name === 'all') {
                    $('.active-list .active').removeClass('active');
                    $('.ui-range-value-min .min_price').text('0');
                    $('.ui-range-value-min input').val(0);
                    $('.ui-range-value-max .max_price').text('10000');
                    $('.ui-range-value-max input').val(10000);
                    $('.noUi-base .noUi-connect').css('left', '0%');
                    $('.noUi-base .noUi-background').css('left', '100%');
                    filter_product_request();
                    $('.selectder-filter-contents').fadeOut();
                    $(this).siblings('ul').html('');
                }
            });

            $(document).on('click', '.shop-nice-select ul.list li.option', function (e) {
                let sort = $(this).data('value');
                let currentPage = $(".pagination .page-item .page-link.active").attr("data-page");
                filter_product_request(currentPage, sort);
            });

            let i = document.querySelector(".ui-range-slider");
            if (void 0 !== i && null !== i) {
                let j = parseInt(i.parentNode.getAttribute("data-start-min"), 10),
                    k = parseInt(i.parentNode.getAttribute("data-start-max"), 10),
                    l = parseInt(i.parentNode.getAttribute("data-min"), 10),
                    m = parseInt(i.parentNode.getAttribute("data-max"), 10),
                    n = parseInt(i.parentNode.getAttribute("data-step"), 10),
                    o = document.querySelector(".ui-range-value-min span"),
                    p = document.querySelector(".ui-range-value-max span"),
                    q = document.querySelector(".ui-range-value-min input"),
                    r = document.querySelector(".ui-range-value-max input");

                noUiSlider.create(i, {
                    start: [j, k], connect: true, step: n, range: { min: l, max: m }, behaviour: 'tap'
                }), i.noUiSlider.on("change", function (a, b) {
                    let c = a[b];
                    b ? (p.innerHTML = Math.round(c), r.value = Math.round(c)) : (o.innerHTML = Math.round(c), q.value = Math.round(c));
                    let currentPage = $(".pagination .page-item .page-link.active").attr("data-page");
                    filter_product_request(currentPage);
                });
            }

            function filter_product_request(page = null, sort = null) {
                let url = '{{ theme_shop_url() }}';
                let category_slug = $('.category-lists li.active').data('slug');
                let size_slug = $('.size-lists .active').data('slug');
                let color_slug = $('.color-lists .active').data('slug');
                let rating = $('.filter-lists .active').data('slug');
                let min_price = $('.ui-range-value-min input').val();
                let max_price = $('.ui-range-value-max input').val();
                let tag_slug = $('.tag-lists .active').data('slug');
                let requestPage = (page !== null) ? page : null;
                let sortBy = (sort !== null) ? sort : null;

                $.ajax({
                    type: 'GET', url: url,
                    data: {
                        'category': category_slug, 'size': size_slug, 'color': color_slug,
                        'rating': rating, 'min_price': min_price, 'max_price': max_price,
                        'tag': tag_slug, 'page': requestPage, 'sort': sortBy
                    },
                    beforeSend: function () { $('.loader').show(); },
                    success: function (data) {
                        $(".grid-product-list").html(data.grid);
                        $(".list-product-list").html(data.list);
                        $(".shop-icons.active").trigger('click');
                        let paginationData = data.pagination;
                        $('.showing-results').text('{{ __('Showing') }} ' + paginationData.from + ' - ' + paginationData.total + ' of ' + paginationData.total + ' {{ __('Results') }}');
                        setInterval(() => { $('.loader').hide(); }, 700);
                    }
                });
            }

            $(document).on('keyup', 'input[name=search]', function (e) {
                let search = $(this).val();
                if (search === '') { setTimeout(() => { location.reload(); }, 500); }
                $.ajax({
                    type: 'GET', url: '{{ theme_search_ajax_url() }}',
                    data: { 'search': search },
                    beforeSend: function () { $('.loader').show(); },
                    success: function (data) {
                        $(".grid-product-list").html(data.grid);
                        $(".list-product-list").html(data.list);
                        $(".shop-icons.active").trigger('click');
                        let paginationData = data.pagination;
                        let fromItems = paginationData.from !== null ? paginationData.from : 0;
                        $('.showing-results').text('{{ __('Showing') }} ' + fromItems + ' - ' + paginationData.total + ' of ' + paginationData.total + ' {{ __('Results') }}');
                        setInterval(() => { $('.loader').hide(); }, 700);
                    }
                });
            });

            $(document).on('click', 'a.popup-modal', function (e) {
                let el = $(this).parent();
                let id = el.data('id');
                let modal = $('#product-modal');
                $.ajax({
                    type: 'GET', url: '{{ theme_quick_view_url() }}',
                    data: { 'id': id },
                    beforeSend: function () { $('.loader').show(); },
                    success: function (data) {
                        modal.html(data.product_modal);
                        setInterval(() => { $('.loader').hide(); }, 700);
                    }
                });
            });
        });
    </script>
@endsection
