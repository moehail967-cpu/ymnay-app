@extends('tenant.frontend.frontend-page-master')

@section('title') {{ $category->name }} @endsection
@section('page-title') {{ $category->name }} @endsection

@section('content')
{{-- Page Banner --}}
<div style="background:var(--gl-gold-pale);border-bottom:1px solid var(--gl-border);padding:36px 0 28px;">
    <div class="container">
        <h1 style="font-size:26px;font-weight:300;color:var(--gl-dark);margin-bottom:10px;letter-spacing:-.3px;">{{ $category->name }}</h1>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gl-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="opacity:.5;"></i>
            <span style="color:var(--gl-dark);">{{ $category->name }}</span>
        </div>
    </div>
</div>

{{-- Category Nav --}}
@if($categories->isNotEmpty())
<div style="background:#fff;border-bottom:1px solid var(--gl-border);">
    <div class="container">
        <div style="display:flex;align-items:center;gap:8px;padding:12px 0;overflow-x:auto;flex-wrap:nowrap;">
            <button class="gl-cat-nav-btn {{ !isset($category) ? 'active' : '' }}" data-slug=""
                    style="flex-shrink:0;padding:7px 18px;border-radius:50px;border:1.5px solid var(--gl-border);background:{{ !isset($category) ? 'var(--gl-dark)' : 'transparent' }};color:{{ !isset($category) ? '#fff' : 'var(--gl-muted)' }};font-size:12px;font-weight:600;letter-spacing:.5px;cursor:pointer;transition:all .2s;">
                {{ __('All') }}
            </button>
            @foreach($categories as $cat)
            <button class="gl-cat-nav-btn {{ isset($category) && $category->slug == $cat->slug ? 'active' : '' }}" data-slug="{{ $cat->slug }}"
                    style="flex-shrink:0;padding:7px 18px;border-radius:50px;border:1.5px solid var(--gl-border);background:{{ isset($category) && $category->slug == $cat->slug ? 'var(--gl-dark)' : 'transparent' }};color:{{ isset($category) && $category->slug == $cat->slug ? '#fff' : 'var(--gl-muted)' }};font-size:12px;font-weight:600;letter-spacing:.5px;cursor:pointer;transition:all .2s;">
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
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:24px;margin-bottom:20px;box-shadow:var(--gl-shadow);">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:16px;">{{ __('Categories') }}</div>
                @foreach($categories as $cat)
                <div style="padding:8px 0;border-bottom:1px dashed var(--gl-border);display:flex;justify-content:space-between;align-items:center;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--gl-dark);">
                        <input type="checkbox" class="gl-cat-checkbox-input" data-slug="{{ $cat->slug }}" data-value="{{ $cat->name }}"
                               style="accent-color:var(--gl-gold);width:14px;height:14px;" {{ isset($category) && $category->slug == $cat->slug ? 'checked' : '' }}>
                        {{ $cat->name }}
                    </label>
                    <span style="font-size:11px;color:var(--gl-muted);">{{ $cat->product_count ?? '' }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:24px;margin-bottom:20px;box-shadow:var(--gl-shadow);">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:16px;">{{ __('Price Range') }}</div>
                <div style="display:flex;gap:10px;margin-bottom:12px;">
                    <input type="number" id="gl-min-price" placeholder="{{ __('Min') }}" value="0" min="0"
                           style="width:50%;padding:8px 12px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:13px;outline:none;font-family:inherit;">
                    <input type="number" id="gl-max-price" placeholder="{{ __('Max') }}" value="10000" min="0"
                           style="width:50%;padding:8px 12px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:13px;outline:none;font-family:inherit;">
                </div>
                <button onclick="glFilterRequest()"
                        style="width:100%;padding:10px;background:var(--gl-dark);color:#fff;border:none;border-radius:var(--gl-radius);font-size:12px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;transition:background .2s;"
                        onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                    {{ __('Apply Filter') }}
                </button>
            </div>

            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:24px;box-shadow:var(--gl-shadow);">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:16px;">{{ __('Rating') }}</div>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px;">
                        <input type="radio" name="gl_rating" class="gl-rating-filter" value="5" style="accent-color:var(--gl-gold);"> <span style="color:#F1C40F;">★★★★★</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px;">
                        <input type="radio" name="gl_rating" class="gl-rating-filter" value="4" style="accent-color:var(--gl-gold);"> <span><span style="color:#F1C40F;">★★★★</span><span style="color:#ccc;">★</span></span>
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px;">
                        <input type="radio" name="gl_rating" class="gl-rating-filter" value="3" style="accent-color:var(--gl-gold);"> <span><span style="color:#F1C40F;">★★★</span><span style="color:#ccc;">★★</span></span>
                    </label>
                </div>
            </div>

        </div>

        {{-- Products --}}
        <div class="col-lg-9">

            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
                <span class="showing-results" style="font-size:13px;color:var(--gl-muted);">
                    {{ __('Showing') }} <strong style="color:var(--gl-dark);">{{ $pagination->count() }}</strong> {{ __('of') }} <strong style="color:var(--gl-dark);">{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <select id="gl-sort-select"
                        style="padding:8px 16px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:13px;font-family:inherit;background:#fff;color:var(--gl-dark);cursor:pointer;outline:none;">
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
    function glFilterRequest(page) {
        page = page || null;
        var cats = [];
        $('.gl-cat-checkbox-input:checked').each(function () { cats.push($(this).data('slug')); });
        var category_slug = cats.join(',') || null;
        var rating    = $('input[name=gl_rating]:checked').val() || null;
        var min_price = $('#gl-min-price').val() || 0;
        var max_price = $('#gl-max-price').val() || 10000;
        var sort      = $('#gl-sort-select').val();

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
                $('.showing-results').html('{{ __('Showing') }} <strong style="color:var(--gl-dark);">' + (p.to ? p.from + '-' + p.to : 0) + '</strong> {{ __('of') }} <strong style="color:var(--gl-dark);">' + (p.total || 0) + '</strong> {{ __('results') }}');
                setTimeout(function () { $('.loader').hide(); }, 500);
            },
            error: function () { $('.loader').hide(); }
        });
    }

    window.glFilterRequest = glFilterRequest;

    $(document).on('click', '.gl-cat-nav-btn', function () {
        $('.gl-cat-nav-btn').css({'background':'transparent','color':'var(--gl-muted)'});
        $(this).css({'background':'var(--gl-dark)','color':'#fff'});
        var slug = $(this).data('slug');
        $('.gl-cat-checkbox-input').prop('checked', false);
        if (slug) { $('.gl-cat-checkbox-input[data-slug="' + slug + '"]').prop('checked', true); }
        glFilterRequest();
    });

    $(document).on('change', '.gl-cat-checkbox-input', function () { glFilterRequest(); });
    $('#gl-sort-select').on('change', function () { glFilterRequest(); });
    $(document).on('change', '.gl-rating-filter', function () { glFilterRequest(); });

});
</script>
@endsection
