@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title !!} @endsection
@section('page-title') {!! $page_post->title !!} @endsection

@section('content')

{{-- Breadcrumb --}}
<div class="dk-breadcrumb-bar">
    <div class="container">
        <ul class="dk-bc-list">
            <li><a href="{{ theme_home_url() }}"><i class="mdi mdi-home-outline"></i> {{ __('Home') }}</a></li>
            <li class="sep"><i class="mdi mdi-chevron-right"></i></li>
            <li class="active">{!! $page_post->title !!}</li>
        </ul>
    </div>
</div>

{{-- Category Tab Strip --}}
@if($categories->isNotEmpty())
<div class="dk-cat-strip">
    <div class="container dk-cat-strip-inner">
        <button class="dk-btn dk-btn-red dk-btn-sm dk-cat-nav-btn" data-slug="">{{ __('All') }}</button>
        @foreach($categories as $cat)
            <button class="dk-btn dk-btn-ghost dk-btn-sm dk-cat-nav-btn" data-slug="{{ $cat->slug }}">
                {{ strtoupper($cat->name) }}
            </button>
        @endforeach
    </div>
</div>
@endif

{{-- Main Layout --}}
<section class="py-4">
    <div class="container">
        <div class="row g-4">

            {{-- Sidebar --}}
            <div class="col-lg-3">

                {{-- Search --}}
                <div class="dk-sidebar-card">
                    <div class="dk-sidebar-title"><i class="mdi mdi-magnify"></i> {{ __('Search') }}</div>
                    <div class="dk-sidebar-search">
                        <input type="text" id="dk-dp-search" placeholder="{{ __('Search products...') }}">
                        <button type="button" onclick="dkDpFilter()"><i class="mdi mdi-magnify"></i></button>
                    </div>
                </div>

                {{-- Categories --}}
                @if($categories->isNotEmpty())
                <div class="dk-sidebar-card">
                    <div class="dk-sidebar-title">{{ __('Categories') }}</div>
                    <div class="d-flex flex-column gap-1">
                        @foreach($categories as $cat)
                        <label class="dk-check-label">
                            <span><input type="checkbox" class="dk-cat-check" data-slug="{{ $cat->slug }}"> {{ $cat->name }}</span>
                            @if($cat->product_count ?? false)
                                <span class="dk-check-count">{{ $cat->product_count }}</span>
                            @endif
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Authors --}}
                @if($authors->isNotEmpty())
                <div class="dk-sidebar-card">
                    <div class="dk-sidebar-title"><i class="mdi mdi-account-outline"></i> {{ __('Authors') }}</div>
                    <div class="d-flex flex-column gap-1">
                        @foreach($authors as $author)
                        <label class="dk-check-label">
                            <span><input type="radio" name="dk_dp_author" class="dk-author-filter" value="{{ $author->slug }}"> {{ $author->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Languages --}}
                @if($languages->isNotEmpty())
                <div class="dk-sidebar-card">
                    <div class="dk-sidebar-title"><i class="mdi mdi-translate"></i> {{ __('Language') }}</div>
                    <div class="d-flex flex-column gap-1">
                        @foreach($languages as $lang)
                        <label class="dk-check-label">
                            <span><input type="radio" name="dk_dp_lang" class="dk-lang-filter" value="{{ $lang->slug }}"> {{ $lang->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Price Range --}}
                <div class="dk-sidebar-card">
                    <div class="dk-sidebar-title">{{ __('Price Range') }}</div>
                    <div class="dk-price-row">
                        <input type="number" id="dk-min-price" class="dk-price-input" placeholder="{{ __('Min') }}" value="0" min="0">
                        <input type="number" id="dk-max-price" class="dk-price-input" placeholder="{{ __('Max') }}" value="10000" min="0">
                    </div>
                    <button class="dk-btn dk-btn-red dk-btn-sm dk-btn-block" onclick="dkDpFilter()">{{ __('APPLY') }}</button>
                </div>

                {{-- Tags --}}
                @if($tags->isNotEmpty())
                <div class="dk-sidebar-card">
                    <div class="dk-sidebar-title"><i class="mdi mdi-tag-outline"></i> {{ __('Tags') }}</div>
                    <div>
                        @foreach($tags->take(15) as $tag)
                            <button class="dk-tag dk-tag-filter" data-tag="{{ $tag->tag_name }}">#{{ $tag->tag_name }}</button>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Clear All --}}
                <a class="dk-clear-link" onclick="dkDpClear()">
                    <i class="mdi mdi-close-circle-outline"></i> {{ __('Clear All Filters') }}
                </a>

            </div>

            {{-- Products --}}
            <div class="col-lg-9">
                <div class="dk-sort-bar">
                    <span class="dk-sort-count showing-results">
                        {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                        {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('products') }}
                    </span>
                    <div class="d-flex align-items-center gap-2">
                        <label class="dk-sort-label">{{ __('SORT BY:') }}</label>
                        <select id="dk-sort-select" class="dk-sort-select">
                            <option value="3">{{ __('LATEST') }}</option>
                            <option value="1">{{ __('NAME') }}</option>
                            <option value="4">{{ __('PRICE: LOW TO HIGH') }}</option>
                            <option value="5">{{ __('PRICE: HIGH TO LOW') }}</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 grid-product-list">
                    @include(include_theme_path('digital-shop.partials.product-partials.grid-products'))
                </div>
            </div>

        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';

    function dkDpFilter(page) {
        var cats = [];
        $('.dk-cat-check:checked').each(function () { cats.push($(this).data('slug')); });

        var data = {
            category:  cats.join(',') || null,
            author:    $('input[name=dk_dp_author]:checked').val() || null,
            language:  $('input[name=dk_dp_lang]:checked').val() || null,
            tag:       $('.dk-tag-filter.active').data('tag') || null,
            min_price: $('#dk-min-price').val() || 0,
            max_price: $('#dk-max-price').val() || 10000,
            sort:      $('#dk-sort-select').val(),
            page:      page || null,
        };

        $.ajax({
            type: 'GET',
            url: '{{ theme_digital_shop_filter_url() }}',
            data: data,
            beforeSend: function () { $('.loader').show(); },
            success: function (res) {
                $('.grid-product-list').html(res.grid);
                var p = res.pagination;
                var from = p.from ?? 0, total = p.total ?? 0;
                $('.showing-results').html('{{ __('Showing') }} <strong>' + from + '</strong> {{ __('of') }} <strong>' + total + '</strong> {{ __('products') }}');
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    }

    function dkDpClear() {
        $('.dk-cat-check').prop('checked', false);
        $('input[name=dk_dp_author]').prop('checked', false);
        $('input[name=dk_dp_lang]').prop('checked', false);
        $('.dk-tag-filter').removeClass('active');
        $('#dk-min-price').val(0);
        $('#dk-max-price').val(10000);
        dkDpFilter();
    }

    window.dkDpFilter = dkDpFilter;
    window.dkDpClear  = dkDpClear;

    $(document).on('click', '.dk-cat-nav-btn', function () {
        var slug = $(this).data('slug');
        $('.dk-cat-nav-btn').removeClass('dk-btn-red').addClass('dk-btn-ghost');
        $(this).removeClass('dk-btn-ghost').addClass('dk-btn-red');
        $('.dk-cat-check').prop('checked', false);
        if (slug) { $('.dk-cat-check[data-slug="' + slug + '"]').prop('checked', true); }
        dkDpFilter();
    });

    $(document).on('change', '.dk-cat-check, input[name=dk_dp_author], input[name=dk_dp_lang]', function () { dkDpFilter(); });
    $('#dk-sort-select').on('change', function () { dkDpFilter(); });

    $(document).on('click', '.dk-tag-filter', function () {
        var active = $(this).hasClass('active');
        $('.dk-tag-filter').removeClass('active');
        if (!active) { $(this).addClass('active'); }
        dkDpFilter();
    });

    $(document).on('click', '.dk-page-btn', function (e) {
        e.preventDefault();
        dkDpFilter($(this).data('page'));
    });

    $(document).on('keyup', '#dk-dp-search', function () {
        clearTimeout(window._dpSearchTimer);
        var q = $(this).val();
        window._dpSearchTimer = setTimeout(function () { dkDpFilter(); }, 350);
    });

})(jQuery);
</script>
@endsection
