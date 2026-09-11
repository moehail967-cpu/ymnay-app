@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title !!} @endsection
@section('page-title') {!! $page_post->title !!} @endsection

@section('content')

<div class="bk-page-banner">
    <div class="container">
        <h1>{!! $page_post->title !!}</h1>
        <div class="bk-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{!! $page_post->title !!}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:40px 0 80px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div class="bk-sidebar-card">
                <div class="bk-sidebar-title">{{ __('Search') }}</div>
                <div style="position:relative;">
                    <input type="text" id="bk-dp-search" class="bk-price-input" style="width:100%;padding-right:38px;"
                           placeholder="{{ __('Search products…') }}">
                    <button type="button" onclick="bkDpFilter()"
                            style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--bk-rose);font-size:18px;">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                </div>
            </div>

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div class="bk-sidebar-card">
                <div class="bk-sidebar-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach($categories as $cat)
                    <label class="bk-filter-item">
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                            <input type="checkbox" class="bk-dp-cat" data-slug="{{ $cat->slug }}"> {{ $cat->name }}
                        </label>
                        @if($cat->product_count ?? false)
                            <span class="bk-filter-count">{{ $cat->product_count }}</span>
                        @endif
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Authors --}}
            @if($authors->isNotEmpty())
            <div class="bk-sidebar-card">
                <div class="bk-sidebar-title">{{ __('Authors') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach($authors as $author)
                    <label class="bk-filter-item">
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                            <input type="radio" name="bk_dp_author" class="bk-dp-author" value="{{ $author->slug }}"> {{ $author->name }}
                        </label>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Languages --}}
            @if($languages->isNotEmpty())
            <div class="bk-sidebar-card">
                <div class="bk-sidebar-title">{{ __('Language') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach($languages as $lang)
                    <label class="bk-filter-item">
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                            <input type="radio" name="bk_dp_lang" class="bk-dp-lang" value="{{ $lang->slug }}"> {{ $lang->name }}
                        </label>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Price --}}
            <div class="bk-sidebar-card">
                <div class="bk-sidebar-title">{{ __('Price Range') }}</div>
                <div class="bk-price-inputs">
                    <input type="number" id="bk-dp-min" class="bk-price-input" placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" id="bk-dp-max" class="bk-price-input" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button class="bk-btn bk-btn-rose bk-btn-sm mt-3 w-100" onclick="bkDpFilter()">{{ __('Apply Filter') }}</button>
            </div>

            {{-- Tags --}}
            @if($tags->isNotEmpty())
            <div class="bk-sidebar-card">
                <div class="bk-sidebar-title">{{ __('Tags') }}</div>
                <div>
                    @foreach($tags->take(15) as $tag)
                        <a href="javascript:void(0)" class="bk-tag-pill bk-dp-tag" data-tag="{{ $tag->tag_name }}">#{{ $tag->tag_name }}</a>
                    @endforeach
                </div>
            </div>
            @endif

            <a class="bk-clear-link" href="javascript:void(0)" onclick="bkDpClear()">
                <i class="mdi mdi-close-circle-outline"></i> {{ __('Clear All Filters') }}
            </a>
        </div>

        {{-- Products --}}
        <div class="col-lg-9">
            <div class="bk-topbar">
                <span class="bk-topbar-count showing-results">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('products') }}
                </span>
                <select id="bk-dp-sort" class="bk-sort-select">
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

    function bkDpFilter(page) {
        var cats = [];
        $('.bk-dp-cat:checked').each(function () { cats.push($(this).data('slug')); });

        var data = {
            category:  cats.join(',') || null,
            author:    $('input[name=bk_dp_author]:checked').val() || null,
            language:  $('input[name=bk_dp_lang]:checked').val() || null,
            tag:       $('.bk-dp-tag.active').data('tag') || null,
            min_price: $('#bk-dp-min').val() || 0,
            max_price: $('#bk-dp-max').val() || 10000,
            sort:      $('#bk-dp-sort').val(),
            page:      page || null,
            search:    $('#bk-dp-search').val() || null,
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

    function bkDpClear() {
        $('.bk-dp-cat').prop('checked', false);
        $('input[name=bk_dp_author]').prop('checked', false);
        $('input[name=bk_dp_lang]').prop('checked', false);
        $('.bk-dp-tag').removeClass('active');
        $('#bk-dp-min').val(0);
        $('#bk-dp-max').val(10000);
        $('#bk-dp-search').val('');
        bkDpFilter();
    }

    window.bkDpFilter = bkDpFilter;
    window.bkDpClear  = bkDpClear;

    $(document).on('change', '.bk-dp-cat, input[name=bk_dp_author], input[name=bk_dp_lang]', function () { bkDpFilter(); });
    $('#bk-dp-sort').on('change', function () { bkDpFilter(); });

    $(document).on('click', '.bk-dp-tag', function () {
        var active = $(this).hasClass('active');
        $('.bk-dp-tag').removeClass('active');
        if (!active) { $(this).addClass('active'); }
        bkDpFilter();
    });

    $(document).on('click', '.bk-page-btn', function (e) {
        e.preventDefault();
        bkDpFilter($(this).data('page'));
    });

    $(document).on('keydown', '#bk-dp-search', function (e) {
        if (e.key === 'Enter') { bkDpFilter(); }
    });

})(jQuery);
</script>
@endsection
