@extends('tenant.frontend.frontend-page-master')

@section('title')
    {!! $campaign->title !!}
@endsection

@section('page-title')
    {!! $campaign->title !!}
@endsection

@section('content')
    <!-- Campaign Page (Digital Shop) -->
    <section class="el-page-section">
        <div class="container">

            <!-- Campaign Hero -->
            <div class="el-campaign-hero">
                <div class="el-campaign-meta">
                    <h1 class="el-campaign-title">{!! $campaign->title !!}</h1>
                    @if(!empty($campaign->description))
                        <p class="el-campaign-desc">{!! $campaign->description !!}</p>
                    @endif
                </div>
                <div class="el-countdown flash-countdown-camp flash-countdown" data-date="{{ $campaign->end_date }}">
                    <div class="el-countdown-item">
                        <span class="el-countdown-value counter-days item"></span>
                        <span class="el-countdown-label item">{{ __('Days') }}</span>
                    </div>
                    <div class="el-countdown-sep">:</div>
                    <div class="el-countdown-item">
                        <span class="el-countdown-value counter-hours item"></span>
                        <span class="el-countdown-label item">{{ __('Hours') }}</span>
                    </div>
                    <div class="el-countdown-sep">:</div>
                    <div class="el-countdown-item">
                        <span class="el-countdown-value counter-minutes item"></span>
                        <span class="el-countdown-label item">{{ __('Mins') }}</span>
                    </div>
                    <div class="el-countdown-sep">:</div>
                    <div class="el-countdown-item">
                        <span class="el-countdown-value counter-seconds item"></span>
                        <span class="el-countdown-label item">{{ __('Secs') }}</span>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="grid-product-list">
                <div class="el-shop-grid mt-5">
                    @foreach($products as $product)
                        @php
                            if (!$product) continue;
                            $data = get_product_dynamic_price($product);
                            $campaign_name = $data['campaign_name'];
                            $regular_price = $data['regular_price'];
                            $sale_price    = $data['sale_price'];
                            $discount      = $data['discount'];

                            $img_data = get_attachment_image_by_id($product->image_id, 'grid');
                            $img_url  = !empty($img_data) ? $img_data['img_url'] : '';
                            $img_alt  = !empty($img_data) ? $img_data['img_alt'] : $product->name;
                        @endphp

                        <div class="el-card">
                            <div class="el-card-img-wrap">
                                <a href="{{ dynamicRoute($product->slug) }}">
                                    <img class="el-card-img" src="{{ $img_url }}" alt="{{ $img_alt }}">
                                </a>

                                @if(!empty($discount))
                                    <span class="el-card-badge">{{ $discount }}% {{ __('off') }}</span>
                                @endif

                                @if(!empty($product->badge))
                                    <span class="el-card-badge el-card-badge-new">{{ $product->badge->name }}</span>
                                @endif

                                @include(include_theme_path('shop.partials.product-options'))
                            </div>

                            <div class="el-card-body">
                                <a href="{{ dynamicRoute($product->slug) }}" class="el-card-name">
                                    {{ Str::words($product->name, 4) }}
                                </a>

                                <!-- Countdown per card -->
                                <div class="el-card-countdown flash-countdown-camp flash-countdown" data-date="{{ $campaign->end_date }}" style="display:flex;gap:6px;font-size:11px;color:#E8603C;font-weight:700;margin:4px 0;">
                                    <span class="counter-days item"></span><span>d</span>
                                    <span class="counter-hours item"></span><span>h</span>
                                    <span class="counter-minutes item"></span><span>m</span>
                                    <span class="counter-seconds item"></span><span>s</span>
                                </div>

                                {!! render_product_star_rating_markup_with_count($product) !!}

                                <div class="el-card-footer">
                                    <div class="el-card-prices">
                                        <span class="el-card-price flash-prices">{{ amount_with_currency_symbol($sale_price) }}</span>
                                        @if(!empty($regular_price))
                                            <span class="el-card-old flash-old-prices">{{ amount_with_currency_symbol($regular_price) }}</span>
                                        @endif
                                    </div>
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

            function filter_product_request(page = null, sort = null) {
                let url = '{{ theme_shop_url() }}';
                let category_slug = $('.category-lists li.active').data('slug');
                let size_slug = $('.size-lists .active').data('slug');
                let color_slug = $('.color-lists .active').data('slug');
                let rating = $('.filter-lists .active').data('slug');
                let min_price = $('.ui-range-value-min input').val();
                let max_price = $('.ui-range-value-max input').val();
                let tag_slug = $('.tag-lists .active').data('slug');

                $.ajax({
                    type: 'GET', url: url,
                    data: {
                        'category': category_slug, 'size': size_slug, 'color': color_slug,
                        'rating': rating, 'min_price': min_price, 'max_price': max_price,
                        'tag': tag_slug, 'page': page, 'sort': sort
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
