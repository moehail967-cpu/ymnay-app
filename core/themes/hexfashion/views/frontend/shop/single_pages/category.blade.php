@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $category->name !!} @endsection
@section('page-title') {!! $category->name !!} @endsection

@section('content')

{{-- Page Banner --}}
<div class="hf-page-banner">
    <div class="container">
        <h1 class="hf-page-banner-title">{!! $category->name !!}</h1>
        <div class="hf-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="hf-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
            <span class="hf-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <span>{!! $category->name !!}</span>
        </div>
    </div>
</div>

{{-- Shop Layout --}}
<div class="container hf-shop-layout">
    <div class="row g-4">

        {{-- ===== Sidebar ===== --}}
        <div class="col-lg-3">

            {{-- Categories --}}
            @if(isset($categories) && $categories->isNotEmpty())
            <div class="hf-sidebar-card">
                <span class="hf-sidebar-title">{{ __('Categories') }}</span>
                @foreach($categories as $cat)
                <label class="hf-filter-item">
                    <label style="display:flex;align-items:center;gap:10px;margin:0;cursor:pointer;font-size:13px;color:#555;">
                        <input type="checkbox" class="hf-cat-checkbox" data-slug="{{ $cat->slug }}"
                            {{ isset($category) && $category->slug === $cat->slug ? 'checked' : '' }}>
                        {{ $cat->name }}
                    </label>
                    <span class="hf-filter-count">{{ $cat->product_count ?? '' }}</span>
                </label>
                @endforeach
            </div>
            @endif

            {{-- Price Range --}}
            <div class="hf-sidebar-card">
                <span class="hf-sidebar-title">{{ __('Price Range') }}</span>
                <div class="hf-price-inputs">
                    <input type="number" class="hf-price-input" id="hf-min-price" placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" class="hf-price-input" id="hf-max-price" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button onclick="elFilterRequest()" style="margin-top:12px;width:100%;padding:9px 0;background:#E8603C;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:700;cursor:pointer;transition:background .2s;" onmouseover="this.style.background='#c94e2c'" onmouseout="this.style.background='#E8603C'">
                    {{ __('Apply Filter') }}
                </button>
            </div>

            {{-- Rating --}}
            <div class="hf-sidebar-card">
                <span class="hf-sidebar-title">{{ __('Rating') }}</span>
                @foreach([5,4,3,2,1] as $stars)
                <label class="hf-filter-item">
                    <label style="display:flex;align-items:center;gap:10px;margin:0;cursor:pointer;">
                        <input type="radio" name="hf_rating" class="hf-rating-filter" value="{{ $stars }}" style="accent-color:#E8603C;">
                        <span>
                            @for($i=1;$i<=5;$i++)
                                <span style="color:{{ $i<=$stars ? '#f59e0b' : '#ddd' }};font-size:15px;">★</span>
                            @endfor
                        </span>
                    </label>
                </label>
                @endforeach
                <a href="javascript:void(0)" class="hf-clear-link" id="hf-clear-filters">
                    <i class="las la-times-circle"></i> {{ __('Clear All Filters') }}
                </a>
            </div>

            {{-- Sizes --}}
            @if(isset($sizes) && $sizes->isNotEmpty())
            <div class="hf-sidebar-card">
                <span class="hf-sidebar-title">{{ __('Sizes') }}</span>
                <div class="hf-size-list">
                    @foreach($sizes as $size)
                    <div class="hf-size-item">
                        <input type="checkbox" class="hf-size-checkbox" id="cat_size_{{ $size->id }}" data-slug="{{ $size->id }}" data-label="{{ $size->size_code }}">
                        <label for="cat_size_{{ $size->id }}" title="{{ $size->name }}">{{ $size->size_code }}</label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Colors --}}
            @if(isset($colors) && $colors->isNotEmpty())
            <div class="hf-sidebar-card">
                <span class="hf-sidebar-title">{{ __('Colors') }}</span>
                <div class="hf-color-list">
                    @foreach($colors as $color)
                    <div class="hf-color-item" title="{{ $color->name }}">
                        <input type="checkbox" class="hf-color-checkbox" id="cat_color_{{ $color->id }}" data-slug="{{ $color->id }}">
                        <label for="cat_color_{{ $color->id }}" style="background-color:{{ $color->color_code }};{{ strtolower($color->color_code)=='#fff'||strtolower($color->color_code)=='#ffffff' ? 'border-color:#ddd!important;' : '' }}"></label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Tags --}}
            @if(isset($tags) && $tags->isNotEmpty())
            <div class="hf-sidebar-card">
                <span class="hf-sidebar-title">{{ __('Tags') }}</span>
                <div class="hf-tag-list">
                    @foreach($tags as $tag)
                    <button class="hf-tag-btn" data-slug="{{ $tag->tag_name }}" onclick="elToggleTag(this)">{{ $tag->tag_name }}</button>
                    @endforeach
                </div>
            </div>
            @endif

        </div>{{-- /sidebar --}}

        {{-- ===== Products ===== --}}
        <div class="col-lg-9">

            {{-- Topbar --}}
            <div class="hf-shop-topbar">
                <span class="hf-topbar-count showing-results">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <select class="hf-sort-select" id="hf-sort-select">
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
        $('.hf-cat-checkbox:checked').each(function () { cats.push($(this).data('slug')); });

        var sizes = [];
        $('.hf-size-checkbox:checked').each(function () { sizes.push($(this).data('slug')); });

        var colors = [];
        $('.hf-color-checkbox:checked').each(function () { colors.push($(this).data('slug')); });

        var tag = null;
        var activeTag = $('.hf-tag-btn.active');
        if (activeTag.length) { tag = activeTag.data('slug'); }

        $.ajax({
            type: 'GET',
            url: '{{ theme_shop_filter_url() }}',
            data: {
                category:  cats.join(',') || '{{ $currentCatSlug }}',
                size:      sizes.join(',') || null,
                color:     colors.join(',') || null,
                rating:    $('input[name=hf_rating]:checked').val() || null,
                min_price: $('#hf-min-price').val() || 0,
                max_price: $('#hf-max-price').val() || 10000,
                sort:      $('#hf-sort-select').val(),
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

    $(document).on('change', '.hf-cat-checkbox, .hf-size-checkbox, .hf-color-checkbox', function () { elFilterRequest(); });
    $(document).on('change', '.hf-rating-filter', function () { elFilterRequest(); });
    $('#hf-sort-select').on('change', function () { elFilterRequest(); });

    $(document).on('click', '#hf-clear-filters', function () {
        $('.hf-cat-checkbox').prop('checked', false);
        // Re-check current category
        $('.hf-cat-checkbox[data-slug="{{ $currentCatSlug }}"]').prop('checked', true);
        $('.hf-size-checkbox, .hf-color-checkbox').prop('checked', false);
        $('input[name=hf_rating]').prop('checked', false);
        $('.hf-tag-btn').removeClass('active');
        $('#hf-min-price').val(0);
        $('#hf-max-price').val(10000);
        elFilterRequest();
    });
});

function elToggleTag(el) {
    var wasActive = $(el).hasClass('active');
    $('.hf-tag-btn').removeClass('active');
    if (!wasActive) $(el).addClass('active');
    elFilterRequest();
}
</script>
@endsection
