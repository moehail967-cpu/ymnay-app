@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title !!} @endsection
@section('page-title') {!! $page_post->title !!} @endsection

@section('content')
{{-- Page Hero --}}
<div class="pf-page-hero">
    <div class="container">
        <h1>{!! $page_post->title !!}</h1>
        <div class="pf-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="las la-angle-right" style="font-size:11px;"></i></span>
            <span class="current">{!! $page_post->title !!}</span>
        </div>
    </div>
</div>

{{-- Shop Layout --}}
<div style="background:var(--pf-bg);padding:36px 0 72px;">
<div class="container">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">
            <div class="pf-sidebar">

                {{-- Search --}}
                <div class="pf-sidebar-search">
                    <input type="text" id="pf-search-input" placeholder="{{ __('Search products…') }}">
                    <button type="button" onclick="pfFilterRequest()"><i class="mdi mdi-magnify"></i></button>
                </div>

                {{-- Categories --}}
                @if($categories->isNotEmpty())
                <div class="pf-sidebar-section">
                    <div class="pf-sidebar-title">{{ __('Categories') }}</div>
                    @foreach($categories as $cat)
                    <div class="pf-check-item">
                        <label>
                            <input type="checkbox" class="pf-cat-checkbox-input" data-slug="{{ $cat->slug }}">
                            {{ $cat->name }}
                        </label>
                        @if($cat->product_count ?? false)
                            <span class="pf-count-badge">{{ $cat->product_count }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Price Range --}}
                <div class="pf-sidebar-section">
                    <div class="pf-sidebar-title">{{ __('Price Range') }}</div>
                    <div class="pf-price-inputs">
                        <input type="number" id="pf-min-price" placeholder="{{ __('Min') }}" value="0" min="0">
                        <input type="number" id="pf-max-price" placeholder="{{ __('Max') }}" value="10000" min="0">
                    </div>
                    <button class="pf-btn pf-btn-teal pf-btn-sm w-100" onclick="pfFilterRequest()">
                        {{ __('Apply') }}
                    </button>
                </div>

                {{-- Rating --}}
                <div class="pf-sidebar-section">
                    <div class="pf-sidebar-title">{{ __('Rating') }}</div>
                    <label class="pf-rating-row"><input type="radio" name="pf_rating" class="pf-rating-filter" value="5"> <span class="pf-rating-stars">★★★★★</span> <span style="font-size:12px;color:var(--pf-muted);">(5)</span></label>
                    <label class="pf-rating-row"><input type="radio" name="pf_rating" class="pf-rating-filter" value="4"> <span class="pf-rating-stars">★★★★</span><span style="color:#ccc;">★</span> <span style="font-size:12px;color:var(--pf-muted);">(4+)</span></label>
                    <label class="pf-rating-row"><input type="radio" name="pf_rating" class="pf-rating-filter" value="3"> <span class="pf-rating-stars">★★★</span><span style="color:#ccc;">★★</span> <span style="font-size:12px;color:var(--pf-muted);">(3+)</span></label>
                </div>

                <a class="pf-clear-link" id="pf-clear-filters">
                    <i class="mdi mdi-close-circle-outline"></i> {{ __('Clear Filters') }}
                </a>
            </div>
        </div>

        {{-- Products --}}
        <div class="col-lg-9">

            <div class="pf-sort-bar">
                <p class="showing-results">
                    {{ __('Showing') }} <span>{{ $pagination->count() }}</span> {{ __('of') }} <span>{{ $pagination->total() }}</span> {{ __('results') }}
                </p>
                <div class="d-flex align-items-center gap-2">
                    <span style="font-size:13px;font-weight:500;color:var(--pf-muted);">{{ __('Sort by:') }}</span>
                    <select class="pf-sort-select" id="pf-sort-select">
                        <option value="3">{{ __('Latest') }}</option>
                        <option value="1">{{ __('Name') }}</option>
                        <option value="2">{{ __('Popular') }}</option>
                        <option value="4">{{ __('Price: Low–High') }}</option>
                        <option value="5">{{ __('Price: High–Low') }}</option>
                    </select>
                </div>
            </div>

            <div class="row row-cols-2 row-cols-md-3 g-3 grid-product-list">
                @include(include_theme_path('shop.partials.product-partials.grid-products'))
            </div>

        </div>
    </div>
</div>
</div>

<div id="product-modal"></div>
@endsection

@section('scripts')
<script>
$(function () {
    function pfFilterRequest(page) {
        page = page || null;

        var cats = [];
        $('.pf-cat-checkbox-input:checked').each(function () { cats.push($(this).data('slug')); });
        var category_slug = cats.join(',') || null;

        var rating    = $('input[name=pf_rating]:checked').val() || null;
        var min_price = $('#pf-min-price').val() || 0;
        var max_price = $('#pf-max-price').val() || 10000;
        var sort      = $('#pf-sort-select').val();
        var search    = $('#pf-search-input').val() || null;

        $.ajax({
            type: 'GET',
            url: '{{ theme_shop_filter_url() }}',
            data: { category: category_slug, rating: rating, min_price: min_price, max_price: max_price, sort: sort, page: page, search: search },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                $('.grid-product-list').html(data.grid);
                var p = data.pagination;
                var from = p.to ? p.from + '–' + p.to : 0;
                $('.showing-results').html('{{ __("Showing") }} <span>' + from + '</span> {{ __("of") }} <span>' + (p.total || 0) + '</span> {{ __("results") }}');
                setTimeout(function () { $('.loader').hide(); }, 500);
            },
            error: function () { $('.loader').hide(); }
        });
    }

    window.pfFilterRequest = pfFilterRequest;

    $(document).on('change', '.pf-cat-checkbox-input', function () { pfFilterRequest(); });
    $('#pf-sort-select').on('change', function () { pfFilterRequest(); });
    $(document).on('change', '.pf-rating-filter', function () { pfFilterRequest(); });

    $(document).on('keyup', '#pf-search-input', function () {
        var val = $(this).val();
        if (val.length === 0 || val.length >= 2) { pfFilterRequest(); }
    });

    $('#pf-clear-filters').on('click', function () {
        $('.pf-cat-checkbox-input').prop('checked', false);
        $('input[name=pf_rating]').prop('checked', false);
        $('#pf-min-price').val(0);
        $('#pf-max-price').val(10000);
        $('#pf-search-input').val('');
        $('#pf-sort-select').val(3);
        pfFilterRequest();
    });
});
</script>
@endsection
