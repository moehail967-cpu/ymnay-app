@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $category->name !!} @endsection
@section('page-title') {!! $category->name !!} @endsection

@section('content')

<div class="fn-page-banner">
    <div class="container">
        <h1>{!! $category->name !!}</h1>
        <div class="fn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="las la-angle-right"></i></span>
            <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
            <span><i class="las la-angle-right"></i></span>
            <span class="current">{!! $category->name !!}</span>
        </div>
    </div>
</div>

<div class="container fn-shop-wrap">
    <div class="row g-4">

        {{-- ===== Sidebar ===== --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div class="fn-sidebar-card">
                <div class="fn-sidebar-title">{{ __('Search') }}</div>
                <form class="fn-shop-search" onsubmit="fnFilterRequest();return false;">
                    <input type="text" id="fn-search-input" placeholder="{{ __('Search products…') }}">
                    <button type="submit"><i class="las la-search"></i></button>
                </form>
            </div>

            {{-- Categories --}}
            @if(isset($categories) && $categories->isNotEmpty())
            <div class="fn-sidebar-card">
                <div class="fn-sidebar-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach($categories as $cat)
                    <label class="fn-filter-item">
                        <label class="d-flex align-items-center gap-2 mb-0 fn-filter-cursor">
                            <input type="checkbox" class="fn-cat-checkbox"
                                   data-slug="{{ $cat->slug }}"
                                   data-value="{{ $cat->name }}"
                                   {{ $cat->slug === $category->slug ? 'checked' : '' }}>
                            {{ $cat->name }}
                        </label>
                        <span class="fn-filter-count">{{ $cat->product_count ?? '' }}</span>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Price Range --}}
            <div class="fn-sidebar-card">
                <div class="fn-sidebar-title">{{ __('Price Range') }}</div>
                <div class="fn-price-inputs">
                    <input type="number" class="fn-price-input" id="fn-min-price"
                           placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" class="fn-price-input" id="fn-max-price"
                           placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button class="fn-btn fn-btn-gold fn-btn-sm mt-3 w-100" onclick="fnFilterRequest()">
                    {{ __('Apply Filter') }}
                </button>
            </div>

            {{-- Rating --}}
            <div class="fn-sidebar-card">
                <div class="fn-sidebar-title">{{ __('Rating') }}</div>
                <label class="fn-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0 fn-filter-cursor">
                        <input type="radio" name="fn_rating" class="fn-rating-filter" value="5">
                        <span class="fn-star-filter">★★★★★</span>
                    </label>
                </label>
                <label class="fn-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0 fn-filter-cursor">
                        <input type="radio" name="fn_rating" class="fn-rating-filter" value="4">
                        <span><span class="fn-star-active">★★★★</span><span class="fn-star-empty">★</span></span>
                    </label>
                </label>
                <label class="fn-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0 fn-filter-cursor">
                        <input type="radio" name="fn_rating" class="fn-rating-filter" value="3">
                        <span><span class="fn-star-active">★★★</span><span class="fn-star-empty">★★</span></span>
                    </label>
                </label>
                <label class="fn-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0 fn-filter-cursor">
                        <input type="radio" name="fn_rating" class="fn-rating-filter" value="2">
                        <span><span class="fn-star-active">★★</span><span class="fn-star-empty">★★★</span></span>
                    </label>
                </label>
                <a href="javascript:void(0)" class="fn-clear-link" id="fn-clear-filters">
                    <i class="las la-times-circle"></i> {{ __('Clear All Filters') }}
                </a>
            </div>

        </div>{{-- /sidebar --}}

        {{-- ===== Products ===== --}}
        <div class="col-lg-9">

            {{-- Topbar --}}
            <div class="fn-shop-topbar">
                <span class="fn-topbar-count showing-results">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <select class="fn-sort-select" id="fn-sort-select">
                    <option value="3">{{ __('Sort: Latest') }}</option>
                    <option value="1">{{ __('Sort: Name') }}</option>
                    <option value="2">{{ __('Sort: Popular') }}</option>
                    <option value="4">{{ __('Price: Low to High') }}</option>
                    <option value="5">{{ __('Price: High to Low') }}</option>
                </select>
            </div>

            {{-- Product Grid --}}
            <div class="fn-sp-product-grid grid-product-list">
                @include(include_theme_path('shop.partials.product-partials.grid-products'))
            </div>

        </div>{{-- /products --}}
    </div>
</div>

<div id="product-modal"></div>
@endsection

@section('scripts')
<script>
$(function () {

    function fnFilterRequest(page) {
        page = page || null;

        var cats = [];
        $('.fn-cat-checkbox:checked').each(function () { cats.push($(this).data('slug')); });
        var category_slug = cats.join(',') || null;

        var rating    = $('input[name=fn_rating]:checked').val() || null;
        var min_price = $('#fn-min-price').val() || 0;
        var max_price = $('#fn-max-price').val() || 10000;
        var sort      = $('#fn-sort-select').val();
        var search    = $('#fn-search-input').val() || null;

        $.ajax({
            type: 'GET',
            url: '{{ theme_shop_filter_url() }}',
            data: {
                category:  category_slug,
                rating:    rating,
                min_price: min_price,
                max_price: max_price,
                sort:      sort,
                page:      page,
                search:    search
            },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                $('.grid-product-list').html(data.grid);

                var p = data.pagination;
                $('.showing-results').html(
                    '{{ __('Showing') }} <strong>' + (p.to ? p.from + '–' + p.to : 0) +
                    '</strong> {{ __('of') }} <strong>' + (p.total || 0) + '</strong> {{ __('results') }}'
                );

                setTimeout(function () { $('.loader').hide(); }, 400);
            },
            error: function () { $('.loader').hide(); }
        });
    }

    window.fnFilterRequest = fnFilterRequest;

    $(document).on('change', '.fn-cat-checkbox', function () { fnFilterRequest(); });
    $('#fn-sort-select').on('change', function () { fnFilterRequest(); });
    $(document).on('change', '.fn-rating-filter', function () { fnFilterRequest(); });
    $(document).on('keydown', '#fn-search-input', function (e) {
        if (e.key === 'Enter') { fnFilterRequest(); }
    });

    $(document).on('click', '#fn-clear-filters', function () {
        $('.fn-cat-checkbox').prop('checked', false);
        $('input[name=fn_rating]').prop('checked', false);
        $('#fn-min-price').val(0);
        $('#fn-max-price').val(10000);
        $('#fn-search-input').val('');
        fnFilterRequest();
    });

});
</script>
@endsection
