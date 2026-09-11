@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $category->name !!} @endsection
@section('page-title') {!! $category->name !!} @endsection

@section('style')
<style>
.ms-page-banner {
    background: var(--ms-warm);
    border-bottom: 1px solid var(--ms-border);
    padding: 40px 0 28px;
}
.ms-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: var(--ms-muted);
    margin-top: 8px;
    letter-spacing: .04em;
}
.ms-breadcrumb a { color: var(--ms-linen-d); text-decoration: none; font-weight: 600; }
.ms-sidebar-card {
    background: var(--ms-cream);
    border: 1px solid var(--ms-border);
    border-radius: var(--ms-radius);
    padding: 22px;
    margin-bottom: 20px;
}
.ms-sidebar-title {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--ms-charcoal);
    margin-bottom: 16px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--ms-border);
}
.ms-filter-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px 0;
    border-bottom: 1px dashed var(--ms-border);
}
.ms-filter-item:last-child { border-bottom: none; }
.ms-cat-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--ms-charcoal);
    cursor: pointer;
    font-weight: 500;
}
.ms-filter-count {
    font-size: 11px;
    color: var(--ms-muted);
    background: var(--ms-surface);
    padding: 2px 8px;
    border-radius: 20px;
}
.ms-price-inputs {
    display: flex;
    gap: 10px;
    margin-bottom: 14px;
}
.ms-price-inputs input {
    flex: 1;
    padding: 8px 10px;
    border: 1px solid var(--ms-border);
    border-radius: var(--ms-radius);
    font-size: 13px;
    background: #fff;
    color: var(--ms-dark);
    outline: none;
    font-family: inherit;
}
.ms-btn-apply {
    display: block;
    width: 100%;
    padding: 9px;
    background: var(--ms-linen);
    color: #fff;
    border: none;
    border-radius: var(--ms-radius);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    cursor: pointer;
    transition: background .2s;
}
.ms-btn-apply:hover { background: var(--ms-linen-d); }
.ms-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 10px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--ms-border);
}
.ms-toolbar-count {
    font-size: 13px;
    color: var(--ms-muted);
}
.ms-toolbar-count strong { color: var(--ms-dark); font-weight: 700; }
.ms-sort-select {
    padding: 8px 32px 8px 12px;
    border: 1px solid var(--ms-border);
    border-radius: var(--ms-radius);
    font-size: 12px;
    color: var(--ms-charcoal);
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%238A8070'/%3E%3C/svg%3E") no-repeat right 12px center;
    appearance: none;
    cursor: pointer;
    outline: none;
    font-family: inherit;
    letter-spacing: .02em;
}
.ms-rating-row {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 5px 0;
    font-size: 15px;
}
</style>
@endsection

@section('content')
{{-- Page Banner --}}
<div class="ms-page-banner">
    <div class="container">
        <h1 style="font-size:13px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:6px;">{{ __('Our Collection') }}</h1>
        <h2 style="font-size:30px;font-weight:300;color:var(--ms-dark);margin:0;letter-spacing:-.01em;">{!! $category->name !!}</h2>
        <div class="ms-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;color:var(--ms-border);"></i>
            <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;color:var(--ms-border);"></i>
            <span>{!! $category->name !!}</span>
        </div>
    </div>
</div>

