@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title !!} @endsection

@section('style')
<style>
.lg-star-filter { color: var(--lx-gold, #C9A84C); }
</style>
@endsection

@section('content')
{{-- Breadcrumb --}}
<div class="lg-breadcrumb-bar">
    <div class="container">
        <div class="lg-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="active">{!! $page_post->title !!}</span>
        </div>
    </div>
</div>

{{-- Shop Layout --}}
<div class="container" style="padding:40px 0 80px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div class="lg-sidebar" style="margin-bottom:20px;">
                <div class="lg-sidebar-title">{{ __('Categories') }}</div>
                @foreach($categories as $cat)
                <div class="lg-check-row">
                    <label>
                        <input type="checkbox" class="lg-cat-checkbox"
                               data-slug="{{ $cat->slug }}" data-value="{{ $cat->name }}">
                        {{ $cat->name }}
                    </label>
                    <span class="lg-check-count">{{ $cat->product_count ?? '' }}</span>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Price Range --}}
            <div class="lg-sidebar" style="margin-bottom:20px;">
                <div class="lg-sidebar-title">{{ __('Price Range') }}</div>
                <div class="lg-price-inputs">
                    <input type="number" id="lg-min-price" placeholder="{{ __('Min') }}" min="0">
                    <span>—</span>
                    <input type="number" id="lg-max-price" placeholder="{{ __('Max') }}" min="0">
                </div>
                <button class="lx-btn lx-btn-outline" style="width:100%;justify-content:center;padding:10px;"
                        onclick="luxegemsFilterRequest()">
                    {{ __('Apply') }}
                </button>
            </div>

            {{-- Rating --}}
            <div class="lg-sidebar">
                <div class="lg-sidebar-title">{{ __('Rating') }}</div>
                <div class="lg-stars-row" style="margin-bottom:10px;">
                    <input type="radio" name="lg_rating" class="lg-rating-filter" value="5" style="accent-color:var(--lx-gold);">
                    <span class="lg-star-filter">★★★★★</span>
                </div>
                <div class="lg-stars-row" style="margin-bottom:10px;">
                    <input type="radio" name="lg_rating" class="lg-rating-filter" value="4" style="accent-color:var(--lx-gold);">
                    <span class="lg-star-filter">★★★★</span><span style="color:var(--lx-border);">★</span>
                </div>
                <div class="lg-stars-row">
                    <input type="radio" name="lg_rating" class="lg-rating-filter" value="3" style="accent-color:var(--lx-gold);">
                    <span class="lg-star-filter">★★★</span><span style="color:var(--lx-border);">★★</span>
                </div>
            </div>

        </div>{{-- /sidebar --}}

        {{-- Products --}}
        <div class="col-lg-9">

            {{-- Sort Bar --}}
            <div class="lg-shop-topbar">
                <span class="lg-shop-count showing-results">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <select class="lg-sort-select" id="lg-sort-select">
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
    function luxegemsFilterRequest(page) {
        var params = { sort: $('#lg-sort-select').val() };

        // collect all checked category slugs
        var slugs = [];
        $('.lg-cat-checkbox:checked').each(function () { slugs.push($(this).data('slug')); });
        if (slugs.length) params.category = slugs.join(',');

        var rating = $('input[name=lg_rating]:checked').val();
        if (rating) params.rating = rating;

        // only apply price filter when user has entered values
        var minP = $('#lg-min-price').val();
        var maxP = $('#lg-max-price').val();
        if (minP !== '' && maxP !== '') { params.min_price = minP; params.max_price = maxP; }

        if (page) params.page = page;

        $.ajax({
            type: 'GET',
            url: '{{ theme_shop_filter_url() }}',
            data: params,
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

    window.luxegemsFilterRequest = luxegemsFilterRequest;

    $(document).on('change', '.lg-cat-checkbox', function () { luxegemsFilterRequest(); });
    $('#lg-sort-select').on('change', function () { luxegemsFilterRequest(); });
    $(document).on('change', '.lg-rating-filter', function () { luxegemsFilterRequest(); });

});
</script>
@endsection
