@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $category->name !!} @endsection

@section('content')
{{-- Page Banner --}}
<div class="fm-page-banner">
    <div class="container">
        <h1 style="font-size:28px;font-weight:800;color:var(--fm-dark);margin-bottom:8px;">{!! $category->name !!}</h1>
        <div class="fm-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="las la-angle-right" style="font-size:11px;"></i></span>
            <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
            <span class="sep"><i class="las la-angle-right" style="font-size:11px;"></i></span>
            <span class="current">{!! $category->name !!}</span>
        </div>
    </div>
</div>

{{-- Shop Layout --}}
<div class="container" style="padding: 36px 0 72px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div class="fm-sidebar-card">
                <div class="fm-sidebar-title">{{ __('Search') }}</div>
                <div style="display:flex;border:1.5px solid var(--fm-border,#e0e0e0);border-radius:6px;overflow:hidden;">
                    <input type="text" id="fm-search-input" placeholder="{{ __('Search products…') }}"
                        style="flex:1;border:none;outline:none;padding:9px 12px;font-size:13px;background:transparent;">
                    <button type="button" onclick="fmFilterRequest()"
                        style="background:var(--fm-green,#28a745);color:#fff;border:none;padding:0 14px;cursor:pointer;">
                        <i class="las la-search"></i>
                    </button>
                </div>
            </div>

            {{-- Categories --}}
            @if(isset($categories) && $categories->isNotEmpty())
            <div class="fm-sidebar-card">
                <div class="fm-sidebar-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach($categories as $cat)
                    <label class="fm-filter-item" style="display:flex;">
                        <label class="fm-filter-label" style="cursor:pointer;">
                            <input type="checkbox" class="fm-cat-checkbox" data-slug="{{ $cat->slug }}" data-value="{{ $cat->name }}"
                                style="accent-color:var(--fm-green);"
                                {{ ($cat->slug === ($category->slug ?? '')) ? 'checked' : '' }}>
                            {{ $cat->name }}
                        </label>
                        <span class="fm-filter-count">{{ $cat->product_count ?? '' }}</span>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Price Range --}}
            <div class="fm-sidebar-card">
                <div class="fm-sidebar-title">{{ __('Price Range') }}</div>
                <div class="d-flex gap-2">
                    <input type="number" class="fm-price-input" id="fm-min-price" placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" class="fm-price-input" id="fm-max-price" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button class="fm-btn fm-btn-green fm-btn-sm mt-3 w-100" onclick="fmFilterRequest()">
                    {{ __('Apply Filter') }}
                </button>
            </div>

            {{-- Rating --}}
            <div class="fm-sidebar-card">
                <div class="fm-sidebar-title">{{ __('Rating') }}</div>
                <label class="fm-filter-item" style="cursor:pointer;">
                    <label class="fm-filter-label">
                        <input type="radio" name="fm_rating" class="fm-rating-filter" value="5" style="accent-color:var(--fm-green);">
                        <span style="color:#FFC107;">★★★★★</span>
                    </label>
                </label>
                <label class="fm-filter-item" style="cursor:pointer;">
                    <label class="fm-filter-label">
                        <input type="radio" name="fm_rating" class="fm-rating-filter" value="4" style="accent-color:var(--fm-green);">
                        <span style="color:#FFC107;">★★★★</span><span style="color:#ccc;">★</span>
                    </label>
                </label>
                <label class="fm-filter-item" style="cursor:pointer;">
                    <label class="fm-filter-label">
                        <input type="radio" name="fm_rating" class="fm-rating-filter" value="3" style="accent-color:var(--fm-green);">
                        <span style="color:#FFC107;">★★★</span><span style="color:#ccc;">★★</span>
                    </label>
                </label>
                <div style="margin-top:10px;">
                    <a href="javascript:void(0)" onclick="$('.fm-rating-filter,.fm-cat-checkbox').prop('checked',false);fmFilterRequest();"
                        style="font-size:12px;color:var(--fm-muted,#888);text-decoration:none;">
                        <i class="las la-times-circle"></i> {{ __('Clear All Filters') }}
                    </a>
                </div>
            </div>

        </div>{{-- /sidebar --}}

        {{-- Products --}}
        <div class="col-lg-9">

            {{-- Sort Bar --}}
            <div class="fm-shop-toolbar">
                <span class="fm-result-count showing-results">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong> {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <div class="d-flex align-items-center gap-3">
                    <select class="fm-sort-select" id="fm-sort-select">
                        <option value="3">{{ __('Sort: Latest') }}</option>
                        <option value="1">{{ __('Sort: Name') }}</option>
                        <option value="2">{{ __('Sort: Popular') }}</option>
                        <option value="4">{{ __('Price: Low to High') }}</option>
                        <option value="5">{{ __('Price: High to Low') }}</option>
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
    function fmFilterRequest(page) {
        page = page || null;

        var cats = [];
        $('.fm-cat-checkbox:checked').each(function () { cats.push($(this).data('slug')); });
        var category_slug = cats.join(',') || null;

        var rating    = $('input[name=fm_rating]:checked').val() || null;
        var min_price = $('#fm-min-price').val() || 0;
        var max_price = $('#fm-max-price').val() || 10000;
        var sort      = $('#fm-sort-select').val();
        var search    = $('#fm-search-input').val() || null;

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

    window.fmFilterRequest = fmFilterRequest;

    $(document).on('change', '.fm-cat-checkbox', function () { fmFilterRequest(); });
    $('#fm-sort-select').on('change', function () { fmFilterRequest(); });
    $(document).on('change', '.fm-rating-filter', function () { fmFilterRequest(); });

    $(document).on('keydown', '#fm-search-input', function (e) {
        if (e.key === 'Enter') { fmFilterRequest(); }
    });

        // .add-to-cart-btn handled globally in footer.blade.php
});
</script>
@endsection
