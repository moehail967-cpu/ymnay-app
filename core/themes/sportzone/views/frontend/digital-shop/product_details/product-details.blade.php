@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $product->name !!} @endsection
@section('page-title') {!! $product->name !!} @endsection

@section('meta-data') {!! render_page_meta_data($product) !!} @endsection

@php
    $price_data    = get_digital_product_dynamic_price($product);
    $sale_price    = $price_data['sale_price'];
    $regular_price = $price_data['regular_price'];
    $discount      = $price_data['discount'];
    $img_data      = get_attachment_image_by_id($product->image_id ?? null, 'full');
    $img_url       = $img_data['img_url'] ?? null;
    $category      = $product->category;
    $author_name   = $product->additionalFields?->author?->name ?? null;
    $avg_rating    = $reviews->isNotEmpty() ? round($reviews->avg('rating'), 1) : 0;
@endphp

@section('content')
{{-- Page Banner --}}
<div class="sz-page-banner">
    <div class="container">
        <h1>{{ \Illuminate\Support\Str::words($product->name, 6) }}</h1>
        <div class="sz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
            <a href="{{ theme_digital_shop_url() }}">{{ __('Digital Shop') }}</a>
            <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ \Illuminate\Support\Str::words($product->name, 4) }}</span>
        </div>
    </div>
</div>

