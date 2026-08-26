@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $category->name !!} @endsection
@section('page-title') {!! $category->name !!} @endsection

@section('content')
{{-- Page Banner --}}
<div class="bk-page-banner">
    <div class="container">
        <h1>{!! $category->name !!}</h1>
        <div class="bk-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{!! $category->name !!}</span>
        </div>
    </div>
</div>

{{-- Shop Layout --}}
<div class="container bk-shop-wrap">
    <div class="row g-4">

        {{-- ===== Sidebar ===== --}}
        <div class="col-lg-3">

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div class="bk-sidebar-card">
                <div class="bk-sidebar-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach($categories as $cat)
                    <label class="bk-filter-item">
                        <label class="d-flex align-items-center gap-2 mb-0 bk-filter-cursor">
                            <input type="checkbox" class="bk-cat-checkbox" data-slug="{{ $cat->slug }}" data-value="{{ $cat->name }}">
                            {{ $cat->name }}
                        </label>
                        <span class="bk-filter-count">{{ $cat->product_count ?? '' }}</span>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Price Range --}}
            <div class="bk-sidebar-card">
                <div class="bk-sidebar-title">{{ __('Price Range') }}</div>
                <div class="bk-price-inputs">
                    <input type="number" class="bk-price-input" id="bk-min-price" placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" class="bk-price-input" id="bk-max-price" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button class="bk-btn bk-btn-rose bk-btn-sm mt-3 w-100" onclick="bkFilterRequest()">
                    {{ __('Apply Filter') }}
                </button>
            </div>

            {{-- Rating --}}
            <div class="bk-sidebar-card">
                <div class="bk-sidebar-title">{{ __('Rating') }}</div>
                <label class="bk-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0 bk-filter-cursor">
                        <input type="radio" name="bk_rating" class="bk-rating-filter" value="5">
                        <span class="bk-star-filter">★★★★★</span>
                    </label>
                </label>
                <label class="bk-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0 bk-filter-cursor">
                        <input type="radio" name="bk_rating" class="bk-rating-filter" value="4">
                        <span><span class="bk-star-active">★★★★</span><span class="bk-star-empty">★</span></span>
                    </label>
                </label>
                <label class="bk-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0 bk-filter-cursor">
                        <input type="radio" name="bk_rating" class="bk-rating-filter" value="3">
                        <span><span class="bk-star-active">★★★</span><span class="bk-star-empty">★★</span></span>
                    </label>
                </label>
                <a href="javascript:void(0)" class="bk-clear-link" id="bk-clear-filters">
                    <i class="mdi mdi-close-circle-outline"></i> {{ __('Clear All Filters') }}
                </a>
            </div>

        </div>{{-- /sidebar --}}

        {{-- ===== Products ===== --}}
        <div class="col-lg-9">

            {{-- Topbar --}}
            <div class="bk-topbar">
                <span class="bk-topbar-count showing-results">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <select class="bk-sort-select" id="bk-sort-select">
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

    function bkFilterRequest(page) {
        page = page || null;

        var cats = [];
        $('.bk-cat-checkbox:checked').each(function () { cats.push($(this).data('slug')); });
        var category_slug = cats.join(',') || null;

        var rating    = $('input[name=bk_rating]:checked').val() || null;
        var min_price = $('#bk-min-price').val() || 0;
        var max_price = $('#bk-max-price').val() || 10000;
        var sort      = $('#bk-sort-select').val();

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

    window.bkFilterRequest = bkFilterRequest;

    // Sidebar category checkboxes
    $(document).on('change', '.bk-cat-checkbox', function () { bkFilterRequest(); });

    // Sort select
    $('#bk-sort-select').on('change', function () { bkFilterRequest(); });

    // Rating filter
    $(document).on('change', '.bk-rating-filter', function () { bkFilterRequest(); });

    // Clear all filters
    $(document).on('click', '#bk-clear-filters', function () {
        $('.bk-cat-checkbox').prop('checked', false);
        $('input[name=bk_rating]').prop('checked', false);
        $('#bk-min-price').val(0);
        $('#bk-max-price').val(10000);
        bkFilterRequest();
    });



});
</script>
@endsection