{{-- Shop Layout --}}
<div class="container" style="padding: 48px 0 80px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div class="ms-sidebar-card">
                <div class="ms-sidebar-title">{{ __('Search') }}</div>
                <div style="display:flex;border:1px solid var(--ms-border);border-radius:var(--ms-radius);overflow:hidden;">
                    <input type="text" id="ms-search-input" placeholder="{{ __('Search products…') }}"
                        style="flex:1;border:none;outline:none;padding:9px 12px;font-size:13px;background:#fff;font-family:inherit;color:var(--ms-dark);">
                    <button type="button" onclick="msFilterRequest()"
                        style="background:var(--ms-linen);color:#fff;border:none;padding:0 14px;cursor:pointer;">
                        <i class="las la-search"></i>
                    </button>
                </div>
            </div>

            @if(isset($categories) && $categories->isNotEmpty())
            <div class="ms-sidebar-card">
                <div class="ms-sidebar-title">{{ __('Categories') }}</div>
                <ul style="list-style:none;margin:0;padding:0;">
                    @foreach($categories as $cat)
                    <li class="ms-filter-item">
                        <label class="ms-cat-label">
                            <input type="checkbox" class="ms-cat-checkbox-input" data-slug="{{ $cat->slug }}" data-value="{{ $cat->name }}"
                                   style="accent-color:var(--ms-linen-d);width:14px;height:14px;cursor:pointer;"
                                   {{ ($cat->slug === ($category->slug ?? '')) ? 'checked' : '' }}>
                            {{ $cat->name }}
                        </label>
                        <span class="ms-filter-count">{{ $cat->product_count ?? '' }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="ms-sidebar-card">
                <div class="ms-sidebar-title">{{ __('Price Range') }}</div>
                <div class="ms-price-inputs">
                    <input type="number" id="ms-min-price" placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" id="ms-max-price" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button class="ms-btn-apply" onclick="msFilterRequest()">{{ __('Apply Filter') }}</button>
            </div>

            <div class="ms-sidebar-card">
                <div class="ms-sidebar-title">{{ __('Rating') }}</div>
                <div class="ms-rating-row">
                    <input type="radio" name="ms_rating" class="ms-rating-filter" value="5" style="accent-color:var(--ms-linen-d);">
                    <span style="color:#C8A84B;font-size:13px;">★★★★★</span>
                </div>
                <div class="ms-rating-row">
                    <input type="radio" name="ms_rating" class="ms-rating-filter" value="4" style="accent-color:var(--ms-linen-d);">
                    <span style="color:#C8A84B;font-size:13px;">★★★★</span><span style="color:var(--ms-border);font-size:13px;">★</span>
                </div>
                <div class="ms-rating-row">
                    <input type="radio" name="ms_rating" class="ms-rating-filter" value="3" style="accent-color:var(--ms-linen-d);">
                    <span style="color:#C8A84B;font-size:13px;">★★★</span><span style="color:var(--ms-border);font-size:13px;">★★</span>
                </div>
                <div style="margin-top:10px;">
                    <a href="javascript:void(0)" onclick="$('.ms-rating-filter,.ms-cat-checkbox-input').prop('checked',false);msFilterRequest();"
                        style="font-size:12px;color:var(--ms-muted);text-decoration:none;letter-spacing:.04em;">
                        <i class="las la-times-circle"></i> {{ __('Clear Filters') }}
                    </a>
                </div>
            </div>

        </div>

        {{-- Products --}}
        <div class="col-lg-9">

            <div class="ms-toolbar">
                <span class="ms-toolbar-count showing-results">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong> {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <select class="ms-sort-select" id="ms-sort-select">
                    <option value="3">{{ __('Latest') }}</option>
                    <option value="1">{{ __('Name A–Z') }}</option>
                    <option value="2">{{ __('Most Popular') }}</option>
                    <option value="4">{{ __('Price: Low to High') }}</option>
                    <option value="5">{{ __('Price: High to Low') }}</option>
                </select>
            </div>

            <div class="row g-3 grid-product-list">
                @include(include_theme_path('shop.partials.product-partials.grid-products'))
            </div>

        </div>
    </div>
</div>

<div id="product-modal"></div>
@endsection

@section('scripts')
<script>
$(function () {
    function msFilterRequest(page) {
        page = page || null;

        var cats = [];
        $('.ms-cat-checkbox-input:checked').each(function () { cats.push($(this).data('slug')); });
        var category_slug = cats.join(',') || null;

        var rating    = $('input[name=ms_rating]:checked').val() || null;
        var min_price = $('#ms-min-price').val() || 0;
        var max_price = $('#ms-max-price').val() || 10000;
        var sort      = $('#ms-sort-select').val();
        var search    = $('#ms-search-input').val() || null;

        var reqData = { min_price: min_price, max_price: max_price, sort: sort };
        if (category_slug) reqData.category = category_slug;
        if (rating)  reqData.rating  = rating;
        if (page)    reqData.page    = page;
        if (search)  reqData.search  = search;

        $.ajax({
            type: 'GET',
            url: '{{ theme_shop_filter_url() }}',
            data: reqData,
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                $('.grid-product-list').html(data.grid);
                var p = data.pagination;
                $('.showing-results').html('{{ __('Showing') }} <strong>' + (p.to ? p.from + '-' + p.to : 0) + '</strong> {{ __('of') }} <strong>' + (p.total || 0) + '</strong> {{ __('results') }}');
                setTimeout(function () { $('.loader').hide(); }, 400);
            },
            error: function () { $('.loader').hide(); }
        });
    }

    window.msFilterRequest = msFilterRequest;

    $(document).on('change', '.ms-cat-checkbox-input', function () { msFilterRequest(); });
    $('#ms-sort-select').on('change', function () { msFilterRequest(); });
    $(document).on('change', '.ms-rating-filter', function () { msFilterRequest(); });

    $(document).on('keydown', '#ms-search-input', function (e) {
        if (e.key === 'Enter') { msFilterRequest(); }
    });

        // .add-to-cart-btn handled globally in footer.blade.php
});
</script>
@endsection
