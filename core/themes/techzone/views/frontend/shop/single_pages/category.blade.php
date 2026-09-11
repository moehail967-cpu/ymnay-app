@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $category->name !!} @endsection
@section('page-title') {!! $category->name !!} @endsection

@section('content')
{{-- Page Banner --}}
<div class="tz-page-banner">
    <div class="container tz-page-banner-content">
        <h1>{!! $category->name !!}</h1>
        <div class="tz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
            <span class="sep">/</span>
            <span class="current">{!! $category->name !!}</span>
        </div>
    </div>
</div>

{{-- Category Nav --}}
@if($categories->isNotEmpty())
<div class="tz-cat-nav">
    <div class="container">
        <a href="javascript:void(0)" class="tz-cat-nav-link" data-slug="">{{ __('All') }}</a>
        @foreach($categories as $cat)
            <a href="javascript:void(0)"
               class="tz-cat-nav-link {{ $cat->slug === ($category->slug ?? '') ? 'active' : '' }}"
               data-slug="{{ $cat->slug }}">{{ $cat->name }}</a>
        @endforeach
    </div>
</div>
@endif

{{-- Shop Layout --}}
<div class="container tz-shop-container">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div class="tz-sidebar-card">
                <div class="tz-sidebar-title">{{ __('Search') }}</div>
                <div style="display:flex;border:1.5px solid var(--tz-border,#e0e0e0);border-radius:6px;overflow:hidden;">
                    <input type="text" id="tz-search-input" placeholder="{{ __('Search products…') }}"
                        style="flex:1;border:none;outline:none;padding:9px 12px;font-size:13px;background:transparent;">
                    <button type="button" onclick="tzFilterRequest()"
                        style="background:var(--tz-blue,#1565c0);color:#fff;border:none;padding:0 14px;cursor:pointer;">
                        <i class="las la-search"></i>
                    </button>
                </div>
            </div>

            @if($categories->isNotEmpty())
            <div class="tz-sidebar-card">
                <div class="tz-sidebar-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach($categories as $cat)
                    <label class="tz-filter-item">
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                            <input type="checkbox" class="tz-cat-checkbox" data-slug="{{ $cat->slug }}" data-value="{{ $cat->name }}"
                                {{ ($cat->slug === ($category->slug ?? '')) ? 'checked' : '' }}>
                            {{ $cat->name }}
                        </label>
                        <span class="tz-filter-count">{{ $cat->product_count ?? '' }}</span>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Price Range --}}
            <div class="tz-sidebar-card">
                <div class="tz-sidebar-title">{{ __('Price Range') }}</div>
                <div class="d-flex gap-2">
                    <input type="number" class="tz-input" id="tz-min-price" placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" class="tz-input" id="tz-max-price" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button class="tz-btn tz-btn-blue tz-btn-sm mt-3 w-100" onclick="tzFilterRequest()">
                    {{ __('Apply Filter') }}
                </button>
            </div>

            {{-- Rating --}}
            <div class="tz-sidebar-card">
                <div class="tz-sidebar-title">{{ __('Rating') }}</div>
                @foreach([5,4,3] as $star)
                <label class="tz-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                        <input type="radio" name="tz_rating" class="tz-rating-filter" value="{{ $star }}">
                        <span class="tz-star-filter">{{ str_repeat('★', $star) }}</span><span class="tz-star-empty">{{ str_repeat('★', 5-$star) }}</span>
                    </label>
                </label>
                @endforeach
                <div style="margin-top:10px;">
                    <a href="javascript:void(0)" onclick="$('.tz-rating-filter,.tz-cat-checkbox').prop('checked',false);tzFilterRequest();"
                        style="font-size:12px;color:var(--tz-muted,#888);text-decoration:none;">
                        <i class="las la-times-circle"></i> {{ __('Clear All Filters') }}
                    </a>
                </div>
            </div>

        </div>{{-- /sidebar --}}

        {{-- Products --}}
        <div class="col-lg-9">

            {{-- Sort Bar --}}
            <div class="tz-sort-bar">
                <span class="showing-results">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong> {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <div class="d-flex align-items-center gap-3">
                    <select class="tz-select" id="tz-sort-select">
                        <option value="3">{{ __('Sort: Latest') }}</option>
                        <option value="1">{{ __('Sort: Name') }}</option>
                        <option value="2">{{ __('Sort: Popular') }}</option>
                        <option value="4">{{ __('Price: Low → High') }}</option>
                        <option value="5">{{ __('Price: High → Low') }}</option>
                    </select>
                </div>
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
    function tzFilterRequest(page) {
        page = page || null;
        var cats = [];
        $('.tz-cat-checkbox:checked').each(function () { cats.push($(this).data('slug')); });
        var category_slug = cats.join(',') || null;
        var rating    = $('input[name=tz_rating]:checked').val() || null;
        var min_price = $('#tz-min-price').val() || 0;
        var max_price = $('#tz-max-price').val() || 10000;
        var sort      = $('#tz-sort-select').val();
        var search    = $('#tz-search-input').val() || null;

        $.ajax({
            type: 'GET',
            url: '{{ theme_shop_filter_url() }}',
            data: { category: category_slug, rating: rating, min_price: min_price, max_price: max_price, sort: sort, page: page, search: search },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                $('.grid-product-list').html(data.grid);
                var p = data.pagination;
                $('.showing-results').html('{{ __('Showing') }} <strong>' + (p.to ? p.from + '-' + p.to : 0) + '</strong> {{ __('of') }} <strong>' + (p.total || 0) + '</strong> {{ __('results') }}');
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    }

    window.tzFilterRequest = tzFilterRequest;

    $(document).on('click', '.tz-cat-nav-link', function (e) {
        e.preventDefault();
        $('.tz-cat-nav-link').removeClass('active');
        $(this).addClass('active');
        var slug = $(this).data('slug');
        $('.tz-cat-checkbox').prop('checked', false);
        if (slug) { $('.tz-cat-checkbox[data-slug="' + slug + '"]').prop('checked', true); }
        tzFilterRequest();
    });

    $(document).on('change', '.tz-cat-checkbox', function () { tzFilterRequest(); });
    $('#tz-sort-select').on('change', function () { tzFilterRequest(); });
    $(document).on('change', '.tz-rating-filter', function () { tzFilterRequest(); });

    $(document).on('keydown', '#tz-search-input', function (e) {
        if (e.key === 'Enter') { tzFilterRequest(); }
    });

        // .add-to-wishlist-btn handled globally in footer.blade.php
});
</script>
@endsection
