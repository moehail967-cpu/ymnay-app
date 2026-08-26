@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $campaign->title !!} @endsection
@section('page-title') {!! $campaign->title !!} @endsection

@section('content')

<div class="cs-page-banner">
    <div class="container">
        <h1 class="cs-page-banner-title">{!! $campaign->title !!}</h1>
        <div class="cs-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="cs-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <span class="cs-breadcrumb-current">{!! $campaign->title !!}</span>
        </div>
    </div>
</div>

<div class="cs-campaign-section">
    <div class="container">
        <div class="grid-product-list">
            <div class="row g-4">
                @foreach($products ?? [] as $product)
                    @php
                        if (!$product) continue;
                        $data          = get_product_dynamic_price($product);
                        $campaign_name = $data['campaign_name'];
                        $regular_price = $data['regular_price'];
                        $sale_price    = $data['sale_price'];
                        $discount      = $data['discount'];
                        $final_price   = calculatePrice($sale_price, $product);
                    @endphp
                    <div class="col-xl-3 col-md-4 col-sm-6">
                        <div class="cs-product-card">
                            <div class="cs-product-thumb">
                                <a href="{{ theme_product_url($product->slug) }}">
                                    {!! render_image_markup_by_attachment_id($product->image_id, 'lazyloads') !!}
                                </a>

                                @include(include_theme_path('shop.partials.product-options'))

                                @if(!empty($discount))
                                    <span class="cs-product-badge cs-product-badge-sale">{{ __('Sale') }}</span>
                                @endif

                                <div class="cs-campaign-timer flash-countdown-camp countdown-2 flash-countdown" data-date="{{ $campaign->end_date }}">
                                    <div class="cs-timer-box">
                                        <span class="counter-days item cs-timer-val"></span>
                                        <span class="cs-timer-label">{{ __('Day') }}</span>
                                    </div>
                                    <div class="cs-timer-box">
                                        <span class="counter-hours item cs-timer-val"></span>
                                        <span class="cs-timer-label">{{ __('Hr') }}</span>
                                    </div>
                                    <div class="cs-timer-box">
                                        <span class="counter-minutes item cs-timer-val"></span>
                                        <span class="cs-timer-label">{{ __('Min') }}</span>
                                    </div>
                                    <div class="cs-timer-box">
                                        <span class="counter-seconds item cs-timer-val"></span>
                                        <span class="cs-timer-label">{{ __('Sec') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="cs-product-body">
                                <h3 class="cs-product-title ff-jost">
                                    <a href="{{ theme_product_url($product->slug) }}">{{ product_limited_text($product->name, 'title') }}</a>
                                </h3>
                                <div class="cs-product-price-row">
                                    <span class="cs-product-price flash-prices">{{ amount_with_currency_symbol($final_price) }}</span>
                                    @if($regular_price != $final_price)
                                        <span class="cs-product-old-price flash-old-prices">{{ amount_with_currency_symbol($regular_price) }}</span>
                                    @endif
                                    <div class="cs-product-atc">
                                        @if($product->inventory_detail_count < 1)
                                            <a href="javascript:void(0)" class="cs-product-atc-btn cart-loading add-to-cart-btn" data-product_id="{{ $product->id }}">
                                                <i class="las la-shopping-bag"></i>
                                            </a>
                                        @else
                                            <a href="javascript:void(0)" class="cs-product-atc-btn cart-loading product-quick-view-ajax"
                                               data-action-route="{{ theme_quick_view_url($product->slug) }}">
                                                <i class="las la-shopping-bag"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

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

    $(document).on('click', '.click-hide-filter .click-hide', function () {
        let filter_name = '.' + $(this).parent().data('filter') + ' .active';
        $(filter_name).removeClass('active');
        filter_product_request();
        $(this).parent().remove();
        if ($('.selected-flex-list').children().length === 0) {
            $('.selectder-filter-contents').fadeOut();
        }
    });

    $(document).on('click', '.click-hide-filter .click-hide-parent', function () {
        if ($(this).data('filter') === 'all') {
            $('.active-list .active').removeClass('active');
            $('.ui-range-value-min .min_price').text('0');
            $('.ui-range-value-min input').val(0);
            $('.ui-range-value-max .max_price').text('10000');
            $('.ui-range-value-max input').val(10000);
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
        noUiSlider.create(i, { start: [j, k], connect: true, step: n, range: { min: l, max: m }, behaviour: 'tap' });
        i.noUiSlider.on("change", function (a, b) {
            let c = a[b];
            b ? (p.innerHTML = Math.round(c), r.value = Math.round(c)) : (o.innerHTML = Math.round(c), q.value = Math.round(c));
            filter_product_request($(".pagination .page-item .page-link.active").attr("data-page"));
        });
    }

    function filter_product_request(page = null, sort = null) {
        $.ajax({
            type: 'GET',
            url: '{{ theme_shop_url() }}',
            data: {
                category: $('.category-lists > .active').data('slug'),
                size: $('.size-lists .active').data('slug'),
                color: $('.color-lists .active').data('slug'),
                rating: $('.filter-lists .active').data('slug'),
                min_price: $('.ui-range-value-min input').val(),
                max_price: $('.ui-range-value-max input').val(),
                tag: $('.tag-lists .active').data('slug'),
                page: page,
                sort: sort
            },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                $(".grid-product-list").html(data.grid);
                $(".list-product-list").html(data.list);
                $(".shop-icons.active").trigger('click');
                let p = data.pagination;
                $('.showing-results').text('{{ __("Showing") }} ' + p.from + ' - ' + p.total + ' of ' + p.total + ' {{ __("Results") }}');
                setInterval(() => { $('.loader').hide(); }, 700);
            }
        });
    }

    $(document).on('keyup', 'input[name=search]', function () {
        let search = $(this).val();
        if (search === '') { setTimeout(() => { location.reload(); }, 500); return; }
        $.ajax({
            type: 'GET',
            url: '{{ theme_search_ajax_url() }}',
            data: { search: search },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                $(".grid-product-list").html(data.grid);
                $(".list-product-list").html(data.list);
                $(".shop-icons.active").trigger('click');
                let p = data.pagination;
                $('.showing-results').text('{{ __("Showing") }} ' + (p.from ?? 0) + ' - ' + p.total + ' of ' + p.total + ' {{ __("Results") }}');
                setInterval(() => { $('.loader').hide(); }, 700);
            }
        });
    });
});
</script>
@endsection
