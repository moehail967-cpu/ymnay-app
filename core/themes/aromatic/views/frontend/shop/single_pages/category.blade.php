@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $category->name !!} @endsection
@section('page-title') {!! $category->name !!} @endsection

@section('content')
{{-- Page Banner --}}
<div class="ar-page-banner">
    <div class="container">
        <h1>{!! $category->name !!}</h1>
        <div class="ar-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
            <span>/</span>
            <span>{!! $category->name !!}</span>
        </div>
    </div>
</div>

{{-- Shop Layout --}}
<div class="container ar-shop-wrap">
    <div class="row g-4">

        {{-- ===== Sidebar ===== --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div class="ar-sidebar-card">
                <div class="ar-sidebar-title">{{ __('Search') }}</div>
                <form class="ar-search" onsubmit="arFilterRequest();return false;">
                    <input type="text" id="ar-search-input" placeholder="{{ __('Search products…') }}">
                    <button type="submit"><i class="mdi mdi-magnify"></i></button>
                </form>
            </div>

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div class="ar-sidebar-card">
                <div class="ar-sidebar-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach($categories as $cat)
                    <label class="ar-filter-item">
                        <label class="d-flex align-items-center gap-2 mb-0 ar-filter-cursor">
                            <input type="checkbox" class="ar-cat-checkbox"
                                   data-slug="{{ $cat->slug }}"
                                   data-value="{{ $cat->name }}"
                                   {{ ($cat->slug == $category->slug) ? 'checked' : '' }}>
                            {{ $cat->name }}
                        </label>
                        <span class="ar-filter-count">{{ $cat->product_count ?? '' }}</span>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Price Range --}}
            <div class="ar-sidebar-card">
                <div class="ar-sidebar-title">{{ __('Price Range') }}</div>
                <div class="ar-price-inputs">
                    <input type="number" class="ar-price-input" id="ar-min-price" placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" class="ar-price-input" id="ar-max-price" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button class="ar-btn ar-btn-red ar-btn-sm mt-3 w-100" onclick="arFilterRequest()">
                    {{ __('Apply Filter') }}
                </button>
            </div>

            {{-- Rating --}}
            <div class="ar-sidebar-card">
                <div class="ar-sidebar-title">{{ __('Rating') }}</div>
                <label class="ar-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0 ar-filter-cursor">
                        <input type="radio" name="ar_rating" class="ar-rating-filter" value="5">
                        <span class="ar-star-active">★★★★★</span>
                    </label>
                </label>
                <label class="ar-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0 ar-filter-cursor">
                        <input type="radio" name="ar_rating" class="ar-rating-filter" value="4">
                        <span><span class="ar-star-active">★★★★</span><span class="ar-star-empty">★</span></span>
                    </label>
                </label>
                <label class="ar-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0 ar-filter-cursor">
                        <input type="radio" name="ar_rating" class="ar-rating-filter" value="3">
                        <span><span class="ar-star-active">★★★</span><span class="ar-star-empty">★★</span></span>
                    </label>
                </label>
                <label class="ar-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0 ar-filter-cursor">
                        <input type="radio" name="ar_rating" class="ar-rating-filter" value="2">
                        <span><span class="ar-star-active">★★</span><span class="ar-star-empty">★★★</span></span>
                    </label>
                </label>
                <a href="javascript:void(0)" class="ar-clear-link" id="ar-clear-filters">
                    <i class="mdi mdi-close-circle-outline"></i> {{ __('Clear All Filters') }}
                </a>
            </div>

        </div>{{-- /sidebar --}}

        {{-- ===== Products ===== --}}
        <div class="col-lg-9">

            {{-- Topbar --}}
            <div class="ar-sort-bar">
                <span class="ar-topbar-count showing-results">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <select class="ar-sort-select" id="ar-sort-select">
                    <option value="3">{{ __('Sort: Latest') }}</option>
                    <option value="1">{{ __('Sort: Name') }}</option>
                    <option value="2">{{ __('Sort: Popular') }}</option>
                    <option value="4">{{ __('Price: Low to High') }}</option>
                    <option value="5">{{ __('Price: High to Low') }}</option>
                </select>
            </div>

            {{-- Product Grid --}}
            <div class="row g-3 grid-product-list">
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

    function arFilterRequest(page) {
        page = page || null;

        var cats = [];
        $('.ar-cat-checkbox:checked').each(function () { cats.push($(this).data('slug')); });
        var category_slug = cats.join(',') || null;

        var rating    = $('input[name=ar_rating]:checked').val() || null;
        var min_price = $('#ar-min-price').val() || 0;
        var max_price = $('#ar-max-price').val() || 10000;
        var sort      = $('#ar-sort-select').val();
        var search    = $('#ar-search-input').val() || null;

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
                    '{{ __('Showing') }} <strong>' + (p.to ? p.from + '–' + p.to : 0) + '</strong> {{ __('of') }} <strong>' + (p.total || 0) + '</strong> {{ __('results') }}'
                );

                setTimeout(function () { $('.loader').hide(); }, 400);
            },
            error: function () { $('.loader').hide(); }
        });
    }

    window.arFilterRequest = arFilterRequest;

    $(document).on('change', '.ar-cat-checkbox', function () { arFilterRequest(); });
    $('#ar-sort-select').on('change', function () { arFilterRequest(); });
    $(document).on('change', '.ar-rating-filter', function () { arFilterRequest(); });
    $(document).on('keydown', '#ar-search-input', function (e) {
        if (e.key === 'Enter') { arFilterRequest(); }
    });
    $(document).on('click', '#ar-clear-filters', function () {
        $('.ar-cat-checkbox').prop('checked', false);
        $('input[name=ar_rating]').prop('checked', false);
        $('#ar-min-price').val(0);
        $('#ar-max-price').val(10000);
        $('#ar-search-input').val('');
        arFilterRequest();
    });

        // .add-to-wishlist-btn handled globally in footer.blade.php

    // Quick View
    $(document).on('click', 'a.popup-modal', function (e) {
        e.preventDefault();
        var id = $(this).closest('[data-id]').data('id');
        $.ajax({
            type: 'GET',
            url: '{{ theme_quick_view_url() }}',
            data: { id: id },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                $('#product-modal').html(data.product_modal);
                setTimeout(function () { $('.loader').hide(); }, 400);
            },
            error: function () { $('.loader').hide(); }
        });
    });

    // Trigger initial load with pre-checked category
    arFilterRequest();
});
</script>
@endsection
