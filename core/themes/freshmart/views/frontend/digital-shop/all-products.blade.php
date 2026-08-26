@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title !!} @endsection
@section('page-title') {!! $page_post->title !!} @endsection

@section('content')
<div class="fm-page-banner">
    <div class="container">
        <h1 style="font-size:28px;font-weight:800;color:var(--fm-dark);margin-bottom:8px;">{!! $page_post->title !!}</h1>
        <div class="fm-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="las la-angle-right" style="font-size:11px;"></i></span>
            <span class="current">{!! $page_post->title !!}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:36px 0 72px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div class="fm-sidebar-card">
                <div class="fm-sidebar-title">{{ __('Search') }}</div>
                <div class="d-flex gap-2">
                    <input type="text" id="fm-dp-search" class="fm-input flex-grow-1" placeholder="{{ __('Search…') }}">
                    <button class="fm-btn fm-btn-green fm-btn-sm" onclick="fmDpFilter()"><i class="las la-search"></i></button>
                </div>
            </div>

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div class="fm-sidebar-card">
                <div class="fm-sidebar-title">{{ __('Categories') }}</div>
                <div class="d-flex flex-column gap-1">
                    @foreach($categories as $cat)
                    <label class="fm-filter-item" style="cursor:pointer;">
                        <label class="fm-filter-label" style="cursor:pointer;margin:0;">
                            <input type="checkbox" class="fm-dp-cat-check" data-slug="{{ $cat->slug }}"
                                   style="accent-color:var(--fm-green);"> {{ $cat->name }}
                        </label>
                        @if($cat->product_count ?? false)
                            <span class="fm-filter-count">{{ $cat->product_count }}</span>
                        @endif
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Authors --}}
            @if($authors->isNotEmpty())
            <div class="fm-sidebar-card">
                <div class="fm-sidebar-title">{{ __('Authors') }}</div>
                <div class="d-flex flex-column gap-1">
                    @foreach($authors as $author)
                    <label class="fm-filter-item" style="cursor:pointer;">
                        <label class="fm-filter-label" style="cursor:pointer;margin:0;">
                            <input type="radio" name="fm_dp_author" class="fm-dp-author-filter" value="{{ $author->slug }}"
                                   style="accent-color:var(--fm-green);"> {{ $author->name }}
                        </label>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Languages --}}
            @if($languages->isNotEmpty())
            <div class="fm-sidebar-card">
                <div class="fm-sidebar-title">{{ __('Language') }}</div>
                <div class="d-flex flex-column gap-1">
                    @foreach($languages as $lang)
                    <label class="fm-filter-item" style="cursor:pointer;">
                        <label class="fm-filter-label" style="cursor:pointer;margin:0;">
                            <input type="radio" name="fm_dp_lang" class="fm-dp-lang-filter" value="{{ $lang->slug }}"
                                   style="accent-color:var(--fm-green);"> {{ $lang->name }}
                        </label>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Price Range --}}
            <div class="fm-sidebar-card">
                <div class="fm-sidebar-title">{{ __('Price Range') }}</div>
                <div class="d-flex gap-2 mb-3">
                    <input type="number" id="fm-dp-min" class="fm-price-input" placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" id="fm-dp-max" class="fm-price-input" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button class="fm-btn fm-btn-green fm-btn-sm w-100" onclick="fmDpFilter()">{{ __('Apply') }}</button>
            </div>

            {{-- Tags --}}
            @if($tags->isNotEmpty())
            <div class="fm-sidebar-card">
                <div class="fm-sidebar-title">{{ __('Tags') }}</div>
                <div class="fm-tag-cloud">
                    @foreach($tags->take(15) as $tag)
                        <button class="fm-tag fm-dp-tag-filter" data-tag="{{ $tag->tag_name }}">{{ $tag->tag_name }}</button>
                    @endforeach
                </div>
            </div>
            @endif

            <button class="fm-btn fm-btn-outline fm-btn-sm w-100" onclick="fmDpClear()">
                <i class="las la-times-circle"></i> {{ __('Clear Filters') }}
            </button>
        </div>

        {{-- Products --}}
        <div class="col-lg-9">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                <span class="showing-results" style="font-size:13px;color:var(--fm-muted);">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('products') }}
                </span>
                <div class="d-flex align-items-center gap-2">
                    <label style="font-size:13px;font-weight:600;color:var(--fm-muted);">{{ __('Sort:') }}</label>
                    <select id="fm-dp-sort" class="fm-select" style="width:auto;padding:8px 12px;">
                        <option value="3">{{ __('Latest') }}</option>
                        <option value="1">{{ __('Name') }}</option>
                        <option value="4">{{ __('Price: Low → High') }}</option>
                        <option value="5">{{ __('Price: High → Low') }}</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 fm-dp-grid">
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

    function fmDpFilter(page) {
        var cats = [];
        $('.fm-dp-cat-check:checked').each(function () { cats.push($(this).data('slug')); });

        var data = {
            category:  cats.join(',') || null,
            author:    $('input[name=fm_dp_author]:checked').val() || null,
            language:  $('input[name=fm_dp_lang]:checked').val() || null,
            tag:       $('.fm-dp-tag-filter.active').data('tag') || null,
            min_price: $('#fm-dp-min').val() || 0,
            max_price: $('#fm-dp-max').val() || 10000,
            sort:      $('#fm-dp-sort').val(),
            search:    $('#fm-dp-search').val() || null,
            page:      page || null,
        };

        $.ajax({
            type: 'GET',
            url: '{{ theme_digital_shop_filter_url() }}',
            data: data,
            beforeSend: function () { $('.loader').show(); },
            success: function (res) {
                $('.fm-dp-grid').html(res.grid);
                var p = res.pagination;
                var from = p.from ?? 0, total = p.total ?? 0;
                $('.showing-results').html('{{ __('Showing') }} <strong>' + from + '</strong> {{ __('of') }} <strong>' + total + '</strong> {{ __('products') }}');
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    }

    function fmDpClear() {
        $('.fm-dp-cat-check').prop('checked', false);
        $('input[name=fm_dp_author]').prop('checked', false);
        $('input[name=fm_dp_lang]').prop('checked', false);
        $('.fm-dp-tag-filter').removeClass('active');
        $('#fm-dp-min').val(0);
        $('#fm-dp-max').val(10000);
        $('#fm-dp-search').val('');
        fmDpFilter();
    }

    window.fmDpFilter = fmDpFilter;
    window.fmDpClear  = fmDpClear;

    $(document).on('change', '.fm-dp-cat-check, input[name=fm_dp_author], input[name=fm_dp_lang]', function () { fmDpFilter(); });
    $('#fm-dp-sort').on('change', function () { fmDpFilter(); });

    $(document).on('click', '.fm-dp-tag-filter', function () {
        var active = $(this).hasClass('active');
        $('.fm-dp-tag-filter').removeClass('active');
        if (!active) $(this).addClass('active');
        fmDpFilter();
    });

    $(document).on('click', '.fm-dp-page-btn', function (e) {
        e.preventDefault();
        fmDpFilter($(this).data('page'));
    });

    $(document).on('keyup', '#fm-dp-search', function () {
        clearTimeout(window._fmDpTimer);
        window._fmDpTimer = setTimeout(fmDpFilter, 350);
    });

})(jQuery);
</script>
@endsection
