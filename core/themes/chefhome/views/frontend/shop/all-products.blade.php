@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title !!} @endsection
@section('page-title') {!! $page_post->title !!} @endsection


@section('content')
{{-- Page Banner --}}
<div class="ch-page-banner">
    <div class="container ch-page-banner-content">
        <h1>{!! $page_post->title !!}</h1>
        <div class="ch-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{!! $page_post->title !!}</span>
        </div>
    </div>
</div>


{{-- Shop Layout --}}
<div class="container" style="padding: 36px 0 72px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div class="ch-sidebar-card">
                <div class="ch-sidebar-title">{{ __('Search') }}</div>
                <form class="ch-search-form" onsubmit="chFilterRequest();return false;">
                    <input type="text" id="ch-search-input" class="ch-search-input" placeholder="{{ __('Search products…') }}" autocomplete="off">
                    <button type="submit" class="ch-search-btn"><i class="las la-search"></i></button>
                </form>
            </div>

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div class="ch-sidebar-card">
                <div class="ch-sidebar-title">{{ __('Categories') }}</div>
                <ul class="category-lists active-list list-unstyled mb-0">
                    @foreach($categories as $cat)
                    <label class="ch-filter-item">
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                            <input type="checkbox" class="ch-cat-checkbox" data-slug="{{ $cat->slug }}" data-value="{{ $cat->name }}">
                            {{ $cat->name }}
                        </label>
                        <span class="ch-filter-count">{{ $cat->product_count ?? '' }}</span>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Price Range --}}
            <div class="ch-sidebar-card">
                <div class="ch-sidebar-title">{{ __('Price Range') }}</div>
                <div class="ch-price-inputs">
                    <input type="number" class="ch-price-input" id="ch-min-price" placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" class="ch-price-input" id="ch-max-price" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button class="ch-btn ch-btn-red ch-btn-sm mt-3 w-100" onclick="chFilterRequest()">
                    {{ __('Apply Filter') }}
                </button>
            </div>

            {{-- Rating --}}
            <div class="ch-sidebar-card">
                <div class="ch-sidebar-title">{{ __('Rating') }}</div>
                <label class="ch-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                        <input type="radio" name="ch_rating" class="ch-rating-filter" value="5">
                        <span class="ch-star-filter">★★★★★</span>
                    </label>
                </label>
                <label class="ch-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                        <input type="radio" name="ch_rating" class="ch-rating-filter" value="4">
                        <span class="ch-star-filter">★★★★</span><span style="color:#ccc;">★</span>
                    </label>
                </label>
                <label class="ch-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                        <input type="radio" name="ch_rating" class="ch-rating-filter" value="3">
                        <span class="ch-star-filter">★★★</span><span style="color:#ccc;">★★</span>
                    </label>
                </label>
            </div>

        </div>{{-- /sidebar --}}

        {{-- Products --}}
        <div class="col-lg-9">

            {{-- Sort Bar --}}
            <div class="ch-sort-bar">
                <span class="showing-results">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong> {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <div class="d-flex align-items-center gap-3">
                    <select class="ch-select" id="ch-sort-select" style="width:auto;padding:8px 36px 8px 12px;font-size:13px;">
                        <option value="3">{{ __('Sort: Latest') }}</option>
                        <option value="1">{{ __('Sort: Name') }}</option>
                        <option value="2">{{ __('Sort: Popular') }}</option>
                        <option value="4">{{ __('Price: Low to High') }}</option>
                        <option value="5">{{ __('Price: High to Low') }}</option>
                    </select>
                    <div class="ch-view-toggle d-flex gap-1">
                        <button class="active" id="ch-grid-btn" title="{{ __('Grid view') }}">
                            <i class="las la-th"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="row g-3 grid-product-list">
                @include(include_theme_path('shop.partials.product-partials.grid-products'))
            </div>

        </div>{{-- /products --}}
    </div>
</div>

{{-- Quick View Modal container --}}
<div id="product-modal"></div>
@endsection

@section('scripts')
<script>
$(function () {
    function chFilterRequest(page) {
        page = page || null;

        // Collect checked categories
        var cats = [];
        $('.ch-cat-checkbox:checked').each(function () { cats.push($(this).data('slug')); });
        var category_slug = cats.join(',') || null;

        var rating    = $('input[name=ch_rating]:checked').val() || null;
        var min_price = $('#ch-min-price').val() || 0;
        var max_price = $('#ch-max-price').val() || 10000;
        var sort      = $('#ch-sort-select').val();
        var search    = $('#ch-search-input').val() || null;

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
                var from = p.from || 0;
                var total = p.total || 0;
                var count = p.to ? (p.to - from + 1) : 0;
                $('.showing-results').html('{{ __('Showing') }} <strong>' + (p.to ? p.from + '-' + p.to : 0) + '</strong> {{ __('of') }} <strong>' + total + '</strong> {{ __('results') }}');

                setTimeout(function () { $('.loader').hide(); }, 500);
            },
            error: function () { $('.loader').hide(); }
        });
    }

    // Expose globally so grid-products pagination buttons can call it
    window.chFilterRequest = chFilterRequest;

    // Category nav pills (top)
    $(document).on('click', '.ch-cat-nav .ch-cat-link', function (e) {
        e.preventDefault();
        $('.ch-cat-nav .ch-cat-link').removeClass('active');
        $(this).addClass('active');
        var slug = $(this).data('slug');
        // Sync sidebar checkbox
        $('.ch-cat-checkbox').prop('checked', false);
        if (slug) {
            $('.ch-cat-checkbox[data-slug="' + slug + '"]').prop('checked', true);
        }
        chFilterRequest();
    });

    // Sidebar category checkboxes
    $(document).on('change', '.ch-cat-checkbox', function () {
        chFilterRequest();
    });

    // Sort select
    $('#ch-sort-select').on('change', function () { chFilterRequest(); });

    // Rating filter
    $(document).on('change', '.ch-rating-filter', function () { chFilterRequest(); });

    // Search on Enter
    $(document).on('keydown', '#ch-search-input', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); chFilterRequest(); }
    });

    // Quick view (fallback to product page)
    $(document).on('click', '.ch-card-img a, .ch-card-title a', function (e) {
        // normal navigation
    });
});
</script>
@endsection
