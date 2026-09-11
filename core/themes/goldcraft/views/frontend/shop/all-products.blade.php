@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title !!} @endsection

@section('content')
{{-- Page Banner --}}
<div style="background:var(--gc-warm);border-bottom:1px solid var(--gc-border);padding:36px 0 28px;">
    <div class="container">
        <h1 style="font-size:13px;font-weight:400;letter-spacing:4px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:8px;">{!! $page_post->title !!}</h1>
        <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:var(--gc-muted);font-style:italic;">
            <a href="{{ theme_home_url() }}" style="color:var(--gc-rose);text-decoration:none;">{{ __('Home') }}</a>
            <span>—</span>
            <span>{!! $page_post->title !!}</span>
        </div>
    </div>
</div>

{{-- Category Nav --}}
@if($categories->isNotEmpty())
<div style="background:var(--gc-ivory);border-bottom:1px solid var(--gc-border);">
    <div class="container">
        <div style="display:flex;align-items:center;gap:8px;padding:12px 0;overflow-x:auto;flex-wrap:nowrap;">
            <button class="gc-cat-nav-btn active" data-slug=""
                    style="flex-shrink:0;padding:6px 18px;border-radius:var(--gc-radius);border:1.5px solid var(--gc-rose);background:var(--gc-rose);color:#fff;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:all .2s;font-style:normal;">
                {{ __('All') }}
            </button>
            @foreach($categories as $cat)
            <button class="gc-cat-nav-btn" data-slug="{{ $cat->slug }}"
                    style="flex-shrink:0;padding:6px 18px;border-radius:var(--gc-radius);border:1.5px solid var(--gc-border);background:transparent;color:var(--gc-muted);font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:all .2s;font-style:italic;">
                {{ $cat->name }}
            </button>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Shop Layout --}}
