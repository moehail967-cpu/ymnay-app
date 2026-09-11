@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $category->name !!} @endsection
@section('page-title') {!! $category->name !!} @endsection

@section('style')
<style>
.ph-sort-bar { background: var(--ph-white); border: 2px solid var(--ph-border); border-radius: var(--ph-radius); padding: 12px 18px; display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 22px; font-size: 13px; color: var(--ph-muted); flex-wrap: wrap; }
.ph-view-toggle button { width: 34px; height: 34px; border: 2px solid var(--ph-border); background: #fff; border-radius: 6px; cursor: pointer; color: var(--ph-muted); font-size: 16px; display: inline-flex; align-items: center; justify-content: center; transition: all .2s; }
.ph-view-toggle button.active, .ph-view-toggle button:hover { background: var(--ph-terra); border-color: var(--ph-terra); color: #fff; }
</style>
@endsection

@section('content')
{{-- Page Banner --}}
<div class="ph-page-banner">
    <div class="container">
        <h1>{!! $category->name !!}</h1>
        <div class="ph-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
            <span class="sep">/</span>
            <span class="current">{!! $category->name !!}</span>
        </div>
    </div>
</div>

{{-- Shop Layout --}}
<div class="container" style="padding: 36px 0 72px; background: var(--ph-bg);">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div class="ph-sidebar-card">
                <div class="ph-sidebar-title">{{ __('Search') }}</div>
                <div style="display:flex;border:1.5px solid var(--ph-border,#e0e0e0);border-radius:var(--ph-radius,6px);overflow:hidden;">
                    <input type="text" id="ph-search-input" placeholder="{{ __('Search products…') }}"
                        style="flex:1;border:none;outline:none;padding:9px 12px;font-size:13px;background:transparent;">
                    <button type="button" onclick="phFilterRequest()"
                        style="background:var(--ph-terra,#8B5E3C);color:#fff;border:none;padding:0 14px;cursor:pointer;">
                        <i class="las la-search"></i>
                    </button>
                </div>
            </div>

            {{-- Categories --}}
            @if(isset($categories) && $categories->isNotEmpty())
            <div class="ph-sidebar-card">
                <div class="ph-sidebar-title">{{ __('Categories') }}</div>
                @foreach($categories as $cat)
                <label class="ph-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                        <input type="checkbox" class="ph-cat-checkbox" data-slug="{{ $cat->slug }}" data-value="{{ $cat->name }}"
                            {{ ($cat->slug === ($category->slug ?? '')) ? 'checked' : '' }}>
                        {{ $cat->name }}
                    </label>
                    <span class="ph-filter-count">{{ $cat->product_count ?? '' }}</span>
                </label>
                @endforeach
            </div>
            @endif

            {{-- Price Range --}}
            <div class="ph-sidebar-card">
                <div class="ph-sidebar-title">{{ __('Price Range') }}</div>
                <div class="ph-price-inputs">
                    <input type="number" class="ph-price-input" id="ph-min-price" placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" class="ph-price-input" id="ph-max-price" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button class="ph-btn ph-btn-terra ph-btn-sm mt-3 w-100 justify-content-center" onclick="phFilterRequest()">
                    {{ __('Apply Filter') }}
                </button>
            </div>

            {{-- Rating --}}
            <div class="ph-sidebar-card">
                <div class="ph-sidebar-title">{{ __('Rating') }}</div>
                <label class="ph-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                        <input type="radio" name="ph_rating" class="ph-rating-filter" value="5">
                        <span class="ph-star-filter">★★★★★</span>
                    </label>
                </label>
                <label class="ph-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                        <input type="radio" name="ph_rating" class="ph-rating-filter" value="4">
                        <span class="ph-star-filter">★★★★</span><span style="color:#ccc;">★</span>
                    </label>
                </label>
                <label class="ph-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                        <input type="radio" name="ph_rating" class="ph-rating-filter" value="3">
                        <span class="ph-star-filter">★★★</span><span style="color:#ccc;">★★</span>
                    </label>
                </label>
                <div style="margin-top:10px;">
                    <a href="javascript:void(0)" onclick="$('.ph-rating-filter,.ph-cat-checkbox').prop('checked',false);phFilterRequest();"
                        style="font-size:12px;color:var(--ph-muted,#888);text-decoration:none;">
                        <i class="las la-times-circle"></i> {{ __('Clear All Filters') }}
                    </a>
                </div>
            </div>

        </div>{{-- /sidebar --}}

        {{-- Products --}}
        <div class="col-lg-9">

            {{-- Sort Bar --}}
            <div class="ph-sort-bar">
                <span class="showing-results">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong> {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <div class="d-flex align-items-center gap-3">
                    <select class="ph-select" id="ph-sort-select" style="width:auto;">
                        <option value="3">{{ __('Sort: Latest') }}</option>
                        <option value="1">{{ __('Sort: Name') }}</option>
                        <option value="2">{{ __('Sort: Popular') }}</option>
                        <option value="4">{{ __('Price: Low to High') }}</option>
                        <option value="5">{{ __('Price: High to Low') }}</option>
                    </select>
                    <div class="ph-view-toggle d-flex gap-1">
                        <button class="active" id="ph-grid-btn" title="{{ __('Grid view') }}">
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

<div id="product-modal"></div>
@endsection

@section('scripts')
<script>
$(function () {
    function phFilterRequest(page) {
        page = page || null;

        var cats = [];
        $('.ph-cat-checkbox:checked').each(function () { cats.push($(this).data('slug')); });
        var category_slug = cats.join(',') || null;

        var rating    = $('input[name=ph_rating]:checked').val() || null;
        var min_price = $('#ph-min-price').val() || 0;
        var max_price = $('#ph-max-price').val() || 10000;
        var sort      = $('#ph-sort-select').val();
        var search    = $('#ph-search-input').val() || null;

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

    window.phFilterRequest = phFilterRequest;

    $(document).on('change', '.ph-cat-checkbox', function () { phFilterRequest(); });
    $('#ph-sort-select').on('change', function () { phFilterRequest(); });
    $(document).on('change', '.ph-rating-filter', function () { phFilterRequest(); });

    $(document).on('keydown', '#ph-search-input', function (e) {
        if (e.key === 'Enter') { phFilterRequest(); }
    });
});
</script>
@endsection
