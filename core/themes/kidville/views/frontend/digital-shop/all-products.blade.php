@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title !!} @endsection

@section('style')
<style>
@keyframes kv-spin { to { transform: rotate(360deg); } }
.kv-dp-tag-filter.active { background: var(--kv-red) !important; color: #fff !important; border-color: var(--kv-red) !important; }
</style>
@endsection

@section('content')
<div class="kv-page-banner">
    <div class="container kv-page-banner-content">
        <h1>{!! $page_post->title !!}</h1>
        <div class="kv-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{!! $page_post->title !!}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:36px;padding-bottom:72px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div class="kv-sidebar-card">
                <div class="kv-sidebar-title">{{ __('Search') }}</div>
                <div class="d-flex gap-2">
                    <input type="text" id="kv-dp-search" class="kv-price-input flex-grow-1"
                           placeholder="{{ __('Search products…') }}">
                    <button onclick="kvDpFilter()" class="kv-btn kv-btn-red kv-btn-sm" style="white-space:nowrap;">
                        <i class="las la-search"></i>
                    </button>
                </div>
            </div>

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div class="kv-sidebar-card">
                <div class="kv-sidebar-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach($categories as $cat)
                    <label class="kv-filter-item">
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                            <input type="checkbox" class="kv-dp-cat-check" data-slug="{{ $cat->slug }}"
                                   style="accent-color:var(--kv-red);">
                            {{ $cat->name }}
                        </label>
                        @if($cat->product_count ?? false)
                            <span class="kv-filter-count">{{ $cat->product_count }}</span>
                        @endif
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Authors --}}
            @if($authors->isNotEmpty())
            <div class="kv-sidebar-card">
                <div class="kv-sidebar-title">{{ __('Authors') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach($authors as $author)
                    <label class="kv-filter-item">
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                            <input type="radio" name="kv_dp_author" value="{{ $author->slug }}"
                                   style="accent-color:var(--kv-red);">
                            {{ $author->name }}
                        </label>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Languages --}}
            @if($languages->isNotEmpty())
            <div class="kv-sidebar-card">
                <div class="kv-sidebar-title">{{ __('Language') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach($languages as $lang)
                    <label class="kv-filter-item">
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                            <input type="radio" name="kv_dp_lang" value="{{ $lang->slug }}"
                                   style="accent-color:var(--kv-red);">
                            {{ $lang->name }}
                        </label>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Price Range --}}
            <div class="kv-sidebar-card">
                <div class="kv-sidebar-title">{{ __('Price Range') }}</div>
                <div class="kv-price-inputs d-flex gap-2 mb-3">
                    <input type="number" id="kv-dp-min" class="kv-price-input" placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" id="kv-dp-max" class="kv-price-input" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button onclick="kvDpFilter()" class="kv-btn kv-btn-red w-100" style="justify-content:center;">
                    {{ __('Apply') }}
                </button>
            </div>

            {{-- Tags --}}
            @if($tags->isNotEmpty())
            <div class="kv-sidebar-card">
                <div class="kv-sidebar-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($tags->take(15) as $tag)
                    <button class="kv-tag kv-dp-tag-filter" data-tag="{{ $tag->tag_name }}">
                        #{{ $tag->tag_name }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            <button onclick="kvDpClear()" class="kv-btn kv-btn-outline w-100 mt-2" style="justify-content:center;">
                <i class="las la-times-circle"></i> {{ __('Clear Filters') }}
            </button>

        </div>

        {{-- Products area --}}
        <div class="col-lg-9">

            {{-- Sort bar --}}
            <div class="kv-sort-bar" style="background:#fff;border:2.5px solid var(--kv-border);border-radius:var(--kv-radius);padding:12px 18px;display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:22px;">
                <span class="kv-dp-showing" style="font-size:13px;font-weight:700;color:var(--kv-muted);">
                    {{ __('Showing') }} <strong style="color:var(--kv-dark);">{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong style="color:var(--kv-dark);">{{ $pagination->total() }}</strong> {{ __('products') }}
                </span>
                <div class="d-flex align-items-center gap-2">
                    <label style="font-size:12px;color:var(--kv-muted);font-weight:700;">{{ __('Sort:') }}</label>
                    <select id="kv-dp-sort" class="kv-price-input" style="width:auto;padding:8px 14px;">
                        <option value="3">{{ __('Latest') }}</option>
                        <option value="1">{{ __('Name') }}</option>
                        <option value="4">{{ __('Price: Low → High') }}</option>
                        <option value="5">{{ __('Price: High → Low') }}</option>
                    </select>
                </div>
            </div>

            {{-- Loader --}}
            <div class="kv-dp-loader" style="display:none;text-align:center;padding:40px;">
                <div style="width:40px;height:40px;border:4px solid var(--kv-border);border-top-color:var(--kv-red);border-radius:50%;animation:kv-spin .7s linear infinite;margin:0 auto;"></div>
            </div>

            {{-- Grid --}}
            <div class="row g-3 kv-dp-grid">
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

    function kvDpFilter(page) {
        var cats = [];
        $('.kv-dp-cat-check:checked').each(function () { cats.push($(this).data('slug')); });

        var data = {
            category:  cats.join(',') || null,
            author:    $('input[name=kv_dp_author]:checked').val() || null,
            language:  $('input[name=kv_dp_lang]:checked').val() || null,
            tag:       $('.kv-dp-tag-filter.active').data('tag') || null,
            min_price: $('#kv-dp-min').val() || 0,
            max_price: $('#kv-dp-max').val() || 10000,
            sort:      $('#kv-dp-sort').val(),
            search:    $('#kv-dp-search').val() || null,
            page:      page || null,
        };

        $.ajax({
            type: 'GET',
            url: '{{ theme_digital_shop_filter_url() }}',
            data: data,
            beforeSend: function () { $('.kv-dp-loader').show(); $('.kv-dp-grid').css('opacity', '.4'); },
            success: function (res) {
                $('.kv-dp-grid').html(res.grid).css('opacity', '1');
                var p = res.pagination;
                var from = p.from ?? 0, total = p.total ?? 0;
                $('.kv-dp-showing').html('{{ __('Showing') }} <strong style="color:var(--kv-dark);">' + from + '</strong> {{ __('of') }} <strong style="color:var(--kv-dark);">' + total + '</strong> {{ __('products') }}');
                $('.kv-dp-loader').hide();
            },
            error: function () { $('.kv-dp-loader').hide(); $('.kv-dp-grid').css('opacity', '1'); }
        });
    }

    function kvDpClear() {
        $('.kv-dp-cat-check').prop('checked', false);
        $('input[name=kv_dp_author]').prop('checked', false);
        $('input[name=kv_dp_lang]').prop('checked', false);
        $('.kv-dp-tag-filter').removeClass('active');
        $('#kv-dp-min').val(0);
        $('#kv-dp-max').val(10000);
        $('#kv-dp-search').val('');
        kvDpFilter();
    }

    window.kvDpFilter = kvDpFilter;
    window.kvDpClear  = kvDpClear;

    $(document).on('change', '.kv-dp-cat-check, input[name=kv_dp_author], input[name=kv_dp_lang]', function () { kvDpFilter(); });
    $('#kv-dp-sort').on('change', function () { kvDpFilter(); });

    $(document).on('click', '.kv-dp-tag-filter', function () {
        var active = $(this).hasClass('active');
        $('.kv-dp-tag-filter').removeClass('active');
        if (!active) { $(this).addClass('active'); }
        kvDpFilter();
    });

    $(document).on('click', '.kv-dp-page-btn', function (e) {
        e.preventDefault();
        kvDpFilter($(this).data('page'));
    });

    $(document).on('keyup', '#kv-dp-search', function () {
        clearTimeout(window._kvDpTimer);
        window._kvDpTimer = setTimeout(kvDpFilter, 350);
    });

})(jQuery);
</script>
@endsection
