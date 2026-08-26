@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $category->name !!} @endsection
@section('page-title') {!! $category->name !!} @endsection

@section('content')
{{-- Page Banner --}}
<div style="background:var(--tr-bark);padding:28px 0;border-bottom:2px solid var(--tr-olive);">
    <div class="container">
        <h1 style="font-size:28px;font-weight:900;color:#fff;margin-bottom:6px;">{!! $category->name !!}</h1>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.6);">
            <a href="{{ theme_home_url() }}" style="color:var(--tr-sand);font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;"></i>
            <a href="{{ theme_shop_url() }}" style="color:var(--tr-sand);font-weight:600;">{{ __('Shop') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;"></i>
            <span>{!! $category->name !!}</span>
        </div>
    </div>
</div>



<div class="container" style="padding:32px 0 72px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">
            <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;margin-bottom:16px;">
                <div style="background:var(--tr-bark);padding:14px 20px;font-size:14px;font-weight:700;color:#fff;display:flex;align-items:center;gap:8px;">
                    <i class="mdi mdi-filter-outline"></i> {{ __('Filter Gear') }}
                </div>

                {{-- Search --}}
                <div style="padding:16px 20px;border-bottom:1px solid var(--tr-border);">
                    <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--tr-olive);margin-bottom:12px;">{{ __('Search') }}</div>
                    <div style="display:flex;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;">
                        <input type="text" id="tc-search-input" placeholder="{{ __('Search products…') }}"
                            style="flex:1;border:none;outline:none;padding:7px 10px;font-size:13px;">
                        <button type="button" onclick="tcFilterRequest()"
                            style="background:var(--tr-olive);color:#fff;border:none;padding:0 12px;cursor:pointer;">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                    </div>
                </div>

                @if(isset($categories) && $categories->isNotEmpty())
                <div style="padding:16px 20px;border-bottom:1px solid var(--tr-border);">
                    <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--tr-olive);margin-bottom:12px;">{{ __('Activity') }}</div>
                    @foreach($categories as $cat)
                    <label style="display:flex;align-items:center;gap:8px;margin-bottom:10px;cursor:pointer;font-size:13px;color:var(--tr-bark);">
                        <input type="checkbox" class="tc-cat-check" data-slug="{{ $cat->slug }}" data-value="{{ $cat->name }}"
                            style="accent-color:var(--tr-olive);width:15px;height:15px;"
                            {{ ($cat->slug === ($category->slug ?? '')) ? 'checked' : '' }}>
                        {{ $cat->name }}
                        <span style="margin-left:auto;font-size:11px;color:var(--tr-stone);">{{ $cat->product_count ?? '' }}</span>
                    </label>
                    @endforeach
                </div>
                @endif

                <div style="padding:16px 20px;border-bottom:1px solid var(--tr-border);">
                    <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--tr-olive);margin-bottom:12px;">{{ __('Price Range') }}</div>
                    <div style="display:flex;gap:8px;align-items:center;margin-bottom:12px;">
                        <input type="number" id="tc-min-price" placeholder="{{ __('Min') }}" value="0" min="0"
                               style="width:100%;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);padding:7px 10px;font-size:13px;outline:none;">
                        <span style="color:var(--tr-stone);">–</span>
                        <input type="number" id="tc-max-price" placeholder="{{ __('Max') }}" value="10000" min="0"
                               style="width:100%;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);padding:7px 10px;font-size:13px;outline:none;">
                    </div>
                    <button class="tr-btn tr-btn-primary" style="width:100%;justify-content:center;font-size:13px;padding:9px;" onclick="tcFilterRequest()">{{ __('Apply') }}</button>
                </div>

                <div style="padding:16px 20px;">
                    <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--tr-olive);margin-bottom:12px;">{{ __('Rating') }}</div>
                    @foreach([5,4,3] as $r)
                    <label style="display:flex;align-items:center;gap:8px;margin-bottom:8px;cursor:pointer;font-size:13px;">
                        <input type="radio" name="tc_rating" class="tc-rating-filter" value="{{ $r }}" style="accent-color:var(--tr-olive);">
                        <span style="color:#FDD835;">{{ str_repeat('★',$r) }}</span><span style="color:#ccc;">{{ str_repeat('★',5-$r) }}</span>
                    </label>
                    @endforeach
                    <div style="margin-top:8px;">
                        <a href="javascript:void(0)" onclick="$('.tc-rating-filter,.tc-cat-check').prop('checked',false);tcFilterRequest();"
                            style="font-size:12px;color:var(--tr-stone);text-decoration:none;">
                            <i class="mdi mdi-close-circle-outline"></i> {{ __('Clear Filters') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Products --}}
        <div class="col-lg-9">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding:12px 16px;background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);">
                <span class="showing-results" style="font-size:13px;color:var(--tr-stone);">
                    {{ __('Showing') }} <strong style="color:var(--tr-bark);">{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong style="color:var(--tr-bark);">{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <select id="tc-sort-select" style="border:0;font-size:13px;outline:none;cursor:pointer;color:var(--tr-bark);">
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
    function tcFilterRequest(page) {
        page = page || null;
        var cats = [];
        $('.tc-cat-check:checked').each(function () { cats.push($(this).data('slug')); });
        var category_slug = cats.join(',') || null;
        var rating    = $('input[name=tc_rating]:checked').val() || null;
        var min_price = $('#tc-min-price').val() || 0;
        var max_price = $('#tc-max-price').val() || 10000;
        var sort      = $('#tc-sort-select').val();
        var search    = $('#tc-search-input').val() || null;

        $.ajax({
            type: 'GET', url: '{{ theme_shop_filter_url() }}',
            data: { category: category_slug, rating: rating, min_price: min_price, max_price: max_price, sort: sort, page: page, search: search },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                $('.grid-product-list').html(data.grid);
                var p = data.pagination;
                $('.showing-results').html('{{ __("Showing") }} <strong style="color:var(--tr-bark);">' + (p.to ? p.from + '-' + p.to : 0) + '</strong> {{ __("of") }} <strong style="color:var(--tr-bark);">' + (p.total || 0) + '</strong> {{ __("results") }}');
                setTimeout(function () { $('.loader').hide(); }, 500);
            },
            error: function () { $('.loader').hide(); }
        });
    }

    window.tcFilterRequest = tcFilterRequest;

    $(document).on('click', '.tc-cat-nav-btn', function () {
        var slug = $(this).data('slug');
        $('.tc-cat-nav-btn').removeClass('active');
        $(this).addClass('active');
        $('.tc-cat-check').prop('checked', false);
        if (slug) { $('.tc-cat-check[data-slug="' + slug + '"]').prop('checked', true); }
        tcFilterRequest();
    });

    $(document).on('change', '.tc-cat-check', function () { tcFilterRequest(); });
    $('#tc-sort-select').on('change', function () { tcFilterRequest(); });
    $(document).on('change', '.tc-rating-filter', function () { tcFilterRequest(); });

    $(document).on('keydown', '#tc-search-input', function (e) {
        if (e.key === 'Enter') { tcFilterRequest(); }
    });

    // .add-to-wishlist-btn and .add-to-cart-btn handled globally in footer.blade.php
});
</script>
@endsection
