@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title !!} @endsection
@section('page-title') {!! $page_post->title !!} @endsection

@section('content')

<div class="ch-page-banner">
    <div class="container ch-page-banner-content">
        <h1>{!! $page_post->title !!}</h1>
        <div class="ch-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{!! $page_post->title !!}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:40px 0 80px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div class="ch-sidebar-card">
                <div class="ch-sidebar-title">{{ __('Search') }}</div>
                <div style="position:relative;">
                    <input type="text" id="ch-dp-search" class="ch-price-input"
                           style="width:100%;padding-right:40px;"
                           placeholder="{{ __('Search products…') }}">
                    <button type="button" onclick="chDpFilter()"
                            style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--ch-red);font-size:18px;">
                        <i class="las la-search"></i>
                    </button>
                </div>
            </div>

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div class="ch-sidebar-card">
                <div class="ch-sidebar-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach($categories as $cat)
                    <label class="ch-filter-item" style="cursor:pointer;">
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                            <input type="checkbox" class="ch-dp-cat" data-slug="{{ $cat->slug }}"> {{ $cat->name }}
                        </label>
                        @if($cat->product_count ?? false)
                            <span class="ch-filter-count">{{ $cat->product_count }}</span>
                        @endif
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Authors --}}
            @if($authors->isNotEmpty())
            <div class="ch-sidebar-card">
                <div class="ch-sidebar-title">{{ __('Authors') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach($authors as $author)
                    <label class="ch-filter-item" style="cursor:pointer;">
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                            <input type="radio" name="ch_dp_author" class="ch-dp-author" value="{{ $author->slug }}"> {{ $author->name }}
                        </label>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Languages --}}
            @if($languages->isNotEmpty())
            <div class="ch-sidebar-card">
                <div class="ch-sidebar-title">{{ __('Language') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach($languages as $lang)
                    <label class="ch-filter-item" style="cursor:pointer;">
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                            <input type="radio" name="ch_dp_lang" class="ch-dp-lang" value="{{ $lang->slug }}"> {{ $lang->name }}
                        </label>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Price --}}
            <div class="ch-sidebar-card">
                <div class="ch-sidebar-title">{{ __('Price Range') }}</div>
                <div class="ch-price-inputs">
                    <input type="number" id="ch-dp-min" class="ch-price-input" placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" id="ch-dp-max" class="ch-price-input" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button class="ch-btn ch-btn-primary ch-btn-sm mt-3 w-100" onclick="chDpFilter()">{{ __('Apply Filter') }}</button>
            </div>

            {{-- Tags --}}
            @if($tags->isNotEmpty())
            <div class="ch-sidebar-card">
                <div class="ch-sidebar-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($tags->take(15) as $tag)
                        <a href="javascript:void(0)" class="ch-tag ch-dp-tag" data-tag="{{ $tag->tag_name }}">#{{ $tag->tag_name }}</a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="text-center mt-2">
                <a href="javascript:void(0)" onclick="chDpClear()" style="font-size:13px;color:var(--ch-muted);text-decoration:none;">
                    <i class="las la-times-circle"></i> {{ __('Clear All Filters') }}
                </a>
            </div>
        </div>

        {{-- Products --}}
        <div class="col-lg-9">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
                <span class="showing-results" style="font-size:13px;color:var(--ch-muted);">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('products') }}
                </span>
                <select id="ch-dp-sort" class="ch-sort-select">
                    <option value="3">{{ __('Sort: Latest') }}</option>
                    <option value="1">{{ __('Sort: Name') }}</option>
                    <option value="4">{{ __('Price: Low to High') }}</option>
                    <option value="5">{{ __('Price: High to Low') }}</option>
                </select>
            </div>

            <div class="row g-3 grid-product-list">
                @include(include_theme_path('digital-shop.partials.product-partials.grid-products'))
            </div>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';

    function chDpFilter(page) {
        var cats = [];
        $('.ch-dp-cat:checked').each(function () { cats.push($(this).data('slug')); });

        var data = {
            category:  cats.join(',') || null,
            author:    $('input[name=ch_dp_author]:checked').val() || null,
            language:  $('input[name=ch_dp_lang]:checked').val() || null,
            tag:       $('.ch-dp-tag.active').data('tag') || null,
            min_price: $('#ch-dp-min').val() || 0,
            max_price: $('#ch-dp-max').val() || 10000,
            sort:      $('#ch-dp-sort').val(),
            page:      page || null,
            search:    $('#ch-dp-search').val() || null,
        };

        $.ajax({
            type: 'GET',
            url: '{{ theme_digital_shop_filter_url() }}',
            data: data,
            beforeSend: function () { $('.loader').show(); },
            success: function (res) {
                $('.grid-product-list').html(res.grid);
                var p = res.pagination;
                $('.showing-results').html(
                    '{{ __('Showing') }} <strong>' + (p.from ?? 0) + '</strong> {{ __('of') }} <strong>' + (p.total ?? 0) + '</strong> {{ __('products') }}'
                );
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    }

    function chDpClear() {
        $('.ch-dp-cat').prop('checked', false);
        $('input[name=ch_dp_author]').prop('checked', false);
        $('input[name=ch_dp_lang]').prop('checked', false);
        $('.ch-dp-tag').removeClass('active');
        $('#ch-dp-min').val(0);
        $('#ch-dp-max').val(10000);
        $('#ch-dp-search').val('');
        chDpFilter();
    }

    window.chDpFilter = chDpFilter;
    window.chDpClear  = chDpClear;

    $(document).on('change', '.ch-dp-cat, input[name=ch_dp_author], input[name=ch_dp_lang]', function () { chDpFilter(); });
    $('#ch-dp-sort').on('change', function () { chDpFilter(); });

    $(document).on('click', '.ch-dp-tag', function () {
        var active = $(this).hasClass('active');
        $('.ch-dp-tag').removeClass('active');
        if (!active) { $(this).addClass('active'); }
        chDpFilter();
    });

    $(document).on('click', '.ch-page-btn', function (e) {
        e.preventDefault();
        chDpFilter($(this).data('page'));
    });

    $(document).on('keydown', '#ch-dp-search', function (e) {
        if (e.key === 'Enter') { chDpFilter(); }
    });

})(jQuery);
</script>
@endsection
