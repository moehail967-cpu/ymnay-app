@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title !!} @endsection
@section('page-title') {!! $page_post->title !!} @endsection

@section('content')
{{-- Page Banner --}}
<div class="sz-page-banner">
    <div class="container">
        <h1>{!! $page_post->title !!}</h1>
        <div class="sz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{!! $page_post->title !!}</span>
        </div>
    </div>
</div>

{{-- Category Tab Strip --}}
@if($categories->isNotEmpty())
<div style="background:var(--sz-white);border-bottom:2px solid var(--sz-border);">
    <div class="container" style="overflow-x:auto;white-space:nowrap;padding:12px 0;">
        <button class="sz-btn sz-btn-sm sz-btn-red sz-cat-nav-btn" data-slug="" style="margin-right:6px;">{{ __('All') }}</button>
        @foreach($categories as $cat)
            <button class="sz-btn sz-btn-sm sz-btn-outline sz-cat-nav-btn" data-slug="{{ $cat->slug }}" style="margin-right:6px;font-family:var(--sz-font-head);font-size:12px;letter-spacing:1.5px;">
                {{ strtoupper($cat->name) }}
            </button>
        @endforeach
    </div>
</div>
@endif

{{-- Main Layout --}}
<div class="container" style="padding:36px 0 72px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">
            <div class="sz-sidebar">
                <div class="sz-sidebar-head"><i class="mdi mdi-filter-outline"></i> {{ __('Filter') }}</div>

                @if($categories->isNotEmpty())
                <div class="sz-sidebar-block">
                    <div class="sz-sidebar-title">{{ __('Sport / Category') }}</div>
                    @foreach($categories as $cat)
                    <label class="sz-filter-check">
                        <input type="checkbox" class="sz-cat-check" data-slug="{{ $cat->slug }}" data-value="{{ $cat->name }}">
                        {{ $cat->name }}
                        <span class="sz-filter-check-count">{{ $cat->product_count ?? '' }}</span>
                    </label>
                    @endforeach
                </div>
                @endif

                <div class="sz-sidebar-block">
                    <div class="sz-sidebar-title">{{ __('Price Range') }}</div>
                    <div class="sz-price-inputs" style="margin-bottom:12px;">
                        <input type="number" id="sz-min-price" placeholder="{{ __('Min') }}" value="0" min="0">
                        <span style="color:var(--sz-muted);">–</span>
                        <input type="number" id="sz-max-price" placeholder="{{ __('Max') }}" value="10000" min="0">
                    </div>
                    <button class="sz-btn sz-btn-red sz-btn-sm w-100" onclick="szFilterRequest()">{{ __('Apply') }}</button>
                </div>

                <div class="sz-sidebar-block">
                    <div class="sz-sidebar-title">{{ __('Rating') }}</div>
                    @foreach([5,4,3] as $r)
                    <label class="sz-filter-check">
                        <input type="radio" name="sz_rating" class="sz-rating-filter" value="{{ $r }}">
                        <span style="color:#FDD835;">{{ str_repeat('★',$r) }}</span><span style="color:#ccc;">{{ str_repeat('★',5-$r) }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Products --}}
        <div class="col-lg-9">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding:12px 16px;background:var(--sz-white);border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);">
                <span class="showing-results" style="font-family:var(--sz-font-head);font-size:13px;color:var(--sz-muted);text-transform:uppercase;letter-spacing:1px;">
                    {{ __('Showing') }} <strong style="color:var(--sz-dark);">{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong style="color:var(--sz-dark);">{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <select id="sz-sort-select" style="border:0;font-family:var(--sz-font-head);font-size:13px;text-transform:uppercase;letter-spacing:1px;outline:none;cursor:pointer;color:var(--sz-dark);">
                    <option value="3">{{ __('Sort: Latest') }}</option>
                    <option value="1">{{ __('Sort: Name') }}</option>
                    <option value="2">{{ __('Sort: Popular') }}</option>
                    <option value="4">{{ __('Price: Low → High') }}</option>
                    <option value="5">{{ __('Price: High → Low') }}</option>
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
    function szFilterRequest(page) {
        page = page || null;
        var cats = [];
        $('.sz-cat-check:checked').each(function () { cats.push($(this).data('slug')); });
        var category_slug = cats.join(',') || null;
        var rating    = $('input[name=sz_rating]:checked').val() || null;
        var min_price = $('#sz-min-price').val() || 0;
        var max_price = $('#sz-max-price').val() || 10000;
        var sort      = $('#sz-sort-select').val();

        $.ajax({
            type: 'GET',
            url: '{{ theme_shop_filter_url() }}',
            data: { category: category_slug, rating: rating, min_price: min_price, max_price: max_price, sort: sort, page: page },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                $('.grid-product-list').html(data.grid);
                var p = data.pagination;
                $('.showing-results').html('{{ __("Showing") }} <strong style="color:var(--sz-dark);">' + (p.to ? p.from + '-' + p.to : 0) + '</strong> {{ __("of") }} <strong style="color:var(--sz-dark);">' + (p.total || 0) + '</strong> {{ __("results") }}');
                setTimeout(function () { $('.loader').hide(); }, 500);
            },
            error: function () { $('.loader').hide(); }
        });
    }

    window.szFilterRequest = szFilterRequest;

    $(document).on('click', '.sz-cat-nav-btn', function () {
        var slug = $(this).data('slug');
        $('.sz-cat-nav-btn').removeClass('sz-btn-red').addClass('sz-btn-outline');
        $(this).removeClass('sz-btn-outline').addClass('sz-btn-red');
        $('.sz-cat-check').prop('checked', false);
        if (slug) { $('.sz-cat-check[data-slug="' + slug + '"]').prop('checked', true); }
        szFilterRequest();
    });

    $(document).on('change', '.sz-cat-check', function () { szFilterRequest(); });
    $('#sz-sort-select').on('change', function () { szFilterRequest(); });
    $(document).on('change', '.sz-rating-filter', function () { szFilterRequest(); });

        // .add-to-cart-btn handled globally in footer.blade.php
});
</script>
@endsection
