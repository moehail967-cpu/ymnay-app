@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title !!} @endsection
@section('page-title') {!! $page_post->title !!} @endsection

@section('content')

{{-- Page Banner --}}
<div class="mc-page-banner">
    <div class="container">
        <h1 class="mc-page-banner-title">{!! $page_post->title !!}</h1>
        <div class="mc-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="mc-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <span>{!! $page_post->title !!}</span>
        </div>
    </div>
</div>

{{-- Shop Layout --}}
<div class="container mc-shop-layout">
    <div class="row g-4">

        {{-- ===== Sidebar ===== --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div class="mc-sidebar-card">
                <span class="mc-sidebar-title">{{ __('Search') }}</span>
                <div class="mc-search-bar">
                    <input type="text" id="mc-search-input" placeholder="{{ __('Search products…') }}">
                    <button type="button" onclick="elFilterRequest()"><i class="las la-search"></i></button>
                </div>
            </div>

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div class="mc-sidebar-card">
                <span class="mc-sidebar-title">{{ __('Categories') }}</span>
                @foreach($categories as $cat)
                <label class="mc-filter-item">
                    <label style="display:flex;align-items:center;gap:10px;margin:0;cursor:pointer;font-size:13px;color:#555;">
                        <input type="checkbox" class="mc-cat-checkbox" data-slug="{{ $cat->slug }}">
                        {{ $cat->name }}
                    </label>
                    <span class="mc-filter-count">{{ $cat->product_count ?? '' }}</span>
                </label>
                @endforeach
            </div>
            @endif

            {{-- Price Range --}}
            <div class="mc-sidebar-card">
                <span class="mc-sidebar-title">{{ __('Price Range') }}</span>
                <div class="mc-price-inputs">
                    <input type="number" class="mc-price-input" id="mc-min-price" placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" class="mc-price-input" id="mc-max-price" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button onclick="elFilterRequest()" style="margin-top:12px;width:100%;padding:9px 0;background:#1A85ED;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:700;cursor:pointer;transition:background .2s;" onmouseover="this.style.background='#1266B8'" onmouseout="this.style.background='#1A85ED'">
                    {{ __('Apply Filter') }}
                </button>
            </div>

            {{-- Rating --}}
            <div class="mc-sidebar-card">
                <span class="mc-sidebar-title">{{ __('Rating') }}</span>
                @foreach([5,4,3,2,1] as $stars)
                <label class="mc-filter-item">
                    <label style="display:flex;align-items:center;gap:10px;margin:0;cursor:pointer;">
                        <input type="radio" name="el_rating" class="mc-rating-filter" value="{{ $stars }}" style="accent-color:#1A85ED;">
                        <span>
                            @for($i=1;$i<=5;$i++)
                                <span style="color:{{ $i<=$stars ? '#326DE4' : '#ddd' }};font-size:15px;">★</span>
                            @endfor
                        </span>
                    </label>
                </label>
                @endforeach
                <a href="javascript:void(0)" class="mc-clear-link" id="mc-clear-filters">
                    <i class="las la-times-circle"></i> {{ __('Clear All Filters') }}
                </a>
            </div>

            {{-- Sizes --}}
            @if($sizes->isNotEmpty())
            <div class="mc-sidebar-card">
                <span class="mc-sidebar-title">{{ __('Sizes') }}</span>
                <div class="mc-size-list">
                    @foreach($sizes as $size)
                    <div class="mc-size-item">
                        <input type="checkbox" class="mc-size-checkbox" id="size_{{ $size->id }}" data-slug="{{ $size->id }}" data-label="{{ $size->size_code }}">
                        <label for="size_{{ $size->id }}" title="{{ $size->name }}">{{ $size->size_code }}</label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Colors --}}
            @if($colors->isNotEmpty())
            <div class="mc-sidebar-card">
                <span class="mc-sidebar-title">{{ __('Colors') }}</span>
                <div class="mc-color-list">
                    @foreach($colors as $color)
                    <div class="mc-color-item" title="{{ $color->name }}">
                        <input type="checkbox" class="mc-color-checkbox" id="color_{{ $color->id }}" data-slug="{{ $color->id }}">
                        <label for="color_{{ $color->id }}" style="background-color:{{ $color->color_code }};{{ strtolower($color->color_code)=='#fff'||strtolower($color->color_code)=='#ffffff' ? 'border-color:#ddd!important;' : '' }}"></label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Tags --}}
            @if($tags->isNotEmpty())
            <div class="mc-sidebar-card">
                <span class="mc-sidebar-title">{{ __('Tags') }}</span>
                <div class="mc-tag-list">
                    @foreach($tags as $tag)
                    <button class="mc-tag-btn" data-slug="{{ $tag->tag_name }}" onclick="elToggleTag(this)">{{ $tag->tag_name }}</button>
                    @endforeach
                </div>
            </div>
            @endif

        </div>{{-- /sidebar --}}

        {{-- ===== Products ===== --}}
        <div class="col-lg-9">

            {{-- Topbar --}}
            <div class="mc-shop-topbar">
                <span class="mc-topbar-count showing-results">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <select class="mc-sort-select" id="mc-sort-select">
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

    function elFilterRequest(page) {
        page = page || null;

        var cats = [];
        $('.mc-cat-checkbox:checked').each(function () { cats.push($(this).data('slug')); });

        var sizes = [];
        $('.mc-size-checkbox:checked').each(function () { sizes.push($(this).data('slug')); });

        var colors = [];
        $('.mc-color-checkbox:checked').each(function () { colors.push($(this).data('slug')); });

        var tag = null;
        var activeTag = $('.mc-tag-btn.active');
        if (activeTag.length) { tag = activeTag.data('slug'); }

        $.ajax({
            type: 'GET',
            url: '{{ theme_shop_filter_url() }}',
            data: {
                category:  cats.join(',') || null,
                size:      sizes.join(',') || null,
                color:     colors.join(',') || null,
                rating:    $('input[name=el_rating]:checked').val() || null,
                min_price: $('#mc-min-price').val() || 0,
                max_price: $('#mc-max-price').val() || 10000,
                sort:      $('#mc-sort-select').val(),
                page:      page,
                search:    $('#mc-search-input').val() || null,
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

    $(document).on('change', '.mc-cat-checkbox, .mc-size-checkbox, .mc-color-checkbox', function () { elFilterRequest(); });
    $(document).on('change', '.mc-rating-filter', function () { elFilterRequest(); });
    $('#mc-sort-select').on('change', function () { elFilterRequest(); });
    $(document).on('keydown', '#mc-search-input', function (e) { if (e.key === 'Enter') elFilterRequest(); });

    $(document).on('click', '#mc-clear-filters', function () {
        $('.mc-cat-checkbox, .mc-size-checkbox, .mc-color-checkbox').prop('checked', false);
        $('input[name=el_rating]').prop('checked', false);
        $('.mc-tag-btn').removeClass('active');
        $('#mc-min-price').val(0);
        $('#mc-max-price').val(10000);
        $('#mc-search-input').val('');
        elFilterRequest();
    });
});

function elToggleTag(el) {
    var wasActive = $(el).hasClass('active');
    $('.mc-tag-btn').removeClass('active');
    if (!wasActive) $(el).addClass('active');
    elFilterRequest();
}
</script>
@endsection
