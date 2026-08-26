@extends('tenant.frontend.frontend-page-master')

@section('title')
    {!! $page_post->title !!}
@endsection

@section('page-title')
    {!! $page_post->title !!}
@endsection


@section('content')
    <!-- Category Filter Book area starts -->
    @include(include_theme_path('digital-shop.partials.shop-topbar-content'))
    <!-- Category Filter Book area end -->

    <!-- Book Filter area start -->
    <div class="responsive-overlay"></div>
    <section class="shop-area section-bg-2 padding-top-100 padding-bottom-100">
        <div class="container custom-container-one">
            <div class="shop-contents-wrapper">
                <div class="shop-icon">
                    <div class="shop-icon-sidebar">
                        <i class="las la-bars"></i>
                    </div>
                </div>

                <!-- Book Filter area start -->
                @include(include_theme_path('digital-shop.partials.shop-sidebar-content'))
                <!-- Book Filter area start -->

                <!-- Book grid area start -->
                @include(include_theme_path('digital-shop.partials.shop-grid-products'))
                <!-- Book grid area end -->
            </div>
        </div>
    </section>
    <!-- Book Filter area end -->
@endsection

@section('scripts')
    <script>
        $(function () {
            $(document).on('click', 'ul.pagination-list li a', function (e) {
                e.preventDefault();

                filter_product_request($(this).data('page'));
            })

            $(document).on('click', '#author_filter, #language_filter, .category-grid-list .list, .price-search-btn', function (e) {
                let currentPage = $(".pagination .page-item .page-link.active").attr("data-page");
                filter_product_request(currentPage);
            })

        // .add-to-wishlist-btn handled globally in footer.blade.php

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

            /*========================================
                Range Slider
            ========================================*/
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
                    start: [j, k],
                    connect: !0,
                    step: n,
                    range: {
                        min: l,
                        max: m
                    },
                    behaviour: 'tap'
                }), i.noUiSlider.on("change", function (a, b) {
                    let c = a[b];

                    b ? (p.innerHTML = Math.round(c), r.value = Math.round(c)) : (o.innerHTML = Math.round(c), q.value = Math.round(c))

                    let currentPage = $(".pagination .page-item .page-link.active").attr("data-page");

                    filter_product_request(currentPage);
                })
            }

            function filter_product_request(page = null, sort = null) {
                let url = '{{theme_digital_shop_url()}}';
                let category_slug = $('.category-grid-list .active').data('slug')
                let author_slug = $('#author_filter .active').data('slug')
                let language_slug = $('#language_filter .active').data('slug')
                let rating = $('.filter-lists .active').data('slug');
                let min_price = $('.ui-range-value-min input').val();
                let max_price = $('.ui-range-value-max input').val();
                let tag_slug = $('.tag-lists .active').data('slug');
                let requestPage = null;
                if (page !== null) {
                    requestPage = page
                }

                let sortBy = null;
                if (sort !== null) {
                    sortBy = sort;
                }

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        'category': category_slug,
                        'author': author_slug,
                        'language': language_slug,
                        'rating': rating,
                        'min_price': min_price,
                        'max_price': max_price,
                        'tag': tag_slug,
                        'page': requestPage,
                        'sort': sortBy
                    },

                    beforeSend: function () {
                        $('.loader').show();
                    },
                    success: function (data) {
                        $(".grid-product-list").html(data.grid)

                        $(".shop-icons.active").trigger('click');

                        let paginationData = data.pagination;
                        let fromItems = paginationData.from;
                        let toItems = paginationData.to;
                        let totalItems = paginationData.total;

                        $('.showing-results').text('{{__('Showing')}} ' + fromItems + ' - ' + totalItems + ' of ' + totalItems + ' {{__('Results')}}');

                        setInterval(() => {
                            $('.loader').hide();
                        }, 700)
                    },
                    error: function (data) {

                    }
                });
            }

            $(document).on('keyup', 'input[name=search]', function (e) {
                let search = $(this).val();

                if (search === '') {
                    setTimeout(() => {
                        location.reload();
                    }, 500)
                }

                $.ajax({
                    type: 'GET',
                    url: '{{theme_search_ajax_url()}}',
                    data: {
                        'search': search,
                    },

                    beforeSend: function () {
                        $('.loader').show();
                    },
                    success: function (data) {
                        $(".grid-product-list").html(data.grid)
                        $(".list-product-list").html(data.list)

                        $(".shop-icons.active").trigger('click');

                        let paginationData = data.pagination;
                        let fromItems = paginationData.from !== null ? paginationData.from : 0;
                        let toItems = paginationData.to;
                        let totalItems = paginationData.total;

                        $('.showing-results').text('{{__('Showing')}} ' + fromItems + ' - ' + totalItems + ' of ' + totalItems + ' {{__('Results')}}');

                        setInterval(() => {
                            $('.loader').hide();
                        }, 700)
                    },
                    error: function (data) {

                    }
                });
            });
        });
    </script>
@endsection
