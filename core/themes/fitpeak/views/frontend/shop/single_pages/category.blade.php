@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $category->name !!} @endsection
@section('page-title') {!! $category->name !!} @endsection

@section('content')

{{-- Page Banner --}}
<div class="fp-page-hero">
    <div class="container">
        <h1 class="fp-page-title">{!! $category->name !!}</h1>
        <ul class="fp-breadcrumb-list">
            <li><a href="{{ theme_home_url() }}">{{ __('Home') }}</a></li>
            <li><a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a></li>
            <li>{!! $category->name !!}</li>
        </ul>
    </div>
</div>



{{-- Shop Layout --}}
<div class="container" style="padding: 36px 0 72px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div class="fp-sidebar-card">
                <div class="fp-sidebar-title">{{ __('Search') }}</div>
                <div style="display:flex;border:1.5px solid var(--fp-border,#e0e0e0);border-radius:6px;overflow:hidden;">
                    <input type="text" id="fp-search-input" placeholder="{{ __('Search products…') }}"
                        style="flex:1;border:none;outline:none;padding:9px 12px;font-size:13px;background:transparent;">
                    <button type="button" onclick="fpFilterRequest()"
                        style="background:var(--fp-green,#28a745);color:#fff;border:none;padding:0 14px;cursor:pointer;">
                        <i class="las la-search"></i>
                    </button>
                </div>
            </div>

            {{-- Categories --}}
            @if(isset($categories) && $categories->isNotEmpty())
            <div class="fp-sidebar-card">
                <div class="fp-sidebar-title">{{ __('Categories') }}</div>
                <ul class="category-lists active-list list-unstyled mb-0">
                    @foreach($categories as $cat)
                    <label class="fp-filter-item">
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                            <input type="checkbox" class="fp-cat-checkbox" data-slug="{{ $cat->slug }}" data-value="{{ $cat->name }}"
                                style="accent-color:var(--fp-green);"
                                {{ ($cat->slug === ($category->slug ?? '')) ? 'checked' : '' }}>
                            {{ $cat->name }}
                        </label>
                        <span class="fp-filter-count">{{ $cat->product_count ?? '' }}</span>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Price Range --}}
            <div class="fp-sidebar-card">
                <div class="fp-sidebar-title">{{ __('Price Range') }}</div>
                <div class="fp-price-inputs">
                    <input type="number" class="fp-price-input" id="fp-min-price" placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" class="fp-price-input" id="fp-max-price" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button class="fp-btn fp-btn-primary fp-btn-sm mt-3 w-100" onclick="fpFilterRequest()">
                    {{ __('Apply Filter') }}
                </button>
            </div>

            {{-- Rating --}}
            <div class="fp-sidebar-card">
                <div class="fp-sidebar-title">{{ __('Rating') }}</div>
                <label class="fp-filter-item">
                    <input type="radio" name="fp_rating" class="fp-rating-filter" value="5" style="accent-color:var(--fp-green);">
                    <span class="fp-stars">★★★★★</span>
                </label>
                <label class="fp-filter-item">
                    <input type="radio" name="fp_rating" class="fp-rating-filter" value="4" style="accent-color:var(--fp-green);">
                    <span class="fp-stars">★★★★</span><span style="color:#555;">★</span>
                </label>
                <label class="fp-filter-item">
                    <input type="radio" name="fp_rating" class="fp-rating-filter" value="3" style="accent-color:var(--fp-green);">
                    <span class="fp-stars">★★★</span><span style="color:#555;">★★</span>
                </label>
                <div style="margin-top:10px;">
                    <a href="javascript:void(0)" onclick="$('.fp-rating-filter,.fp-cat-checkbox').prop('checked',false);fpFilterRequest();"
                        style="font-size:12px;color:var(--fp-muted,#888);text-decoration:none;">
                        <i class="las la-times-circle"></i> {{ __('Clear All Filters') }}
                    </a>
                </div>
            </div>

        </div>{{-- /sidebar --}}

        {{-- Products --}}
        <div class="col-lg-9">

            {{-- Sort Bar --}}
            <div class="fp-sort-bar">
                <span class="showing-results" style="color:var(--fp-muted);font-size:13px;">
                    {{ __('Showing') }} <strong style="color:var(--fp-green);">{{ $pagination->count() }}</strong> {{ __('of') }} <strong style="color:var(--fp-green);">{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <div class="d-flex align-items-center gap-3">
                    <select class="fp-select" id="fp-sort-select">
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

{{-- Quick View Modal container --}}
<div id="product-modal"></div>
@endsection

@section('scripts')
<script>
$(function () {
    function fpFilterRequest(page) {
        page = page || null;

        var cats = [];
        $('.fp-cat-checkbox:checked').each(function () { cats.push($(this).data('slug')); });
        var category_slug = cats.join(',') || null;

        var rating    = $('input[name=fp_rating]:checked').val() || null;
        var min_price = $('#fp-min-price').val() || 0;
        var max_price = $('#fp-max-price').val() || 10000;
        var sort      = $('#fp-sort-select').val();
        var search    = $('#fp-search-input').val() || null;

        $.ajax({
            type: 'GET',
            url: '{{ theme_shop_filter_url() }}',
            data: {
                category:  category_slug,
                rating:    rating,
                min_price: min_price,
                max_price: max_price,
                sort:      sort,
                page:      page,
                search:    search
            },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                $('.grid-product-list').html(data.grid);

                var p = data.pagination;
                $('.showing-results').html('{{ __('Showing') }} <strong style="color:var(--fp-green);">' + (p.to ? p.from + '-' + p.to : 0) + '</strong> {{ __('of') }} <strong style="color:var(--fp-green);">' + (p.total || 0) + '</strong> {{ __('results') }}');

                setTimeout(function () { $('.loader').hide(); }, 500);
            },
            error: function () { $('.loader').hide(); }
        });
    }

    window.fpFilterRequest = fpFilterRequest;

    // Category nav pills (top)
    $(document).on('click', '.fp-cat-nav .fp-cat-link', function (e) {
        e.preventDefault();
        $('.fp-cat-nav .fp-cat-link').removeClass('active');
        $(this).addClass('active');
        var slug = $(this).data('slug');
        $('.fp-cat-checkbox').prop('checked', false);
        if (slug) { $('.fp-cat-checkbox[data-slug="' + slug + '"]').prop('checked', true); }
        fpFilterRequest();
    });

    // Sidebar category checkboxes
    $(document).on('change', '.fp-cat-checkbox', function () { fpFilterRequest(); });

    // Sort select
    $('#fp-sort-select').on('change', function () { fpFilterRequest(); });

    // Rating filter
    $(document).on('change', '.fp-rating-filter', function () { fpFilterRequest(); });

    // Search on Enter
    $(document).on('keydown', '#fp-search-input', function (e) {
        if (e.key === 'Enter') { fpFilterRequest(); }
    });

});
</script>
@endsection
