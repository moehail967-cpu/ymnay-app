@extends(route_prefix().'frontend.frontend-page-master')

@section('title')
    {!!  $product->name !!}
@endsection

@section('page-title')
    {!! $product->name !!}
@endsection

@section('meta-data')
    {!! render_page_meta_data($product) !!}
@endsection

@section('style')
    <link rel="stylesheet" href="{{ global_asset('assets/common/css/star-rating.min.css') }}">
    <style>
        :root {
            --gl-star-size: 35px;
            --gl-tooltip-border-radius: 4px;
            --gl-tooltip-font-size: 0.875rem;
            --gl-tooltip-font-weight: 400;
            --gl-tooltip-line-height: 1;
            --gl-tooltip-margin: 12px;
            --gl-tooltip-padding: .3em 1em;
            --gl-tooltip-size: 6px;
        }
        .gl-star-rating--stars span { margin-right: 5px !important; }
        .campaign_countdown_wrapper { text-align: center; z-index: 95; }
        .campaign_countdown_wrapper .global-timer .syotimer__body { gap: 10px 15px; justify-content: space-between; }
        .campaign_countdown_wrapper .global-timer .syotimer__body .syotimer-cell { background-color: rgba(232,96,60,.1); padding: 10px 20px; min-width: 100px; }
        .campaign_countdown_wrapper .global-timer .syotimer__body .syotimer-cell .syotimer-cell__value { font-size: 32px; line-height: 36px; }
        .campaign_countdown_wrapper .global-timer .syotimer__body .syotimer-cell .syotimer-cell__unit { font-size: 18px; line-height: 28px; }
        @media (max-width:1399.98px) {
            .campaign_countdown_wrapper .global-timer .syotimer__body .syotimer-cell .syotimer-cell__value { font-size: 28px; }
            .campaign_countdown_wrapper .global-timer .syotimer__body .syotimer-cell .syotimer-cell__unit { font-size: 16px; }
        }
    </style>
    <x-summernote.custom-css />
@endsection

@section('content')
    @php
        $data = get_product_dynamic_price($product);
        $campaign_name = $data['campaign_name'];
        $data_regular_price = $data['regular_price'];
        $data_sale_price = $data['sale_price'];
        $discount = $data['discount'];
        $is_running = $data['is_running'];

        $sale_price = $data['sale_price'];
        $deleted_price = $data['regular_price'];

        $campaign_product = null;
        if ($is_running) {
            $campaign_product = $product?->campaign_product;
            $sale_price = $data_sale_price;
            $deleted_price = $data_regular_price;
            $campaign_percentage = $discount;
        }

        $stock_count = $campaign_product
            ? ($campaign_product->units_for_sale !== null
                ? max(0, $campaign_product->units_for_sale - (int) $campaign_product->sold_count)
                : null)
            : optional($product->inventory)->stock_count;
        $stock_count = $stock_count > 0 ? $stock_count : 0;

        if ($campaign_product) {
            $campaign_title = \Modules\Campaign\Entities\Campaign::select('id','title')->where("id",$campaign_product?->id)->first();
            $campaign_active = !empty($campaign_product) && empty($data['campaign_name']) ? 1 : 0;
        }

        $final_price = calculatePrice($sale_price, $product);
        $quickView = false;
    @endphp

    <!-- Product Details -->
    <section class="el-page-section">
        <div class="container">
            <div class="el-product-detail-wrap">
                @include(include_theme_path('shop.product_details.partials.product-images-slider'))
                <div class="el-pd-options-col">
                    @include(include_theme_path('shop.product_details.partials.product-options'))
                </div>
            </div>
        </div>
    </section>

    <!-- Product Detail Tabs -->
    <section class="el-pd-tabs-section">
        <div class="container">
            <div class="el-pd-tabs">
                <div class="el-pd-tab-bar">
                    <button class="el-pd-tab-btn active" data-tab="description">{{ __('Description') }}</button>
                    <button class="el-pd-tab-btn" data-tab="reviews">{{ __('Reviews') }}</button>
                    <button class="el-pd-tab-btn" data-tab="ship_return">{{ __('Ship & Return') }}</button>
                </div>

                @include(include_theme_path('shop.product_details.partials.product-description'))
                @include(include_theme_path('shop.product_details.partials.product-reviews'))
                @include(include_theme_path('shop.product_details.partials.product-ship_return'))
            </div>
        </div>
    </section>

    <!-- Related Products -->
    @include(include_theme_path('shop.product_details.partials.featured-product'))
@endsection

