@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title !!} @endsection
@section('page-title') {!! $page_post->title !!} @endsection

@section('content')
<div class="fp-page-hero">
    <div class="container">
        <h1 class="fp-page-title">{!! $page_post->title !!}</h1>
        <ul class="fp-breadcrumb-list">
            <li><a href="{{ theme_home_url() }}">{{ __('Home') }}</a></li>
            <li>{!! $page_post->title !!}</li>
        </ul>
    </div>
</div>

<div class="container" style="padding:36px 0 72px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div class="fp-sidebar-card">
                <div class="fp-sidebar-title">{{ __('Search') }}</div>
                <div style="position:relative;">
                    <input type="text" id="fp-dp-search" class="fp-price-input" style="width:100%;padding-right:40px;" placeholder="{{ __('Search products…') }}">
                    <button type="button" onclick="fpDpFilter()" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--fp-green);font-size:18px;">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                </div>
            </div>

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div class="fp-sidebar-card">
                <div class="fp-sidebar-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach($categories as $cat)
                    <label class="fp-filter-check" style="cursor:pointer;">
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                            <input type="checkbox" class="fp-dp-cat" data-slug="{{ $cat->slug }}" style="accent-color:var(--fp-green);"> {{ $cat->name }}
                        </label>
                        @if($cat->product_count ?? false)
                            <span class="fp-filter-count">{{ $cat->product_count }}</span>
                        @endif
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Authors --}}
            @if($authors->isNotEmpty())
            <div class="fp-sidebar-card">
                <div class="fp-sidebar-title">{{ __('Authors') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach($authors as $author)
                    <div style="margin-bottom:8px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--fp-text);">
                            <input type="radio" name="fp_dp_author" class="fp-dp-author" value="{{ $author->slug }}" style="accent-color:var(--fp-green);"> {{ $author->name }}
                        </label>
                    </div>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Languages --}}
            @if($languages->isNotEmpty())
            <div class="fp-sidebar-card">
                <div class="fp-sidebar-title">{{ __('Language') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach($languages as $lang)
                    <div style="margin-bottom:8px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--fp-text);">
                            <input type="radio" name="fp_dp_lang" class="fp-dp-lang" value="{{ $lang->slug }}" style="accent-color:var(--fp-green);"> {{ $lang->name }}
                        </label>
                    </div>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Price --}}
            <div class="fp-sidebar-card">
                <div class="fp-sidebar-title">{{ __('Price Range') }}</div>
                <div class="fp-price-inputs">
                    <input type="number" id="fp-dp-min" class="fp-price-input" placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" id="fp-dp-max" class="fp-price-input" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button class="fp-apply-btn mt-3" onclick="fpDpFilter()">{{ __('Apply Filter') }}</button>
            </div>

            {{-- Tags --}}
            @if($tags->isNotEmpty())
            <div class="fp-sidebar-card">
                <div class="fp-sidebar-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($tags->take(15) as $tag)
                        <a href="javascript:void(0)" class="fp-dp-tag"
                           data-tag="{{ $tag->tag_name }}"
                           style="font-size:12px;font-family:var(--fp-font-head);color:var(--fp-muted);border:1px solid var(--fp-border);padding:3px 10px;border-radius:var(--fp-radius);text-transform:uppercase;letter-spacing:1px;transition:all .2s;">
                            #{{ $tag->tag_name }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div style="text-align:center;margin-top:8px;">
                <a href="javascript:void(0)" onclick="fpDpClear()" style="font-size:12px;color:var(--fp-muted);font-family:var(--fp-font-head);text-transform:uppercase;letter-spacing:1px;">
                    <i class="mdi mdi-close-circle-outline"></i> {{ __('Clear Filters') }}
                </a>
            </div>
        </div>

        {{-- Products --}}
        <div class="col-lg-9">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
                <span class="fp-sort-count showing-results">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('products') }}
                </span>
                <select id="fp-dp-sort" class="fp-select-box">
                    <option value="3">{{ __('Latest') }}</option>
                    <option value="1">{{ __('Name A-Z') }}</option>
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

    function fpDpFilter(page) {
        var cats = [];
        $('.fp-dp-cat:checked').each(function () { cats.push($(this).data('slug')); });
        $.ajax({
            type: 'GET',
            url: '{{ theme_digital_shop_filter_url() }}',
            data: {
                category:  cats.join(',') || null,
                author:    $('input[name=fp_dp_author]:checked').val() || null,
                language:  $('input[name=fp_dp_lang]:checked').val() || null,
                tag:       $('.fp-dp-tag.active').data('tag') || null,
                min_price: $('#fp-dp-min').val() || 0,
                max_price: $('#fp-dp-max').val() || 10000,
                sort:      $('#fp-dp-sort').val(),
                page:      page || null,
                search:    $('#fp-dp-search').val() || null,
            },
            beforeSend: function () { $('.loader').show(); },
            success: function (res) {
                $('.grid-product-list').html(res.grid);
                var p = res.pagination;
                $('.showing-results').html('{{ __("Showing") }} <strong>' + (p.from ?? 0) + '</strong> {{ __("of") }} <strong>' + (p.total ?? 0) + '</strong> {{ __("products") }}');
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    }

    function fpDpClear() {
        $('.fp-dp-cat').prop('checked', false);
        $('input[name=fp_dp_author]').prop('checked', false);
        $('input[name=fp_dp_lang]').prop('checked', false);
        $('.fp-dp-tag').removeClass('active');
        $('#fp-dp-min').val(0); $('#fp-dp-max').val(10000); $('#fp-dp-search').val('');
        fpDpFilter();
    }

    window.fpDpFilter = fpDpFilter;
    window.fpDpClear  = fpDpClear;

    $(document).on('change', '.fp-dp-cat, input[name=fp_dp_author], input[name=fp_dp_lang]', function () { fpDpFilter(); });
    $('#fp-dp-sort').on('change', function () { fpDpFilter(); });

    $(document).on('click', '.fp-dp-tag', function () {
        var isActive = $(this).hasClass('active');
        $('.fp-dp-tag').removeClass('active').css({'color':'var(--fp-muted)','border-color':'var(--fp-border)'});
        if (!isActive) { $(this).addClass('active').css({'color':'var(--fp-green)','border-color':'var(--fp-green)'}); }
        fpDpFilter();
    });
})(jQuery);
</script>
@endsection
