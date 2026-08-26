@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $category->name !!} @endsection
@section('page-title') {!! $category->name !!} @endsection

@section('content')
<div class="tn-page-banner">
    <div class="container tn-page-banner-content">
        <h1>{!! $category->name !!}</h1>
        <div class="tn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
            <span class="sep">/</span>
            <span class="current">{!! $category->name !!}</span>
        </div>
    </div>
</div>

<div class="container tn-page-wrap">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div class="tn-sidebar-card">
                <div class="tn-sidebar-title">{{ __('Search') }}</div>
                <div style="display:flex;border:1.5px solid var(--tn-border,#e0e0e0);border-radius:var(--tn-radius,6px);overflow:hidden;">
                    <input type="text" id="tn-search-input" placeholder="{{ __('Search products…') }}"
                        style="flex:1;border:none;outline:none;padding:9px 12px;font-size:13px;background:transparent;">
                    <button type="button" onclick="window.tnFilterRequest()"
                        style="background:var(--tn-primary,#4a7c6f);color:#fff;border:none;padding:0 14px;cursor:pointer;">
                        <i class="las la-search"></i>
                    </button>
                </div>
            </div>

            {{-- Categories --}}
            @php $sidebar_categories = $categories ?? collect(); @endphp
            @if($sidebar_categories->isNotEmpty())
            <div class="tn-sidebar-card">
                <div class="tn-sidebar-title">{{ __('Categories') }}</div>
                @foreach($sidebar_categories as $cat)
                <label class="tn-filter-item">
                    <input type="checkbox" class="tn-filter-chk tn-hidden-input" data-slug="{{ $cat->slug }}"
                        {{ ($cat->slug === ($category->slug ?? '')) ? 'checked' : '' }}>
                    <span class="tn-filter-label">{{ $cat->name }}</span>
                    <span class="tn-filter-count">{{ $cat->product_count ?? '' }}</span>
                </label>
                @endforeach
            </div>
            @endif

            {{-- Price Range --}}
            <div class="tn-sidebar-card">
                <div class="tn-sidebar-title">{{ __('Price Range') }}</div>
                <div class="d-flex gap-2 align-items-center">
                    <input type="number" id="price_from" class="tn-price-input" placeholder="{{ __('Min') }}">
                    <span class="tn-range-sep">–</span>
                    <input type="number" id="price_to" class="tn-price-input" placeholder="{{ __('Max') }}">
                </div>
                <button class="tn-btn tn-btn-primary w-100 mt-3" id="tn_filter_price_btn">
                    {{ __('Apply') }}
                </button>
            </div>

            {{-- Rating --}}
            <div class="tn-sidebar-card">
                <div class="tn-sidebar-title">{{ __('Rating') }}</div>
                @foreach([5,4,3,2,1] as $star)
                <label class="tn-filter-item">
                    <input type="radio" name="rating_filter" value="{{ $star }}" class="tn-filter-chk tn-hidden-input">
                    <span class="tn-filter-label">
                        @for($i=1;$i<=5;$i++)
                            <i class="las {{ $i<=$star ? 'la-star tn-star' : 'la-star-half-alt tn-star-empty' }}"></i>
                        @endfor
                        {{ $star }}+
                    </span>
                </label>
                @endforeach
            </div>

            <div style="text-align:center;margin-top:4px;">
                <a href="javascript:void(0)"
                    onclick="$('.tn-filter-chk').prop('checked',false);$('#price_from,#price_to').val('');window.tnFilterRequest();"
                    style="font-size:12px;color:var(--tn-muted,#888);text-decoration:none;">
                    <i class="las la-times-circle"></i> {{ __('Clear Filters') }}
                </a>
            </div>

        </div>

        {{-- Main --}}
        <div class="col-lg-9">

            {{-- Sort bar --}}
            <div class="tn-sort-bar">
                <span class="tn-sort-count" id="tn_product_count">
                    {{ $pagination->from ?? 0 }}–{{ $pagination->to ?? 0 }} {{ __('of') }} {{ $pagination->total() }} {{ __('Products') }}
                </span>
                <div class="d-flex gap-2 align-items-center">
                    <label class="tn-sort-label">{{ __('Sort by') }}:</label>
                    <select class="tn-select-sort" id="tn_sort_select">
                        <option value="latest">{{ __('Latest') }}</option>
                        <option value="price_low">{{ __('Price: Low to High') }}</option>
                        <option value="price_high">{{ __('Price: High to Low') }}</option>
                        <option value="top_rated">{{ __('Top Rated') }}</option>
                    </select>
                </div>
            </div>

            <div id="tn_product_grid">
                @include(include_theme_path('shop.partials.product-partials.grid-products'))
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function ($) {
    window.tnFilterRequest = function (page) {
        page = page || 1;
        var slugs = [];
        $('.tn-filter-chk[data-slug]:checked').each(function () { slugs.push($(this).data('slug')); });
        var rating    = $('input[name="rating_filter"]:checked').val() || '';
        var min_price = $('#price_from').val();
        var max_price = $('#price_to').val();
        var search    = $('#tn-search-input').val() || '';
        var sortMap   = { latest: 3, price_low: 4, price_high: 5, top_rated: 2 };
        var sort      = sortMap[$('#tn_sort_select').val()] || 3;

        var data = { sort: sort, page: page };
        if (slugs.length)  data.category  = slugs.join(',');
        if (rating)        data.rating     = rating;
        if (min_price)     data.min_price  = min_price;
        if (max_price)     data.max_price  = max_price;
        if (search)        data.search     = search;

        $.ajax({
            url: '{{ theme_shop_filter_url() }}',
            method: 'GET',
            data: data,
            beforeSend: function () { $('#tn_product_grid').css('opacity', '.5'); },
            success: function (res) {
                $('#tn_product_grid').html(res.grid).css('opacity', '1');
                var p = res.pagination;
                if (p) $('#tn_product_count').text((p.from ?? 0) + '–' + (p.to ?? 0) + ' {{ __('of') }} ' + (p.total ?? 0) + ' {{ __('Products') }}');
            },
            error: function () { $('#tn_product_grid').css('opacity', '1'); }
        });
    };

    $(document).on('change', '.tn-filter-chk', function () { window.tnFilterRequest(); });
    $(document).on('click',  '#tn_filter_price_btn', function () { window.tnFilterRequest(); });
    $(document).on('change', '#tn_sort_select', function () { window.tnFilterRequest(); });
    $(document).on('keydown', '#tn-search-input', function (e) {
        if (e.key === 'Enter') { window.tnFilterRequest(); }
    });
})(jQuery);
</script>
@endsection