@section('scripts')
    <script>
        $(function () {
            $(document).ready(function () {
                setTimeout(() => {
                    $('.shop-details-bottom-slider-area').removeAttr("style");
                }, 1000);
            });

            /* ── Star Rating ── */
            let starRatingControl = new StarRating('.star-rating', {
                maxStars: 5,
                clearable: false,
                stars: function (el, item, index) {
                    el.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect class="gl-star-full" width="19" height="19" x="2.5" y="2.5"/><polygon fill="#FFF" points="12 5.375 13.646 10.417 19 10.417 14.665 13.556 16.313 18.625 11.995 15.476 7.688 18.583 9.333 13.542 5 10.417 10.354 10.417"/></svg>';
                },
                classNames: { active: 'gl-active', base: 'gl-star-rating', selected: 'rating-selected' },
            });

            /* ── Tabs ── */
            $(document).on('click', '.el-pd-tab-btn', function () {
                let tab = $(this).data('tab');
                $('.el-pd-tab-btn').removeClass('active');
                $(this).addClass('active');
                $('.el-pd-tab-content').removeClass('active');
                $('#' + tab).addClass('active');
            });

            /* ── Countdown Timer ── */
            @php
                if (!empty($campaign_product) && $campaign_active != 0) {
                    $end_date = $campaign_product->campaign?->end_date;
                }
            @endphp

            let year  = '{{ $end_date->year ?? 0 }}';
            let month = '{{ $end_date->month ?? 0 }}';
            let day   = '{{ $end_date->day ?? 0 }}';

            $('.global-timer').syotimer({ year: year, month: month, day: day });

            /* ── Thumbnail Click ── */
            $(document).on('click', '.el-pd-thumb', function () {
                let image = $(this).data('image-path');
                let mainImg = $('.el-pd-main-img img');
                mainImg.hide();
                mainImg.attr('src', image);
                mainImg.fadeIn(100);
                $('.el-pd-thumb').removeClass('active');
                $(this).addClass('active');
            });

            /* Legacy small-img handler */
            $(document).on('click', '.small-img', function () {
                let image = $(this).data('image-path');
                let long_img = $('.long-img img');
                long_img.hide();
                long_img.attr('src', image);
                long_img.fadeIn(100);
            });

            /* ── Review Submit ── */
            $(document).on('click', '#review-submit-btn', function (e) {
                e.preventDefault();
                let product_id = '{{ $product->id }}';
                let selected_rating = $('.rating-selected').data('value');
                let review_text = $('#review-text').val();
                let submit_btn_el = $(this);

                $.ajax({
                    url: '{{ theme_product_review_url() }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: product_id,
                        review_text: review_text,
                        rating: selected_rating
                    },
                    beforeSend: function () {
                        CustomSweetalertTwo.warning('{{ __('Submitting please wait.') }}');
                        submit_btn_el.text('{{ __('Submitting..') }}');
                    },
                    success: function (data) {
                        if (data.type === 'success') {
                            CustomSweetalertTwo.success(data.msg);
                            setTimeout(() => { location.reload(); }, 300);
                        } else {
                            CustomSweetalertTwo.error(data.msg);
                            submit_btn_el.closest('form')[0].reset();
                        }
                        submit_btn_el.text('{{ __('Submit Review') }}');
                    },
                    error: function (data) {
                        var response = data.responseJSON.errors;
                        $.each(response, function (value, index) { CustomSweetalertTwo.error(index); });
                        submit_btn_el.text('{{ __('Submit Review') }}');
                    }
                });
            });

            /* ── See More Reviews ── */
            $(document).on('click', '.see-more-review', function () {
                let el = $(this);
                let items = el.attr('data-items');
                $.ajax({
                    url: '{{ theme_product_review_more_url() }}',
                    type: 'GET',
                    data: { product_id: '{{ $product->id }}', items: items },
                    beforeSend: function () { el.text('{{ __('Loading..') }}'); },
                    success: function (data) {
                        $('.all-reviews').html(data.markup).hide();
                        $('.all-reviews').fadeIn(800);
                        el.text('{{ __('See More') }}');
                        el.attr('data-items', Number(items) + 5);
                    },
                    error: function () { el.text('{{ __('See More') }}'); }
                });
            });

            /* ── Quantity ── */
            $(document).on('click', '.plus', function () {
                var selectedInput = $(this).prev('.quantity-input');
                if (selectedInput.val()) { selectedInput[0].stepUp(1); }
            });

            $(document).on('click', '.substract', function () {
                var selectedInput = $(this).next('.quantity-input');
                if (selectedInput.val() > 1) { selectedInput[0].stepDown(1); }
            });
        });
    </script>

    {!! theme_product_js($product_inventory_set, $additional_info_store, $campaign_product ?? null, $campaign_active ?? 0, $product) !!}
@endsection