{{-- Product Section --}}
<section style="background:var(--sz-bg);padding:48px 0 64px;">
<div class="container">
    <div class="row g-4">

        {{-- Cover Image (sticky) --}}
        <div class="col-lg-3 col-md-4">
            <div style="position:sticky;top:100px;">
                @if($img_url)
                    <img src="{{ $img_url }}" alt="{{ $product->name }}"
                         style="width:100%;aspect-ratio:3/4;object-fit:cover;border-radius:var(--sz-radius-xl);border:2px solid var(--sz-border);box-shadow:0 8px 32px rgba(13,27,62,.12);">
                @else
                    <div style="width:100%;aspect-ratio:3/4;background:#fff;border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);display:flex;align-items:center;justify-content:center;box-shadow:0 8px 32px rgba(13,27,62,.12);">
                        <i class="mdi mdi-file-download-outline" style="font-size:72px;color:var(--sz-border);"></i>
                    </div>
                @endif
            </div>
        </div>

        {{-- Product Info --}}
        <div class="col-lg-6 col-md-8">
            @if($category)
                <div style="font-family:var(--sz-font-head);font-size:11px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--sz-red);margin-bottom:10px;">
                    <i class="mdi mdi-tag-outline"></i> {{ $category->name }}
                </div>
            @endif

            <h1 style="font-family:var(--sz-font-head);font-size:clamp(20px,3vw,30px);font-weight:700;color:var(--sz-dark);text-transform:uppercase;letter-spacing:1px;line-height:1.2;margin-bottom:14px;">
                {!! $product->name !!}
            </h1>

            @if($author_name)
            <div style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--sz-muted);margin-bottom:12px;">
                <i class="mdi mdi-account-circle-outline" style="color:var(--sz-navy);"></i>
                <span>{{ $author_name }}</span>
            </div>
            @endif

            {{-- Star Rating --}}
            @if($avg_rating > 0)
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
                <div style="display:flex;gap:2px;">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="mdi mdi-star{{ $i <= $avg_rating ? '' : ($i - 0.5 <= $avg_rating ? '-half-full' : '-outline') }}"
                           style="font-size:18px;color:{{ $i <= $avg_rating ? '#FDD835' : 'var(--sz-border)' }};"></i>
                    @endfor
                </div>
                <span style="font-size:12px;color:var(--sz-muted);">({{ $reviews->count() }} {{ __('reviews') }})</span>
            </div>
            @endif

            {{-- Price --}}
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:24px;padding-bottom:20px;border-bottom:2px solid var(--sz-border);">
                <span class="sz-price-sale" style="font-size:28px;">{{ amount_with_currency_symbol($sale_price) }}</span>
                @if($discount > 0 && $regular_price)
                    <span class="sz-price-orig" style="font-size:16px;">{{ amount_with_currency_symbol($regular_price) }}</span>
                    <span class="sz-card-badge sz-badge-sale" style="position:static;display:inline-block;font-size:12px;padding:4px 12px;">
                        {{ $discount }}% {{ __('OFF') }}
                    </span>
                @endif
            </div>

            {{-- Specs Grid --}}
            @php
                $specs = [
                    ['key' => 'file_format',        'label' => __('Format'),           'icon' => 'mdi-file-outline',          'format' => 'strtoupper'],
                    ['key' => 'number_of_pages',     'label' => __('Pages'),            'icon' => 'mdi-book-open-page-variant','format' => null],
                    ['key' => 'getLanguage',         'label' => __('Language'),         'icon' => 'mdi-translate',             'format' => null, 'relation' => 'name'],
                    ['key' => 'file_size',           'label' => __('File Size'),        'icon' => 'mdi-harddisk',              'format' => null],
                    ['key' => 'software_version',    'label' => __('Version'),          'icon' => 'mdi-tag-check-outline',     'format' => null],
                ];
                $hasSpecs = false;
                foreach ($specs as $spec) {
                    $field = $product->additionalFields;
                    if (!$field) break;
                    $val = isset($spec['relation']) ? ($field->{$spec['key']}?->{$spec['relation']} ?? null) : ($field->{$spec['key']} ?? null);
                    if ($val) { $hasSpecs = true; break; }
                }
            @endphp
            @if($hasSpecs || ($product->downloads_count ?? 0))
            <div style="background:#fff;border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);padding:20px;margin-bottom:24px;">
                <div style="font-family:var(--sz-font-head);font-size:11px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--sz-muted);margin-bottom:14px;">
                    {{ __('Product Details') }}
                </div>
                <div class="row g-3">
                    @foreach($specs as $spec)
                    @php
                        $field = $product->additionalFields;
                        $val = $field ? (isset($spec['relation']) ? ($field->{$spec['key']}?->{$spec['relation']} ?? null) : ($field->{$spec['key']} ?? null)) : null;
                        if ($spec['format'] === 'strtoupper' && $val) $val = strtoupper($val);
                    @endphp
                    @if($val)
                    <div class="col-6">
                        <div style="display:flex;align-items:flex-start;gap:8px;">
                            <i class="mdi {{ $spec['icon'] }}" style="font-size:18px;color:var(--sz-red);margin-top:2px;flex-shrink:0;"></i>
                            <div>
                                <div style="font-size:10px;font-family:var(--sz-font-head);font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--sz-muted);">{{ $spec['label'] }}</div>
                                <div style="font-size:14px;font-weight:600;color:var(--sz-dark);margin-top:2px;">{{ $val }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach

                    @if($product->downloads_count ?? 0)
                    <div class="col-6">
                        <div style="display:flex;align-items:flex-start;gap:8px;">
                            <i class="mdi mdi-download-circle-outline" style="font-size:18px;color:var(--sz-red);margin-top:2px;flex-shrink:0;"></i>
                            <div>
                                <div style="font-size:10px;font-family:var(--sz-font-head);font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--sz-muted);">{{ __('Downloads') }}</div>
                                <div style="font-size:14px;font-weight:600;color:var(--sz-dark);margin-top:2px;">{{ $product->downloads_count }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Short Description --}}
            @if($product->short_description)
            <div style="font-size:14px;color:var(--sz-muted);line-height:1.8;margin-bottom:18px;">
                {!! $product->short_description !!}
            </div>
            @endif

            {{-- Tags --}}
            @if($product->tag && $product->tag->isNotEmpty())
            <div style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;">
                <span style="font-family:var(--sz-font-head);font-size:11px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--sz-muted);">{{ __('Tags:') }}</span>
                @foreach($product->tag as $tag)
                    <span class="sz-tag">{{ $tag->tag_name }}</span>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Purchase Sidebar (sticky) --}}
        <div class="col-lg-3">
            <div style="background:#fff;border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);padding:24px;position:sticky;top:100px;box-shadow:0 8px 32px rgba(13,27,62,.1);">

                {{-- Price --}}
                <div style="font-family:var(--sz-font-head);font-size:28px;font-weight:700;color:var(--sz-dark);margin-bottom:6px;letter-spacing:1px;">
                    {{ amount_with_currency_symbol($sale_price) }}
                </div>
                @if($discount > 0 && $regular_price)
                <div style="font-size:13px;color:var(--sz-muted);margin-bottom:16px;padding-bottom:16px;border-bottom:2px solid var(--sz-border);">
                    {{ __('Was') }} <span style="text-decoration:line-through;">{{ amount_with_currency_symbol($regular_price) }}</span>
                    — <span style="color:var(--sz-red);font-family:var(--sz-font-head);font-weight:700;letter-spacing:1px;">{{ $discount }}% {{ __('OFF') }}</span>
                </div>
                @else
                <div style="border-bottom:2px solid var(--sz-border);margin-bottom:16px;"></div>
                @endif

                @auth('web')
                {{-- Add to Cart Form --}}
                <form action="{{ theme_digital_product_add_to_cart_url() }}" method="POST" style="margin-bottom:10px;">
                    @csrf
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit"
                            class="sz-btn sz-btn-red w-100"
                            style="justify-content:center;gap:8px;font-size:14px;padding:13px;">
                        <i class="mdi mdi-download" style="font-size:18px;"></i>
                        {{ __('Get Now') }}
                    </button>
                </form>
                @else
                {{-- Login to Purchase --}}
                <a href="{{ theme_login_url() }}"
                   class="sz-btn sz-btn-red w-100"
                   style="display:flex;align-items:center;justify-content:center;gap:8px;font-size:14px;padding:13px;text-decoration:none;margin-bottom:10px;">
                    <i class="mdi mdi-login" style="font-size:18px;"></i>
                    {{ __('Sign In to Purchase') }}
                </a>
                @endauth

                {{-- Wishlist --}}
                <button class="sz-btn sz-btn-outline w-100 add-to-wishlist-btn"
                        data-product_id="{{ $product->id }}"
                        style="display:flex;align-items:center;justify-content:center;gap:8px;font-size:13px;padding:11px;margin-bottom:20px;cursor:pointer;">
                    <i class="mdi mdi-heart-outline" style="font-size:16px;"></i>
                    {{ __('Add to Wishlist') }}
                </button>

                {{-- Trust Badges --}}
                <div style="border-top:2px solid var(--sz-border);padding-top:16px;display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:var(--sz-muted);">
                        <i class="mdi mdi-download-circle-outline" style="font-size:20px;color:var(--sz-red);flex-shrink:0;"></i>
                        <span>{{ __('Instant digital download') }}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:var(--sz-muted);">
                        <i class="mdi mdi-shield-check" style="font-size:20px;color:var(--sz-red);flex-shrink:0;"></i>
                        <span>{{ __('Secure checkout') }}</span>
                    </div>
                    @if($product->refund_policy)
                    <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:var(--sz-muted);">
                        <i class="mdi mdi-undo-variant" style="font-size:20px;color:var(--sz-red);flex-shrink:0;"></i>
                        <span>{{ __('Refund policy available') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
</section>

{{-- Tabs --}}
<div style="background:#fff;border-top:2px solid var(--sz-border);border-bottom:2px solid var(--sz-border);padding:48px 0 64px;">
<div class="container">

    {{-- Tab Nav --}}
    <div style="display:flex;gap:0;border-bottom:3px solid var(--sz-border);margin-bottom:32px;flex-wrap:wrap;">
        <button class="sz-dp-tab-btn active" data-tab="sz-dp-tab-desc"
                style="font-family:var(--sz-font-head);font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:2px;padding:12px 24px;border:none;background:transparent;cursor:pointer;color:var(--sz-red);border-bottom:3px solid var(--sz-red);margin-bottom:-3px;transition:all .2s;">
            {{ __('Description') }}
        </button>
        <button class="sz-dp-tab-btn" data-tab="sz-dp-tab-reviews"
                style="font-family:var(--sz-font-head);font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:2px;padding:12px 24px;border:none;background:transparent;cursor:pointer;color:var(--sz-muted);border-bottom:3px solid transparent;margin-bottom:-3px;transition:all .2s;">
            {{ __('Reviews') }} ({{ $reviews->count() }})
        </button>
        @if($product->refund_policy)
        <button class="sz-dp-tab-btn" data-tab="sz-dp-tab-refund"
                style="font-family:var(--sz-font-head);font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:2px;padding:12px 24px;border:none;background:transparent;cursor:pointer;color:var(--sz-muted);border-bottom:3px solid transparent;margin-bottom:-3px;transition:all .2s;">
            {{ __('Refund Policy') }}
        </button>
        @endif
    </div>

    {{-- Description Panel --}}
    <div id="sz-dp-tab-desc" class="sz-dp-tab-panel" style="display:block;">
        @if($product->description)
            <div style="font-size:14px;color:var(--sz-muted);line-height:1.9;">{!! $product->description !!}</div>
        @else
            <p style="color:var(--sz-muted);">{{ __('No description available.') }}</p>
        @endif
    </div>

    {{-- Reviews Panel --}}
    <div id="sz-dp-tab-reviews" class="sz-dp-tab-panel" style="display:none;">

        {{-- Existing Reviews --}}
        @if($reviews->isNotEmpty())
        <div style="display:flex;flex-direction:column;gap:16px;margin-bottom:32px;">
            @foreach($reviews as $review)
            <div style="background:var(--sz-bg);border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);padding:20px;">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                    {{-- Avatar --}}
                    <div style="width:44px;height:44px;border-radius:50%;background:var(--sz-navy);display:flex;align-items:center;justify-content:center;font-family:var(--sz-font-head);font-size:16px;font-weight:700;color:#fff;flex-shrink:0;letter-spacing:1px;">
                        {{ strtoupper(substr($review->user?->name ?? 'A', 0, 1)) }}
                    </div>
                    <div style="flex:1;">
                        <div style="font-family:var(--sz-font-head);font-weight:700;font-size:14px;color:var(--sz-dark);text-transform:uppercase;letter-spacing:1px;">
                            {{ $review->user?->name ?? __('Anonymous') }}
                        </div>
                        <div style="display:flex;gap:2px;margin-top:4px;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="mdi mdi-star{{ $i <= $review->rating ? '' : '-outline' }}"
                                   style="font-size:14px;color:{{ $i <= $review->rating ? '#FDD835' : 'var(--sz-border)' }};"></i>
                            @endfor
                        </div>
                    </div>
                    <div style="font-family:var(--sz-font-head);font-size:11px;color:var(--sz-muted);letter-spacing:1px;text-transform:uppercase;">
                        {{ $review->created_at->format('d M Y') }}
                    </div>
                </div>
                <p style="font-size:14px;color:var(--sz-muted);line-height:1.8;margin:0;">{{ $review->review_text }}</p>
            </div>
            @endforeach
        </div>
        @else
        <div style="text-align:center;padding:32px 0;color:var(--sz-muted);">
            <i class="mdi mdi-comment-outline" style="font-size:48px;opacity:.3;display:block;margin-bottom:12px;"></i>
            <p>{{ __('No reviews yet. Be the first!') }}</p>
        </div>
        @endif

        {{-- Review Form --}}
        @auth('web')
        <div style="background:var(--sz-bg);border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);padding:28px;">
            <div style="font-family:var(--sz-font-head);font-size:13px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--sz-dark);margin-bottom:18px;">
                <i class="mdi mdi-pencil-outline" style="color:var(--sz-red);"></i> {{ __('Leave a Review') }}
            </div>

            {{-- Star Picker --}}
            <div style="display:flex;gap:6px;margin-bottom:16px;">
                @for($i = 1; $i <= 5; $i++)
                <i class="mdi mdi-star sz-dp-star" data-value="{{ $i }}"
                   style="font-size:32px;color:var(--sz-border);cursor:pointer;transition:color .15s;"></i>
                @endfor
            </div>

            <textarea id="sz-dp-review-text" rows="4"
                      style="width:100%;background:#fff;border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);color:var(--sz-dark);padding:14px 18px;font-size:14px;font-family:var(--sz-font-body);outline:none;resize:vertical;margin-bottom:14px;transition:border-color .2s;box-sizing:border-box;"
                      placeholder="{{ __('Write your review here…') }}"
                      onfocus="this.style.borderColor='var(--sz-red)'"
                      onblur="this.style.borderColor='var(--sz-border)'"></textarea>

            <button id="sz-dp-review-submit" data-product="{{ $product->id }}"
                    class="sz-btn sz-btn-red"
                    style="gap:8px;">
                <i class="mdi mdi-send-outline"></i> {{ __('Submit Review') }}
            </button>
        </div>
        @endauth
    </div>

    {{-- Refund Policy Panel --}}
    @if($product->refund_policy)
    <div id="sz-dp-tab-refund" class="sz-dp-tab-panel" style="display:none;">
        <div style="font-size:14px;color:var(--sz-muted);line-height:1.9;">
            {!! $product->refund_policy->description ?? '' !!}
        </div>
    </div>
    @endif

</div>
</div>

{{-- Related Products --}}
@if($related_products->isNotEmpty())
<section style="background:var(--sz-bg);padding:56px 0 72px;">
<div class="container">
    <div class="sz-sec-heading">
        <span class="sz-sec-title">{{ __('Related Products') }}</span>
    </div>
    <div class="row g-3">
        @foreach($related_products as $rp)
        @php
            $rp_data   = get_digital_product_dynamic_price($rp);
            $rp_img_d  = get_attachment_image_by_id($rp->image_id ?? null, 'grid');
            $rp_img    = $rp_img_d['img_url'] ?? null;
            $rp_price  = $rp_data['sale_price'] > 0 ? $rp_data['sale_price'] : $rp_data['regular_price'];
        @endphp
        <div class="col-md-4">
            <div class="sz-card">
                <div class="sz-card-img" style="height:180px;position:relative;">
                    @if($rp_img)
                        <a href="{{ theme_product_url($rp->slug) }}">
                            <img src="{{ $rp_img }}" alt="{{ $rp->name }}" loading="lazy"
                                 style="width:100%;height:180px;object-fit:cover;display:block;">
                        </a>
                    @else
                        <a href="{{ theme_product_url($rp->slug) }}" class="sz-img-ph" style="height:180px;display:flex;align-items:center;justify-content:center;">
                            <i class="mdi mdi-file-download-outline" style="font-size:48px;color:var(--sz-red);opacity:.3;"></i>
                        </a>
                    @endif
                    @if($rp_data['discount'] > 0)
                        <span class="sz-card-badge sz-badge-sale">{{ $rp_data['discount'] }}% {{ __('off') }}</span>
                    @endif
                </div>
                <div class="sz-card-body">
                    @if($rp->category)
                        <div class="sz-card-cat">{{ $rp->category->name }}</div>
                    @endif
                    <div class="sz-card-name">
                        <a href="{{ theme_product_url($rp->slug) }}">{{ \Illuminate\Support\Str::words($rp->name, 6) }}</a>
                    </div>
                    <div class="sz-card-price">
                        <span class="sz-price-sale">{{ amount_with_currency_symbol($rp_price) }}</span>
                        @if($rp_data['discount'] > 0 && $rp_data['regular_price'])
                            <span class="sz-price-orig">{{ amount_with_currency_symbol($rp_data['regular_price']) }}</span>
                        @endif
                    </div>
                    <a href="{{ theme_product_url($rp->slug) }}"
                       class="sz-btn sz-btn-outline sz-btn-sm w-100"
                       style="display:flex;align-items:center;justify-content:center;gap:6px;text-decoration:none;">
                        <i class="mdi mdi-eye"></i> {{ __('View') }}
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
</section>
@endif

@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';

    // Tab switching
    $(document).on('click', '.sz-dp-tab-btn', function () {
        var target = $(this).data('tab');
        $('.sz-dp-tab-btn')
            .css({ color: 'var(--sz-muted)', 'border-bottom-color': 'transparent' })
            .removeClass('active');
        $(this)
            .css({ color: 'var(--sz-red)', 'border-bottom-color': 'var(--sz-red)' })
            .addClass('active');
        $('.sz-dp-tab-panel').hide();
        $('#' + target).show();
    });

    // Interactive star rating
    var selectedRating = 0;

    $(document).on('mouseover', '.sz-dp-star', function () {
        var val = $(this).data('value');
        $('.sz-dp-star').each(function (i) {
            $(this).css('color', i < val ? '#FDD835' : 'var(--sz-border)');
        });
    }).on('mouseout', '.sz-dp-star', function () {
        $('.sz-dp-star').each(function (i) {
            $(this).css('color', i < selectedRating ? '#FDD835' : 'var(--sz-border)');
        });
    }).on('click', '.sz-dp-star', function () {
        selectedRating = $(this).data('value');
        $('.sz-dp-star').each(function (i) {
            $(this).css('color', i < selectedRating ? '#FDD835' : 'var(--sz-border)');
        });
    });

    // Submit review
    $(document).on('click', '#sz-dp-review-submit', function () {
        var btn        = $(this);
        var product_id = btn.data('product');
        var rating     = selectedRating;
        var review_text = $('#sz-dp-review-text').val().trim();

        if (!rating) {
            toastr.warning('{{ __("Please select a star rating") }}');
            return;
        }
        if (!review_text) {
            toastr.warning('{{ __("Please enter your review") }}');
            return;
        }

        $.ajax({
            url:  '{{ theme_digital_product_review_url() }}',
            type: 'POST',
            data: {
                _token:      '{{ csrf_token() }}',
                product_id:  product_id,
                rating:      rating,
                review_text: review_text,
            },
            beforeSend: function () {
                btn.html('<i class="mdi mdi-loading mdi-spin"></i> {{ __("Submitting…") }}')
                   .prop('disabled', true);
            },
            success: function (data) {
                if (data.type === 'success') {
                    toastr.success(data.msg);
                    setTimeout(function () { location.reload(); }, 1000);
                } else {
                    toastr.error(data.msg);
                    btn.html('<i class="mdi mdi-send-outline"></i> {{ __("Submit Review") }}')
                       .prop('disabled', false);
                }
            },
            error: function () {
                toastr.error('{{ __("An error occurred. Please try again.") }}');
                btn.html('<i class="mdi mdi-send-outline"></i> {{ __("Submit Review") }}')
                   .prop('disabled', false);
            }
        });
    });

})(jQuery);
</script>
@endsection
