@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title ?? __('Digital Shop') !!} @endsection
@section('page-title') {!! $page_post->title ?? __('Digital Shop') !!} @endsection

@section('content')
<div class="tn-page-banner">
    <div class="container tn-page-banner-content">
        <h1>{!! $page_post->title ?? __('Digital Shop') !!}</h1>
        <div class="tn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ $page_post->title ?? __('Digital Shop') }}</span>
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
                <div class="d-flex gap-2">
                    <input type="text" id="tn-dp-search" class="tn-input" placeholder="{{ __('Search products…') }}">
                    <button type="button" class="tn-btn tn-btn-primary tn-btn-icon-sq" onclick="tnDpFilter()">
                        <i class="las la-search"></i>
                    </button>
                </div>
            </div>

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div class="tn-sidebar-card">
                <div class="tn-sidebar-title">{{ __('Categories') }}</div>
                @foreach($categories as $cat)
                <label class="tn-filter-item">
                    <input type="checkbox" class="tn-dp-cat-check tn-hidden-input" data-slug="{{ $cat->slug }}">
                    <span class="tn-filter-label">{{ $cat->name }}</span>
                    @if($cat->product_count ?? false)
                        <span class="tn-filter-count">{{ $cat->product_count }}</span>
                    @endif
                </label>
                @endforeach
            </div>
            @endif

            {{-- Price Range --}}
            <div class="tn-sidebar-card">
                <div class="tn-sidebar-title">{{ __('Price Range') }}</div>
                <div class="d-flex gap-2 align-items-center">
                    <input type="number" id="tn-dp-min" class="tn-price-input" placeholder="{{ __('Min') }}" value="0" min="0">
                    <span class="tn-range-sep">–</span>
                    <input type="number" id="tn-dp-max" class="tn-price-input" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button class="tn-btn tn-btn-primary w-100 mt-3" onclick="tnDpFilter()">{{ __('Apply') }}</button>
            </div>

            {{-- Tags --}}
            @if(isset($tags) && $tags->isNotEmpty())
            <div class="tn-sidebar-card">
                <div class="tn-sidebar-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($tags->take(15) as $tag)
                    <button class="tn-tag tn-dp-tag" data-tag="{{ $tag->tag_name }}">
                        {{ $tag->tag_name }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            <button class="tn-btn tn-btn-outline w-100" id="tn-dp-clear">
                <i class="las la-times-circle"></i> {{ __('Clear Filters') }}
            </button>
        </div>

        {{-- Products --}}
        <div class="col-lg-9">

            {{-- Sort bar --}}
            <div class="tn-sort-bar">
                <span class="tn-sort-count tn-dp-showing">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('products') }}
                </span>
                <div class="d-flex align-items-center gap-2">
                    <label class="tn-sort-label">{{ __('Sort by') }}:</label>
                    <select id="tn-dp-sort" class="tn-select-sort">
                        <option value="3">{{ __('Latest') }}</option>
                        <option value="1">{{ __('Name') }}</option>
                        <option value="4">{{ __('Price: Low to High') }}</option>
                        <option value="5">{{ __('Price: High to Low') }}</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 tn-dp-grid">
                @include(include_theme_path('digital-shop.partials.product-partials.grid-products'))
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function ($) {
    function tnDpFilter(page) {
        var cats = [];
        $('.tn-dp-cat-check:checked').each(function () { cats.push($(this).data('slug')); });
        var data = {
            category:  cats.join(',') || null,
            tag:       $('.tn-dp-tag.active').data('tag') || null,
            search:    $('#tn-dp-search').val() || null,
            min_price: $('#tn-dp-min').val() || 0,
            max_price: $('#tn-dp-max').val() || 10000,
            sort:      $('#tn-dp-sort').val(),
            page:      page || null,
        };
        Object.keys(data).forEach(function (k) { if (data[k] === null) delete data[k]; });

        $.ajax({
            type: 'GET',
            url: '{{ theme_digital_shop_filter_url() }}',
            data: data,
            beforeSend: function () { $('.loader').show(); },
            success: function (res) {
                $('.tn-dp-grid').html(res.grid);
                var p = res.pagination;
                $('.tn-dp-showing').html(
                    '{{ __('Showing') }} <strong>' + (p.from ?? 0) + '–' + (p.to ?? 0) + '</strong> ' +
                    '{{ __('of') }} <strong>' + (p.total ?? 0) + '</strong> {{ __('products') }}'
                );
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    }

    window.tnDpFilter = tnDpFilter;

    $(document).on('change', '.tn-dp-cat-check', function () { tnDpFilter(); });
    $('#tn-dp-sort').on('change', function () { tnDpFilter(); });

    $(document).on('click', '.tn-dp-tag', function () {
        var wasActive = $(this).hasClass('active');
        $('.tn-dp-tag').removeClass('active');
        if (!wasActive) { $(this).addClass('active'); }
        tnDpFilter();
    });

    $(document).on('keyup', '#tn-dp-search', function () {
        clearTimeout(window._tnDpTimer);
        window._tnDpTimer = setTimeout(function () { tnDpFilter(); }, 350);
    });

    $('#tn-dp-clear').on('click', function () {
        $('.tn-dp-cat-check').prop('checked', false);
        $('.tn-dp-tag').removeClass('active');
        $('#tn-dp-search').val('');
        $('#tn-dp-min').val(0);
        $('#tn-dp-max').val(10000);
        tnDpFilter();
    });
})(jQuery);
</script>
@endsection
