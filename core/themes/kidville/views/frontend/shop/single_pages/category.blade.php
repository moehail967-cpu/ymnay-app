@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $category->name !!} @endsection

@section('style')
<style>
.kv-sort-bar { background: #fff; border: 2.5px solid var(--kv-border); border-radius: var(--kv-radius); padding: 12px 18px; display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 22px; font-size: 13px; font-weight: 700; color: var(--kv-muted); }
.kv-view-toggle button { width: 34px; height: 34px; border: 2px solid var(--kv-border); background: #fff; border-radius: var(--kv-radius-sm); cursor: pointer; color: var(--kv-muted); font-size: 16px; display: inline-flex; align-items: center; justify-content: center; transition: all .2s; }
.kv-view-toggle button.active, .kv-view-toggle button:hover { background: var(--kv-red); border-color: var(--kv-red); color: #fff; }
.kv-price-inputs { display: flex; gap: 8px; }
.kv-price-input { flex: 1; padding: 8px 10px; border: 2px solid var(--kv-border); border-radius: var(--kv-radius-sm); font-size: 13px; width: 100%; font-family: var(--kv-font); outline: none; }
.kv-price-input:focus { border-color: var(--kv-red); }
.kv-star-filter { color: var(--kv-yellow); }
</style>
@endsection

@section('content')
{{-- Page Banner --}}
<div class="kv-page-banner">
    <div class="container kv-page-banner-content">
        <h1>{!! $category->name !!}</h1>
        <div class="kv-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
            <span class="sep">/</span>
            <span class="current">{!! $category->name !!}</span>
        </div>
    </div>
</div>

{{-- Category Nav --}}
@if(isset($categories) && $categories->isNotEmpty())
<div class="kv-cat-nav">
    <div class="container">
        <a href="javascript:void(0)" class="kv-cat-link" data-slug="">{{ __('All') }}</a>
        @foreach($categories as $cat)
            <a href="javascript:void(0)"
               class="kv-cat-link {{ $cat->slug === ($category->slug ?? '') ? 'active' : '' }}"
               data-slug="{{ $cat->slug }}">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>
</div>
@endif

{{-- Shop Layout --}}
<div class="container" style="padding: 36px 0 72px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div class="kv-sidebar-card">
                <div class="kv-sidebar-title">{{ __('Search') }}</div>
                <div style="display:flex;border:2px solid var(--kv-border,#e0e0e0);border-radius:var(--kv-radius-sm,6px);overflow:hidden;">
                    <input type="text" id="kv-search-input" placeholder="{{ __('Search products…') }}"
                        style="flex:1;border:none;outline:none;padding:9px 12px;font-size:13px;background:transparent;font-family:var(--kv-font);">
                    <button type="button" onclick="kvFilterRequest()"
                        style="background:var(--kv-red,#e53935);color:#fff;border:none;padding:0 14px;cursor:pointer;">
                        <i class="las la-search"></i>
                    </button>
                </div>
            </div>

            @if(isset($categories) && $categories->isNotEmpty())
            <div class="kv-sidebar-card">
                <div class="kv-sidebar-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach($categories as $cat)
                    <label class="kv-filter-item">
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                            <input type="checkbox" class="kv-cat-checkbox" data-slug="{{ $cat->slug }}" data-value="{{ $cat->name }}"
                                style="accent-color:var(--kv-red);"
                                {{ ($cat->slug === ($category->slug ?? '')) ? 'checked' : '' }}>
                            {{ $cat->name }}
                        </label>
                        <span class="kv-filter-count">{{ $cat->product_count ?? '' }}</span>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="kv-sidebar-card">
                <div class="kv-sidebar-title">{{ __('Price Range') }}</div>
                <div class="kv-price-inputs">
                    <input type="number" class="kv-price-input" id="kv-min-price" placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" class="kv-price-input" id="kv-max-price" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button class="kv-btn kv-btn-red kv-btn-sm mt-3 w-100" onclick="kvFilterRequest()">
                    {{ __('Apply Filter') }}
                </button>
            </div>

            <div class="kv-sidebar-card">
                <div class="kv-sidebar-title">{{ __('Rating') }}</div>
                <label class="kv-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                        <input type="radio" name="kv_rating" class="kv-rating-filter" value="5" style="accent-color:var(--kv-red);">
                        <span class="kv-star-filter">★★★★★</span>
                    </label>
                </label>
                <label class="kv-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                        <input type="radio" name="kv_rating" class="kv-rating-filter" value="4" style="accent-color:var(--kv-red);">
                        <span class="kv-star-filter">★★★★</span><span style="color:#ccc;">★</span>
                    </label>
                </label>
                <label class="kv-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                        <input type="radio" name="kv_rating" class="kv-rating-filter" value="3" style="accent-color:var(--kv-red);">
                        <span class="kv-star-filter">★★★</span><span style="color:#ccc;">★★</span>
                    </label>
                </label>
                <div style="margin-top:10px;">
                    <a href="javascript:void(0)" onclick="$('.kv-rating-filter,.kv-cat-checkbox').prop('checked',false);kvFilterRequest();"
                        style="font-size:12px;color:var(--kv-muted,#888);text-decoration:none;">
                        <i class="las la-times-circle"></i> {{ __('Clear All Filters') }}
                    </a>
                </div>
            </div>

        </div>{{-- /sidebar --}}

        {{-- Products --}}
        <div class="col-lg-9">

            <div class="kv-sort-bar">
                <span class="showing-results">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong> {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <div class="d-flex align-items-center gap-3">
                    <select class="kv-select" id="kv-sort-select" style="width:auto;padding:8px 36px 8px 12px;font-size:13px;">
                        <option value="3">{{ __('Sort: Latest') }}</option>
                        <option value="1">{{ __('Sort: Name') }}</option>
                        <option value="2">{{ __('Sort: Popular') }}</option>
                        <option value="4">{{ __('Price: Low to High') }}</option>
                        <option value="5">{{ __('Price: High to Low') }}</option>
                    </select>
                    <div class="kv-view-toggle d-flex gap-1">
                        <button class="active" id="kv-grid-btn" title="{{ __('Grid view') }}">
                            <i class="las la-th"></i>
                        </button>
                    </div>
                </div>
            </div>

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
    function kvFilterRequest(page) {
        page = page || null;
        var cats = [];
        $('.kv-cat-checkbox:checked').each(function () { cats.push($(this).data('slug')); });
        var category_slug = cats.join(',') || null;
        var rating    = $('input[name=kv_rating]:checked').val() || null;
        var min_price = $('#kv-min-price').val() || 0;
        var max_price = $('#kv-max-price').val() || 10000;
        var sort      = $('#kv-sort-select').val();
        var search    = $('#kv-search-input').val() || null;

        $.ajax({
            type: 'GET',
            url: '{{ theme_shop_filter_url() }}',
            data: { category: category_slug, rating: rating, min_price: min_price, max_price: max_price, sort: sort, page: page, search: search },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                $('.grid-product-list').html(data.grid);
                var p = data.pagination;
                $('.showing-results').html('{{ __('Showing') }} <strong>' + (p.to ? p.from + '-' + p.to : 0) + '</strong> {{ __('of') }} <strong>' + (p.total || 0) + '</strong> {{ __('results') }}');
                setTimeout(function () { $('.loader').hide(); }, 500);
            },
            error: function () { $('.loader').hide(); }
        });
    }

    window.kvFilterRequest = kvFilterRequest;

    $(document).on('click', '.kv-cat-nav .kv-cat-link', function (e) {
        e.preventDefault();
        $('.kv-cat-nav .kv-cat-link').removeClass('active');
        $(this).addClass('active');
        var slug = $(this).data('slug');
        $('.kv-cat-checkbox').prop('checked', false);
        if (slug) { $('.kv-cat-checkbox[data-slug="' + slug + '"]').prop('checked', true); }
        kvFilterRequest();
    });

    $(document).on('change', '.kv-cat-checkbox', function () { kvFilterRequest(); });
    $('#kv-sort-select').on('change', function () { kvFilterRequest(); });
    $(document).on('change', '.kv-rating-filter', function () { kvFilterRequest(); });

    $(document).on('keydown', '#kv-search-input', function (e) {
        if (e.key === 'Enter') { kvFilterRequest(); }
    });

        // .add-to-cart-btn handled globally in footer.blade.php
});
</script>
@endsection
