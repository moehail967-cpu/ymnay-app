@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $category->name !!} @endsection
@section('page-title') {!! $category->name !!} @endsection

@section('content')

{{-- Page Banner --}}
<div class="cs-page-banner">
    <div class="container">
        <h1 class="cs-page-banner-title">{!! $category->name !!}</h1>
        <div class="cs-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="cs-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <span class="cs-breadcrumb-current">{!! $category->name !!}</span>
        </div>
    </div>
</div>

{{-- Shop Layout --}}
<div class="container cs-shop-wrap">
    <div class="row g-4">

        {{-- ===== Sidebar ===== --}}
        <div class="col-lg-3">

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div class="cs-sidebar-card">
                <div class="cs-sidebar-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach($categories as $cat)
                    <label class="cs-filter-item">
                        <span class="d-flex align-items-center gap-2 cs-filter-cursor">
                            <input type="checkbox" class="cs-cat-checkbox" data-slug="{{ $cat->slug }}"
                                {{ isset($category) && $category->slug === $cat->slug ? 'checked' : '' }}>
                            {{ $cat->name }}
                        </span>
                        <span class="cs-filter-count">{{ $cat->product_count ?? '' }}</span>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Price Range --}}
            <div class="cs-sidebar-card">
                <div class="cs-sidebar-title">{{ __('Price Range') }}</div>
                <div class="cs-price-inputs">
                    <input type="number" class="cs-price-input" id="cs-min-price" placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" class="cs-price-input" id="cs-max-price" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button class="cs-apply-btn w-100 mt-3" onclick="csFilterRequest()">{{ __('Apply Filter') }}</button>
            </div>

            {{-- Rating --}}
            <div class="cs-sidebar-card">
                <div class="cs-sidebar-title">{{ __('Rating') }}</div>
                <label class="cs-filter-item">
                    <span class="d-flex align-items-center gap-2 cs-filter-cursor">
                        <input type="radio" name="cs_rating" class="cs-rating-filter" value="5">
                        <span class="cs-stars-active">★★★★★</span>
                    </span>
                </label>
                <label class="cs-filter-item">
                    <span class="d-flex align-items-center gap-2 cs-filter-cursor">
                        <input type="radio" name="cs_rating" class="cs-rating-filter" value="4">
                        <span><span class="cs-stars-active">★★★★</span><span class="cs-stars-empty">★</span></span>
                    </span>
                </label>
                <label class="cs-filter-item">
                    <span class="d-flex align-items-center gap-2 cs-filter-cursor">
                        <input type="radio" name="cs_rating" class="cs-rating-filter" value="3">
                        <span><span class="cs-stars-active">★★★</span><span class="cs-stars-empty">★★</span></span>
                    </span>
                </label>
                <label class="cs-filter-item">
                    <span class="d-flex align-items-center gap-2 cs-filter-cursor">
                        <input type="radio" name="cs_rating" class="cs-rating-filter" value="2">
                        <span><span class="cs-stars-active">★★</span><span class="cs-stars-empty">★★★</span></span>
                    </span>
                </label>
                <a href="javascript:void(0)" class="cs-clear-link" id="cs-clear-filters">
                    <i class="las la-times-circle"></i> {{ __('Clear All Filters') }}
                </a>
            </div>

        </div>{{-- /sidebar --}}

        {{-- ===== Products ===== --}}
        <div class="col-lg-9">

            {{-- Topbar --}}
            <div class="cs-shop-topbar">
                <span class="cs-topbar-count showing-results">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <select class="cs-sort-select" id="cs-sort-select">
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
@include(include_theme_path('shop.partials.product-quick-view'))

@endsection

@section('scripts')
<script>
$(function () {

    function csFilterRequest(page) {
        page = page || null;

        var cats = [];
        $('.cs-cat-checkbox:checked').each(function () { cats.push($(this).data('slug')); });
        var category_slug = cats.join(',') || null;

        var rating    = $('input[name=cs_rating]:checked').val() || null;
        var min_price = $('#cs-min-price').val() || 0;
        var max_price = $('#cs-max-price').val() || 10000;
        var sort      = $('#cs-sort-select').val();

        $.ajax({
            type: 'GET',
            url: '{{ theme_shop_filter_url() }}',
            data: {
                category:  category_slug,
                rating:    rating,
                min_price: min_price,
                max_price: max_price,
                sort:      sort,
                page:      page
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

    window.csFilterRequest = csFilterRequest;

    // Category checkboxes
    $(document).on('change', '.cs-cat-checkbox', function () { csFilterRequest(); });

    // Sort select
    $('#cs-sort-select').on('change', function () { csFilterRequest(); });

    // Rating filter
    $(document).on('change', '.cs-rating-filter', function () { csFilterRequest(); });

    // Clear all filters
    $(document).on('click', '#cs-clear-filters', function () {
        $('.cs-cat-checkbox').prop('checked', false);
        $('input[name=cs_rating]').prop('checked', false);
        $('#cs-min-price').val(0);
        $('#cs-max-price').val(10000);
        csFilterRequest();
    });

    // Quick view modal
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

});
</script>
@endsection
