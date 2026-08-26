@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title !!} @endsection
@section('page-title') {!! $page_post->title !!} @endsection

@section('content')

{{-- Page Banner --}}
<div class="bp-page-banner">
    <div class="container">
        <h1>{!! $page_post->title !!}</h1>
        <div class="bp-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="las la-angle-right"></i></span>
            <span class="current">{!! $page_post->title !!}</span>
        </div>
    </div>
</div>

{{-- Shop Layout --}}
<div class="container bp-shop-wrap">
    <div class="row g-4">

        {{-- ===== Sidebar ===== --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div class="bp-sidebar-card">
                <div class="bp-sidebar-title">{{ __('Search') }}</div>
                <form class="bp-shop-search" onsubmit="bpFilterRequest();return false;">
                    <input type="text" id="bp-search-input" placeholder="{{ __('Search products…') }}">
                    <button type="submit"><i class="las la-search"></i></button>
                </form>
            </div>

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div class="bp-sidebar-card">
                <div class="bp-sidebar-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach($categories as $cat)
                    <label class="bp-filter-item">
                        <label class="d-flex align-items-center gap-2 mb-0 bp-filter-cursor">
                            <input type="checkbox" class="bp-cat-checkbox"
                                   data-slug="{{ $cat->slug }}"
                                   data-value="{{ $cat->name }}">
                            {{ $cat->name }}
                        </label>
                        <span class="bp-filter-count">{{ $cat->product_count ?? '' }}</span>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Price Range --}}
            <div class="bp-sidebar-card">
                <div class="bp-sidebar-title">{{ __('Price Range') }}</div>
                <div class="bp-price-inputs">
                    <input type="number" class="bp-price-input" id="bp-min-price"
                           placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" class="bp-price-input" id="bp-max-price"
                           placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button class="bp-btn bp-btn-green bp-btn-sm mt-3 w-100" onclick="bpFilterRequest()">
                    {{ __('Apply Filter') }}
                </button>
            </div>

            {{-- Rating --}}
            <div class="bp-sidebar-card">
                <div class="bp-sidebar-title">{{ __('Rating') }}</div>
                <label class="bp-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0 bp-filter-cursor">
                        <input type="radio" name="bp_rating" class="bp-rating-filter" value="5">
                        <span class="bp-star-filter">★★★★★</span>
                    </label>
                </label>
                <label class="bp-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0 bp-filter-cursor">
                        <input type="radio" name="bp_rating" class="bp-rating-filter" value="4">
                        <span><span class="bp-star-active">★★★★</span><span class="bp-star-empty">★</span></span>
                    </label>
                </label>
                <label class="bp-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0 bp-filter-cursor">
                        <input type="radio" name="bp_rating" class="bp-rating-filter" value="3">
                        <span><span class="bp-star-active">★★★</span><span class="bp-star-empty">★★</span></span>
                    </label>
                </label>
                <label class="bp-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0 bp-filter-cursor">
                        <input type="radio" name="bp_rating" class="bp-rating-filter" value="2">
                        <span><span class="bp-star-active">★★</span><span class="bp-star-empty">★★★</span></span>
                    </label>
                </label>
                <a href="javascript:void(0)" class="bp-clear-link" id="bp-clear-filters">
                    <i class="las la-times-circle"></i> {{ __('Clear All Filters') }}
                </a>
            </div>

        </div>{{-- /sidebar --}}

        {{-- ===== Products ===== --}}
        <div class="col-lg-9">

            {{-- Topbar --}}
            <div class="bp-shop-topbar">
                <span class="bp-topbar-count showing-results">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <select class="bp-sort-select" id="bp-sort-select">
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

    function bpFilterRequest(page) {
        page = page || null;

        var cats = [];
        $('.bp-cat-checkbox:checked').each(function () { cats.push($(this).data('slug')); });
        var category_slug = cats.join(',') || null;

        var rating    = $('input[name=bp_rating]:checked').val() || null;
        var min_price = $('#bp-min-price').val() || 0;
        var max_price = $('#bp-max-price').val() || 10000;
        var sort      = $('#bp-sort-select').val();
        var search    = $('#bp-search-input').val() || null;

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

    // Expose globally so pagination buttons in grid-products partial can call it
    window.bpFilterRequest = bpFilterRequest;

    // Sidebar category checkboxes
    $(document).on('change', '.bp-cat-checkbox', function () { bpFilterRequest(); });

    // Sort select
    $('#bp-sort-select').on('change', function () { bpFilterRequest(); });

    // Rating filter
    $(document).on('change', '.bp-rating-filter', function () { bpFilterRequest(); });

    // Search on Enter
    $(document).on('keydown', '#bp-search-input', function (e) {
        if (e.key === 'Enter') { bpFilterRequest(); }
    });

    // Clear all filters
    $(document).on('click', '#bp-clear-filters', function () {
        $('.bp-cat-checkbox').prop('checked', false);
        $('input[name=bp_rating]').prop('checked', false);
        $('#bp-min-price').val(0);
        $('#bp-max-price').val(10000);
        $('#bp-search-input').val('');
        bpFilterRequest();
    });

});
</script>
@endsection