<div class="container" style="padding:40px 0 80px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            @if($categories->isNotEmpty())
            <div style="background:var(--gc-warm);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:24px;margin-bottom:20px;box-shadow:var(--gc-shadow);">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-muted);margin-bottom:16px;">{{ __('Categories') }}</div>
                @foreach($categories as $cat)
                <div style="padding:8px 0;border-bottom:1px dashed var(--gc-border);display:flex;justify-content:space-between;align-items:center;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--gc-dark);font-style:italic;">
                        <input type="checkbox" class="gc-cat-checkbox-input" data-slug="{{ $cat->slug }}" data-value="{{ $cat->name }}"
                               style="accent-color:var(--gc-rose);width:14px;height:14px;">
                        {{ $cat->name }}
                    </label>
                    <span style="font-size:11px;color:var(--gc-muted);">{{ $cat->product_count ?? '' }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <div style="background:var(--gc-warm);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:24px;margin-bottom:20px;box-shadow:var(--gc-shadow);">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-muted);margin-bottom:16px;">{{ __('Price Range') }}</div>
                <div style="display:flex;gap:10px;margin-bottom:12px;">
                    <input type="number" id="gc-min-price" placeholder="{{ __('Min') }}" value="0" min="0"
                           style="width:50%;padding:8px 12px;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);font-size:13px;outline:none;font-family:Georgia,serif;background:#fff;">
                    <input type="number" id="gc-max-price" placeholder="{{ __('Max') }}" value="10000" min="0"
                           style="width:50%;padding:8px 12px;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);font-size:13px;outline:none;font-family:Georgia,serif;background:#fff;">
                </div>
                <button onclick="gcFilterRequest()"
                        style="width:100%;padding:10px;background:var(--gc-rose);color:#fff;border:none;border-radius:var(--gc-radius);font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:background .2s;"
                        onmouseover="this.style.background='var(--gc-rose-d)'" onmouseout="this.style.background='var(--gc-rose)'">
                    {{ __('Apply Filter') }}
                </button>
            </div>

            <div style="background:var(--gc-warm);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:24px;box-shadow:var(--gc-shadow);">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-muted);margin-bottom:16px;">{{ __('Rating') }}</div>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px;">
                        <input type="radio" name="gc_rating" class="gc-rating-filter" value="5" style="accent-color:var(--gc-rose);"> <span style="color:#C8A96E;">★★★★★</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px;">
                        <input type="radio" name="gc_rating" class="gc-rating-filter" value="4" style="accent-color:var(--gc-rose);"> <span><span style="color:#C8A96E;">★★★★</span><span style="color:#ccc;">★</span></span>
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px;">
                        <input type="radio" name="gc_rating" class="gc-rating-filter" value="3" style="accent-color:var(--gc-rose);"> <span><span style="color:#C8A96E;">★★★</span><span style="color:#ccc;">★★</span></span>
                    </label>
                </div>
            </div>

        </div>

        {{-- Products --}}
        <div class="col-lg-9">

            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
                <span class="showing-results" style="font-size:13px;color:var(--gc-muted);font-style:italic;">
                    {{ __('Showing') }} <em>{{ $pagination->count() }}</em> {{ __('of') }} <em>{{ $pagination->total() }}</em> {{ __('pieces') }}
                </span>
                <select id="gc-sort-select"
                        style="padding:8px 16px;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);font-size:13px;font-family:Georgia,serif;background:var(--gc-warm);color:var(--gc-dark);cursor:pointer;outline:none;">
                    <option value="3">{{ __('Sort: Latest') }}</option>
                    <option value="1">{{ __('Sort: Name') }}</option>
                    <option value="2">{{ __('Sort: Popular') }}</option>
                    <option value="4">{{ __('Price: Low to High') }}</option>
                    <option value="5">{{ __('Price: High to Low') }}</option>
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
    function gcFilterRequest(page) {
        page = page || null;
        var cats = [];
        $('.gc-cat-checkbox-input:checked').each(function () { cats.push($(this).data('slug')); });
        var category_slug = cats.join(',') || null;
        var rating    = $('input[name=gc_rating]:checked').val() || null;
        var min_price = $('#gc-min-price').val() || 0;
        var max_price = $('#gc-max-price').val() || 10000;
        var sort      = $('#gc-sort-select').val();

        var reqData = { min_price: min_price, max_price: max_price, sort: sort };
        if (category_slug) reqData.category = category_slug;
        if (rating)        reqData.rating   = rating;
        if (page)          reqData.page     = page;

        $.ajax({
            type: 'GET', url: '{{ theme_shop_filter_url() }}',
            data: reqData,
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                $('.grid-product-list').html(data.grid);
                var p = data.pagination;
                $('.showing-results').html('{{ __('Showing') }} <em>' + (p.to ? p.from + '-' + p.to : 0) + '</em> {{ __('of') }} <em>' + (p.total || 0) + '</em> {{ __('pieces') }}');
                setTimeout(function () { $('.loader').hide(); }, 500);
            },
            error: function () { $('.loader').hide(); }
        });
    }

    window.gcFilterRequest = gcFilterRequest;

    $(document).on('click', '.gc-cat-nav-btn', function () {
        $('.gc-cat-nav-btn').css({'background':'transparent','color':'var(--gc-muted)','border-color':'var(--gc-border)'});
        $(this).css({'background':'var(--gc-rose)','color':'#fff','border-color':'var(--gc-rose)'});
        var slug = $(this).data('slug');
        $('.gc-cat-checkbox-input').prop('checked', false);
        if (slug) { $('.gc-cat-checkbox-input[data-slug="' + slug + '"]').prop('checked', true); }
        gcFilterRequest();
    });

    $(document).on('change', '.gc-cat-checkbox-input', function () { gcFilterRequest(); });
    $('#gc-sort-select').on('change', function () { gcFilterRequest(); });
    $(document).on('change', '.gc-rating-filter', function () { gcFilterRequest(); });

        // .add-to-wishlist-btn handled globally in footer.blade.php

        // .add-to-cart-btn handled globally in footer.blade.php
});
</script>
@endsection
