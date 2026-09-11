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
@endsection

@section('content')
    <!-- Digital Product Details -->
    <section class="hf-page-section">
        <div class="container">
            <div class="hf-digital-pd-wrap">
                @include(include_theme_path('digital-shop.product_details.partials.details'))
                @include(include_theme_path('digital-shop.product_details.partials.details-sidebar'))
            </div>
        </div>
    </section>

    <!-- Description & Reviews -->
    @include(include_theme_path('digital-shop.product_details.partials.description-review'))
@endsection

@section('scripts')
    <script>
        $(function () {
            let starRatingControl = new StarRating('.star-rating', {
                maxStars: 5,
                clearable: false,
                stars: function (el, item, index) {
                    el.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect class="gl-star-full" width="19" height="19" x="2.5" y="2.5"/><polygon fill="#FFF" points="12 5.375 13.646 10.417 19 10.417 14.665 13.556 16.313 18.625 11.995 15.476 7.688 18.583 9.333 13.542 5 10.417 10.354 10.417"/></svg>';
                },
                classNames: { active: 'gl-active', base: 'gl-star-rating', selected: 'rating-selected' },
            });

            /* ── Tabs ── */
            $(document).on('click', '.hf-pd-tab-btn', function () {
                let tab = $(this).data('tab');
                $('.hf-pd-tab-btn').removeClass('active');
                $(this).addClass('active');
                $('.hf-pd-tab-content').removeClass('active');
                $('#' + tab).addClass('active');
            });

            /* ── Countdown Timer ─��� */
            @php
                if (!empty($campaign_product) && $campaign_active != 0) {
                    $end_date = $campaign_product->campaign?->end_date;
                }
            @endphp

            let year  = '{{ $end_date->year ?? 0 }}';
            let month = '{{ $end_date->month ?? 0 }}';
            let day   = '{{ $end_date->day ?? 0 }}';

            $('.global-timer').syotimer({ year: year, month: month, day: day });

            /* ── Review Submit ── */
            $(document).on('click', '#review-submit-btn', function (e) {
                e.preventDefault();
                let product_id = '{{ $product->id }}';
                let selected_rating = $('.rating-selected').data('value');
                let review_text = $('#review-text').val();
                let submit_btn_el = $(this);

                $.ajax({
                    url: '{{ theme_digital_product_review_url() }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: product_id,
                        review_text: review_text,
                        rating: selected_rating
                    },
                    beforeSend: function () {
                        toastr.warning('{{ __('Submitting please wait.') }}', 5000);
                        submit_btn_el.text('{{ __('Submitting..') }}');
                    },
                    success: function (data) {
                        if (data.type === 'success') {
                            toastr.success(data.msg, 5000);
                            setTimeout(() => { location.reload(); }, 300);
                        } else {
                            toastr.error(data.msg, 5000);
                            submit_btn_el.closest('form')[0].reset();
                        }
                        submit_btn_el.text('{{ __('Submit Review') }}');
                    },
                    error: function (data) {
                        var response = data.responseJSON.errors;
                        $.each(response, function (value, index) { toastr.error(index, 5000); });
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

    <script>
        (function ($) {
            'use strict';

            $(document).ready(function () {
                $(document).on('click', '#login_submit_btn', function (e) {
                    e.preventDefault();
                    let el = $(this);
                    let username = $('#login_form_order_page input[name=email]').val();
                    let password = $('#login_form_order_page input[name=password]').val();
                    let remember = $('#login_form_order_page input[name=remember]').val();

                    el.text('{{ __("Please Wait") }}');

                    $.ajax({
                        type: 'post',
                        url: "{{ theme_ajax_login_url() }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            username: username,
                            password: password,
                            remember: remember,
                        },
                        success: function (data) {
                            if (data.status === 'invalid') {
                                el.text('{{ __("Login") }}');
                                toastr.warning(data.msg);
                            } else {
                                el.text('{{ __("Login Success.. Redirecting ..") }}');
                                toastr.success(data.msg);
                                setTimeout(() => { location.reload(); }, 300);
                            }
                        },
                        error: function (data) {
                            var response = data.responseJSON.errors;
                            $.each(response, function (value, index) { toastr.error(index); });
                            el.text('{{ __("Login") }}');
                        }
                    });
                });
            });
        })(jQuery);

        $(document).on('click', '.add_to_cart_single_page', function (e) {
            e.preventDefault();
            let product_id = '{{ $product->id }}';
            $.ajax({
                url: '{{ theme_digital_product_add_to_cart_url() }}',
                type: 'POST',
                data: { product_id: product_id, _token: '{{ csrf_token() }}' },
                success: function (data) {
                    if (data.quantity_msg) {
                        toastr.warning(data.quantity_msg);
                    } else if (data.error_msg) {
                        toastr.error(data.error_msg);
                    } else {
                        toastr.success(data.msg, '{{ __('Go to Cart') }}', '#', 60000);
                        $('.track-icon-list').hide();
                        $('.track-icon-list').load(location.href + " .track-icon-list");
                        $('.track-icon-list').fadeIn();
                    }
                },
                error: function () { toastr.error('{{ __("An error occurred") }}'); }
            });
        });

        $(document).on('click', '.add_to_wishlist_single_page', function (e) {
            e.preventDefault();
            let has_campaign = '{{ empty($campaign_product) ? 0 : 1 }}';
            let campaign_expired = '{{ isset($campaign_active) ? $campaign_active : 0 }}';
            if (has_campaign == 1 && campaign_expired == 0) {
                toastr.error('{{ __('This campaign has ended. You cannot add this product to your cart.') }}');
                return false;
            }
            let product_id = '{{ $product->id }}';
            $.ajax({
                url: '{{ theme_add_to_wishlist_url() }}',
                type: 'POST',
                data: { product_id: product_id, _token: '{{ csrf_token() }}' },
                success: function (data) {
                    if (data.quantity_msg) { toastr.warning(data.quantity_msg); }
                    else if (data.error_msg) { toastr.error(data.error_msg); }
                    else { toastr.success(data.msg); $('.track-icon-list').load(location.href + " .track-icon-list"); }
                },
                error: function () { toastr.error('{{ __("An error occurred") }}'); }
            });
        });

        $(document).on('click', '.compare-btn', function (e) {
            e.preventDefault();
            let has_campaign = '{{ empty($campaign_product) ? 0 : 1 }}';
            let campaign_expired = '{{ isset($campaign_active) ? $campaign_active : 0 }}';
            if (has_campaign == 1 && campaign_expired == 0) {
                toastr.error('{{ __('This campaign has ended. You cannot add this product to your cart.') }}');
                return false;
            }
            let product_id = '{{ $product->id }}';
            $.ajax({
                url: '{{ theme_add_to_compare_url() }}',
                type: 'POST',
                data: { product_id: product_id, _token: '{{ csrf_token() }}' },
                success: function (data) {
                    if (data.quantity_msg) { toastr.warning(data.quantity_msg); }
                    else if (data.error_msg) { toastr.error(data.error_msg); }
                    else { toastr.success(data.msg); $('.track-icon-list').load(location.href + " .track-icon-list"); }
                },
                error: function () { toastr.error('{{ __("An error occurred") }}'); }
            });
        });

        $(document).on('click', '.but_now_single_page', function (e) {
            e.preventDefault();
            let has_campaign = '{{ empty($campaign_product) ? 0 : 1 }}';
            let campaign_expired = '{{ isset($campaign_active) ? $campaign_active : 0 }}';
            if (has_campaign == 1 && campaign_expired == 0) {
                toastr.error('{{ __('This campaign has ended. You cannot add this product to your cart.') }}');
                return false;
            }
            let product_id = '{{ $product->id }}';
            $.ajax({
                url: '{{ theme_buy_now_url() }}',
                type: 'POST',
                data: { product_id: product_id, _token: '{{ csrf_token() }}' },
                success: function (data) {
                    if (data.quantity_msg) { toastr.warning(data.quantity_msg, 5000); }
                    else if (data.error_msg) { toastr.error(data.error_msg, 5000); }
                    if (data.type === 'success') {
                        toastr.success(data.msg);
                        setTimeout(() => { location.href = data.redirect; }, 2000);
                    }
                },
                error: function () { toastr.error('{{ __("An error occurred") }}', 5000); }
            });
        });
    </script>
@endsection
