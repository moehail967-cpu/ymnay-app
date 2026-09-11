@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title !!} @endsection
@section('page-title') {!! $page_post->title !!} @endsection

@section('content')
{{-- Page Header --}}
<div class="vl-page-header">
    <div class="container">
        <div class="vl-breadcrumb mb-3">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <span>{!! $page_post->title !!}</span>
        </div>
        <h1 style="font-style:italic;">{!! $page_post->title !!}</h1>
    </div>
</div>

{{-- Shop Layout --}}
<div class="container" style="padding:40px 0 80px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div class="vl-sidebar-card">
                <div class="vl-sidebar-title">{{ __('Categories') }}</div>
                @foreach($categories as $cat)
                <div class="vl-check-item">
                    <label>
                        <input type="checkbox" class="vl-cat-checkbox"
                               data-slug="{{ $cat->slug }}" data-value="{{ $cat->name }}">
                        {{ $cat->name }}
                    </label>
                    <span class="vl-check-count">{{ $cat->product_count ?? '' }}</span>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Price Range --}}
            <div class="vl-sidebar-card">
                <div class="vl-sidebar-title">{{ __('Price Range') }}</div>
                <div class="vl-price-inputs">
                    <input type="number" id="vl-min-price" placeholder="{{ __('Min') }}" value="0" min="0">
                    <span>—</span>
                    <input type="number" id="vl-max-price" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button class="vl-btn vl-btn-outline" style="width:100%;justify-content:center;padding:10px;"
                        onclick="velvetluxFilterRequest()">
                    {{ __('Apply') }}
                </button>
            </div>

            {{-- Rating --}}
            <div class="vl-sidebar-card">
                <div class="vl-sidebar-title">{{ __('Rating') }}</div>
                <div class="vl-rating-row">
                    <input type="radio" name="vl_rating" class="vl-rating-filter" value="5">
                    <span class="vl-rating-stars">★★★★★</span>
                </div>
                <div class="vl-rating-row">
                    <input type="radio" name="vl_rating" class="vl-rating-filter" value="4">
                    <span class="vl-rating-stars">★★★★</span><span style="color:var(--vl-border);">★</span>
                </div>
                <div class="vl-rating-row">
                    <input type="radio" name="vl_rating" class="vl-rating-filter" value="3">
                    <span class="vl-rating-stars">★★★</span><span style="color:var(--vl-border);">★★</span>
                </div>
            </div>

        </div>{{-- /sidebar --}}

        {{-- Products --}}
        <div class="col-lg-9">

            {{-- Sort Bar --}}
            <div class="vl-sort-bar">
                <span class="vl-sort-count showing-results">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <select class="vl-sort-select" id="vl-sort-select">
                    <option value="3">{{ __('Sort: Latest') }}</option>
                    <option value="1">{{ __('Sort: Name') }}</option>
                    <option value="2">{{ __('Sort: Popular') }}</option>
                    <option value="4">{{ __('Price: Low to High') }}</option>
                    <option value="5">{{ __('Price: High to Low') }}</option>
                </select>
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
    function velvetluxFilterRequest(page) {
        page = page || null;

        var cats = [];
        $('.vl-cat-checkbox:checked').each(function () { cats.push($(this).data('slug')); });
        var category_slug = cats.join(',') || null;

        var rating    = $('input[name=vl_rating]:checked').val() || null;
        var min_price = $('#vl-min-price').val() || 0;
        var max_price = $('#vl-max-price').val() || 10000;
        var sort      = $('#vl-sort-select').val();

        $.ajax({
            type: 'GET',
            url: '{{ theme_shop_filter_url() }}',
            data: { category: category_slug, rating: rating, min_price: min_price, max_price: max_price, sort: sort, page: page },
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

    window.velvetluxFilterRequest = velvetluxFilterRequest;

    $(document).on('change', '.vl-cat-checkbox', function () { velvetluxFilterRequest(); });
    $('#vl-sort-select').on('change', function () { velvetluxFilterRequest(); });
    $(document).on('change', '.vl-rating-filter', function () { velvetluxFilterRequest(); });

    // .add-to-wishlist-btn and .add-to-cart-btn are handled globally in footer.blade.php
});
</script>
@endsection
