@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $category->name !!} @endsection
@section('page-title') {!! $category->name !!} @endsection

@section('content')

{{-- Page Banner --}}
<div class="el-page-banner">
    <div class="container">
        <h1 class="el-page-banner-title">{!! $category->name !!}</h1>
        <div class="el-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="el-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
            <span class="el-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <span>{!! $category->name !!}</span>
        </div>
    </div>
</div>

{{-- Shop Layout --}}
<div class="container el-shop-layout">
    <div class="row g-4">

        {{-- ===== Sidebar ===== --}}
        <div class="col-lg-3">

            {{-- Categories --}}
            @if(isset($categories) && $categories->isNotEmpty())
            <div class="el-sidebar-card">
                <span class="el-sidebar-title">{{ __('Categories') }}</span>
                @foreach($categories as $cat)
                <label class="el-filter-item">
                    <label style="display:flex;align-items:center;gap:10px;margin:0;cursor:pointer;font-size:13px;color:#555;">
                        <input type="checkbox" class="el-cat-checkbox" data-slug="{{ $cat->slug }}"
                            {{ isset($category) && $category->slug === $cat->slug ? 'checked' : '' }}>
                        {{ $cat->name }}
                    </label>
                    <span class="el-filter-count">{{ $cat->product_count ?? '' }}</span>
                </label>
                @endforeach
            </div>
            @endif

            {{-- Price Range --}}
            <div class="el-sidebar-card">
                <span class="el-sidebar-title">{{ __('Price Range') }}</span>
                <div class="el-price-inputs">
                    <input type="number" class="el-price-input" id="el-min-price" placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" class="el-price-input" id="el-max-price" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button onclick="elFilterRequest()" style="margin-top:12px;width:100%;padding:9px 0;background:#E8603C;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:700;cursor:pointer;transition:background .2s;" onmouseover="this.style.background='#c94e2c'" onmouseout="this.style.background='#E8603C'">
                    {{ __('Apply Filter') }}
                </button>
            </div>

            {{-- Rating --}}
            <div class="el-sidebar-card">
                <span class="el-sidebar-title">{{ __('Rating') }}</span>
                @foreach([5,4,3,2,1] as $stars)
                <label class="el-filter-item">
                    <label style="display:flex;align-items:center;gap:10px;margin:0;cursor:pointer;">
                        <input type="radio" name="el_rating" class="el-rating-filter" value="{{ $stars }}" style="accent-color:#E8603C;">
                        <span>
                            @for($i=1;$i<=5;$i++)
                                <span style="color:{{ $i<=$stars ? '#f59e0b' : '#ddd' }};font-size:15px;">★</span>
                            @endfor
                        </span>
                    </label>
                </label>
                @endforeach
                <a href="javascript:void(0)" class="el-clear-link" id="el-clear-filters">
                    <i class="las la-times-circle"></i> {{ __('Clear All Filters') }}
                </a>
            </div>

            {{-- Sizes --}}
            @if(isset($sizes) && $sizes->isNotEmpty())
            <div class="el-sidebar-card">
                <span class="el-sidebar-title">{{ __('Sizes') }}</span>
                <div class="el-size-list">
                    @foreach($sizes as $size)
                    <div class="el-size-item">
                        <input type="checkbox" class="el-size-checkbox" id="cat_size_{{ $size->id }}" data-slug="{{ $size->id }}" data-label="{{ $size->size_code }}">
                        <label for="cat_size_{{ $size->id }}" title="{{ $size->name }}">{{ $size->size_code }}</label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Colors --}}
            @if(isset($colors) && $colors->isNotEmpty())
            <div class="el-sidebar-card">
                <span class="el-sidebar-title">{{ __('Colors') }}</span>
                <div class="el-color-list">
                    @foreach($colors as $color)
                    <div class="el-color-item" title="{{ $color->name }}">
                        <input type="checkbox" class="el-color-checkbox" id="cat_color_{{ $color->id }}" data-slug="{{ $color->id }}">
                        <label for="cat_color_{{ $color->id }}" style="background-color:{{ $color->color_code }};{{ strtolower($color->color_code)=='#fff'||strtolower($color->color_code)=='#ffffff' ? 'border-color:#ddd!important;' : '' }}"></label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Tags --}}
            @if(isset($tags) && $tags->isNotEmpty())
            <div class="el-sidebar-card">
                <span class="el-sidebar-title">{{ __('Tags') }}</span>
                <div class="el-tag-list">
                    @foreach($tags as $tag)
                    <button class="el-tag-btn" data-slug="{{ $tag->tag_name }}" onclick="elToggleTag(this)">{{ $tag->tag_name }}</button>
                    @endforeach
                </div>
            </div>
            @endif

        </div>{{-- /sidebar --}}

        {{-- ===== Products ===== --}}
        <div class="col-lg-9">

            {{-- Topbar --}}
            <div class="el-shop-topbar">
                <span class="el-topbar-count showing-results">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <select class="el-sort-select" id="el-sort-select">
                    <option value="3">{{ __('Sort: Latest') }}</option>
                    <option value="1">{{ __('Sort: Name') }}</option>
                    <option value="2">{{ __('Sort: Popular') }}</option>
                    <option value="4">{{ __('Price: Low → High') }}</option>
                    <option value="5">{{ __('Price: High → Low') }}</option>
                </select>
            </div>

            {{-- Product Grid --}}
            <div class="grid-product-list">
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

    @php $currentCatSlug = $category->slug ?? ''; @endphp

    function elFilterRequest(page) {
        page = page || null;

        var cats = [];
        $('.el-cat-checkbox:checked').each(function () { cats.push($(this).data('slug')); });

        var sizes = [];
        $('.el-size-checkbox:checked').each(function () { sizes.push($(this).data('slug')); });

        var colors = [];
        $('.el-color-checkbox:checked').each(function () { colors.push($(this).data('slug')); });

        var tag = null;
        var activeTag = $('.el-tag-btn.active');
        if (activeTag.length) { tag = activeTag.data('slug'); }

        $.ajax({
            type: 'GET',
            url: '{{ theme_shop_filter_url() }}',
            data: {
                category:  cats.join(',') || '{{ $currentCatSlug }}',
                size:      sizes.join(',') || null,
                color:     colors.join(',') || null,
                rating:    $('input[name=el_rating]:checked').val() || null,
                min_price: $('#el-min-price').val() || 0,
                max_price: $('#el-max-price').val() || 10000,
                sort:      $('#el-sort-select').val(),
                page:      page,
                tag:       tag
            },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                $('.grid-product-list').html(data.grid);
                var p = data.pagination;
                $('.showing-results').html(
                    '{{ __("Showing") }} <strong>' + (p.to ? p.from + '–' + p.to : 0) + '</strong> {{ __("of") }} <strong>' + (p.total || 0) + '</strong> {{ __("results") }}'
                );
                setTimeout(function () { $('.loader').hide(); }, 400);
            },
            error: function () { $('.loader').hide(); }
        });
    }

    window.elFilterRequest = elFilterRequest;

    $(document).on('change', '.el-cat-checkbox, .el-size-checkbox, .el-color-checkbox', function () { elFilterRequest(); });
    $(document).on('change', '.el-rating-filter', function () { elFilterRequest(); });
    $('#el-sort-select').on('change', function () { elFilterRequest(); });

    $(document).on('click', '#el-clear-filters', function () {
        $('.el-cat-checkbox').prop('checked', false);
        // Re-check current category
        $('.el-cat-checkbox[data-slug="{{ $currentCatSlug }}"]').prop('checked', true);
        $('.el-size-checkbox, .el-color-checkbox').prop('checked', false);
        $('input[name=el_rating]').prop('checked', false);
        $('.el-tag-btn').removeClass('active');
        $('#el-min-price').val(0);
        $('#el-max-price').val(10000);
        elFilterRequest();
    });
});

function elToggleTag(el) {
    var wasActive = $(el).hasClass('active');
    $('.el-tag-btn').removeClass('active');
    if (!wasActive) $(el).addClass('active');
    elFilterRequest();
}
</script>
@endsection
