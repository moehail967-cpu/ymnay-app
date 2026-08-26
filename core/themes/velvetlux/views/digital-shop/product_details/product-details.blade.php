@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $product->name !!} @endsection
@section('page-title') {!! $product->name !!} @endsection

@section('style')
<link rel="stylesheet" href="{{ global_asset('assets/common/css/star-rating.min.css') }}">
<style>
.vl-dp-tab-nav { display:flex; border-bottom:1px solid var(--vl-border); margin-bottom:24px; }
.vl-dp-tab-btn { padding:14px 24px; font-size:10px; font-weight:600; color:var(--vl-muted); background:none; border:none; border-bottom:2px solid transparent; margin-bottom:-1px; cursor:pointer; font-family:inherit; transition:all .2s; text-transform:uppercase; letter-spacing:2px; }
.vl-dp-tab-btn.active { color:var(--vl-champagne); border-bottom-color:var(--vl-champagne); }
.vl-dp-tab-panel { display:none; }
.vl-dp-tab-panel.active { display:block; }
</style>
@endsection

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

{{-- Breadcrumb --}}
<div style="background:var(--vl-surface);border-bottom:1px solid var(--vl-border);padding:40px 0 28px;">
    <div class="container">
        <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:8px;">{{ __('Digital') }}</div>
        <h2 style="font-size:22px;font-weight:300;color:var(--vl-ivory);margin-bottom:12px;font-family:'Cormorant Garamond',serif;letter-spacing:1px;">{{ \Illuminate\Support\Str::words($product->name, 8) }}</h2>
        <div class="vl-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <a href="{{ theme_digital_shop_url() }}">{{ __('Digital Shop') }}</a>
            <span>/</span>
            <span>{{ \Illuminate\Support\Str::words($product->name, 6) }}</span>
        </div>
    </div>
</div>

{{-- Product Section --}}
<div class="container" style="padding-top:40px;padding-bottom:64px;">
    <div class="row g-4">

        {{-- Cover Image --}}
        <div class="col-lg-3 col-md-4">
            <div style="position:sticky;top:100px;">
                @if($img_url)
                    <div style="border:1px solid var(--vl-border);overflow:hidden;background:var(--vl-surface);aspect-ratio:4/3;display:flex;align-items:center;justify-content:center;">
                        <img src="{{ $img_url }}" alt="{{ $product->name }}"
                             style="width:100%;height:100%;object-fit:contain;">
                    </div>
                @else
                    <div style="border:1px solid var(--vl-border);background:var(--vl-surface);aspect-ratio:4/3;display:flex;align-items:center;justify-content:center;">
                        <i class="las la-file-download" style="font-size:64px;color:var(--vl-champagne);opacity:.3;"></i>
                    </div>
                @endif
            </div>
        </div>

        {{-- Product Info --}}
        <div class="col-lg-6 col-md-8">
            @if($category)
                <div style="display:inline-block;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-champagne);background:rgba(212,184,150,.08);border:1px solid var(--vl-champagne);padding:4px 12px;margin-bottom:12px;">{{ $category->name }}</div>
            @endif

            <h1 style="font-family:'Cormorant Garamond',serif;font-size:28px;font-weight:300;color:var(--vl-ivory);margin-bottom:12px;letter-spacing:.5px;">{!! $product->name !!}</h1>

            @if($author_name)
            <div style="font-size:13px;color:var(--vl-muted);margin-bottom:12px;letter-spacing:.5px;">
                <i class="las la-user" style="color:var(--vl-champagne);"></i> {{ $author_name }}
            </div>
            @endif

            @if($avg_rating > 0)
            <div class="d-flex align-items-center gap-2 mb-3">
                @for($i=1;$i<=5;$i++)
                    <i class="las la-{{ $i <= $avg_rating ? 'star' : ($i - $avg_rating < 1 ? 'star-half-alt' : 'star') }}"
                       style="color:{{ $i <= $avg_rating ? 'var(--vl-champagne)' : 'var(--vl-border)' }};font-size:15px;"></i>
                @endfor
                <span style="font-size:12px;color:var(--vl-muted);">({{ $reviews->count() }})</span>
            </div>
            @endif

            {{-- Price --}}
            <div style="margin-bottom:20px;">
                <span style="font-size:22px;color:var(--vl-champagne);font-family:'Inter',sans-serif;">{{ amount_with_currency_symbol($sale_price) }}</span>
                @if($discount > 0)
                    <span style="font-size:15px;color:var(--vl-muted);text-decoration:line-through;margin-left:10px;">{{ amount_with_currency_symbol($regular_price) }}</span>
                    <span style="font-size:11px;background:rgba(212,184,150,.1);color:var(--vl-champagne);padding:3px 10px;letter-spacing:1px;font-weight:600;font-family:'Inter',sans-serif;margin-left:8px;">{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            {{-- Meta specs --}}
            @if($product->additionalFields?->file_format || $product->additionalFields?->number_of_pages || $product->additionalFields?->language || $product->additionalFields?->file_size || ($product->downloads_count ?? 0))
            <div style="background:var(--vl-surface);border:1px solid var(--vl-border);padding:20px;margin-bottom:20px;">
                <div class="row g-3">
                    @if($product->additionalFields?->file_format)
                    <div class="col-6">
                        <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-muted);margin-bottom:4px;">{{ __('Format') }}</div>
                        <div style="font-size:14px;color:var(--vl-champagne);font-family:'Inter',sans-serif;font-weight:600;">{{ strtoupper($product->additionalFields->file_format) }}</div>
                    </div>
                    @endif
                    @if($product->additionalFields?->number_of_pages)
                    <div class="col-6">
                        <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-muted);margin-bottom:4px;">{{ __('Pages') }}</div>
                        <div style="font-size:14px;color:var(--vl-ivory);font-family:'Inter',sans-serif;">{{ $product->additionalFields->number_of_pages }}</div>
                    </div>
                    @endif
                    @if($product->additionalFields?->language)
                    <div class="col-6">
                        <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-muted);margin-bottom:4px;">{{ __('Language') }}</div>
                        <div style="font-size:14px;color:var(--vl-ivory);font-family:'Inter',sans-serif;">{{ $product->additionalFields->getLanguage?->name }}</div>
                    </div>
                    @endif
                    @if($product->additionalFields?->file_size)
                    <div class="col-6">
                        <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-muted);margin-bottom:4px;">{{ __('File Size') }}</div>
                        <div style="font-size:14px;color:var(--vl-ivory);font-family:'Inter',sans-serif;">{{ $product->additionalFields->file_size }}</div>
                    </div>
                    @endif
                    @if($product->downloads_count ?? 0)
                    <div class="col-6">
                        <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-muted);margin-bottom:4px;">{{ __('Downloads') }}</div>
                        <div style="font-size:14px;color:var(--vl-ivory);font-family:'Inter',sans-serif;">{{ $product->downloads_count }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Short description --}}
            @if($product->short_description)
            <div style="font-size:14px;color:var(--vl-muted);line-height:1.7;margin-bottom:20px;">
                {!! $product->short_description !!}
            </div>
            @endif

            {{-- Tags --}}
            @if($product->tag && $product->tag->isNotEmpty())
            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                <span style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-muted);">{{ __('Tags:') }}</span>
                @foreach($product->tag as $tag)
                    <span style="background:rgba(212,184,150,.08);border:1px solid var(--vl-border);color:var(--vl-champagne);padding:3px 12px;font-size:10px;letter-spacing:1px;">{{ $tag->tag_name }}</span>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Purchase Sidebar --}}
        <div class="col-lg-3">
            <div style="background:var(--vl-card);border:1px solid var(--vl-border);padding:28px;position:sticky;top:100px;">
                <div style="font-size:9px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:20px;font-family:'Inter',sans-serif;padding-bottom:16px;border-bottom:1px solid var(--vl-border);">{{ __('Get This Product') }}</div>

                <div style="font-size:26px;color:var(--vl-champagne);font-family:'Inter',sans-serif;margin-bottom:4px;">{{ amount_with_currency_symbol($sale_price) }}</div>
                @if($discount > 0)
                <div style="font-size:12px;color:var(--vl-muted);margin-bottom:16px;">
                    {{ __('Was') }} <span style="text-decoration:line-through;">{{ amount_with_currency_symbol($regular_price) }}</span>
                    — <span style="color:var(--vl-champagne);font-weight:600;">{{ $discount }}% {{ __('off') }}</span>
                </div>
                @endif

                @auth('web')
                <form action="{{ theme_digital_product_add_to_cart_url() }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="vl-btn vl-btn-primary" style="width:100%;justify-content:center;padding:14px;margin-bottom:10px;font-size:10px;letter-spacing:3px;">
                        <i class="las la-shopping-bag"></i> {{ __('Add to Cart') }}
                    </button>
                </form>
                @else
                <a href="{{ theme_login_url() }}" class="vl-btn vl-btn-primary" style="width:100%;justify-content:center;padding:14px;margin-bottom:10px;font-size:10px;letter-spacing:3px;display:flex;">
                    <i class="las la-sign-in-alt"></i> {{ __('Sign In to Purchase') }}
                </a>
                @endauth

                <button class="add-to-wishlist-btn vl-btn vl-btn-outline"
                        data-product_id="{{ $product->id }}"
                        style="width:100%;justify-content:center;padding:12px;margin-bottom:20px;font-size:10px;letter-spacing:3px;">
                    <i class="las la-heart"></i> {{ __('Wishlist') }}
                </button>

                <div style="border-top:1px solid var(--vl-border);padding-top:16px;display:flex;flex-direction:column;gap:10px;">
                    <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--vl-muted);">
                        <i class="las la-cloud-download-alt" style="color:var(--vl-champagne);font-size:16px;"></i>
                        {{ __('Instant digital download') }}
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--vl-muted);">
                        <i class="las la-shield-alt" style="color:var(--vl-champagne);font-size:16px;"></i>
                        {{ __('Secure checkout') }}
                    </div>
                    @if($product->refund_policy)
                    <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--vl-muted);">
                        <i class="las la-undo-alt" style="color:var(--vl-champagne);font-size:16px;"></i>
                        {{ __('Refund policy available') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- Tabs --}}
    <div style="margin-top:56px;">
        <div class="vl-dp-tab-nav">
            <button class="vl-dp-tab-btn active" data-target="vl-dp-tab-desc">{{ __('Description') }}</button>
            @if($reviews->isNotEmpty())
            <button class="vl-dp-tab-btn" data-target="vl-dp-tab-reviews">{{ __('Reviews') }} ({{ $reviews->count() }})</button>
            @endif
            @if($product->refund_policy)
            <button class="vl-dp-tab-btn" data-target="vl-dp-tab-refund">{{ __('Refund Policy') }}</button>
            @endif
        </div>

        <div id="vl-dp-tab-desc" class="vl-dp-tab-panel active" style="font-size:14px;color:var(--vl-muted);line-height:1.8;">
            @if($product->description)
                {!! $product->description !!}
            @else
                <p>{{ __('No description available.') }}</p>
            @endif
        </div>

        @if($reviews->isNotEmpty())
        <div id="vl-dp-tab-reviews" class="vl-dp-tab-panel">
            <div class="d-flex flex-column gap-3 mb-4">
                @foreach($reviews as $review)
                <div style="background:var(--vl-card);border:1px solid var(--vl-border);padding:20px;">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div style="width:40px;height:40px;background:var(--vl-plum);border:1px solid var(--vl-border);display:flex;align-items:center;justify-content:center;font-weight:600;color:var(--vl-champagne);font-size:14px;flex-shrink:0;">
                            {{ strtoupper(substr($review->user?->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-size:14px;color:var(--vl-ivory);font-weight:400;font-family:'Cormorant Garamond',serif;">{{ $review->user?->name ?? __('Anonymous') }}</div>
                            <div>
                                @for($i=1;$i<=5;$i++)
                                    <i class="las la-star" style="color:{{ $i <= $review->rating ? 'var(--vl-champagne)' : 'var(--vl-border)' }};font-size:12px;"></i>
                                @endfor
                            </div>
                        </div>
                        <div style="margin-left:auto;font-size:11px;color:var(--vl-muted);letter-spacing:.5px;">{{ $review->created_at->format('d M Y') }}</div>
                    </div>
                    <p style="font-size:14px;color:var(--vl-muted);margin:0;line-height:1.6;">{{ $review->review_text }}</p>
                </div>
                @endforeach
            </div>

            @auth('web')
            <div style="background:var(--vl-card);border:1px solid var(--vl-border);padding:28px;">
                <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:16px;font-family:'Inter',sans-serif;">{{ __('Leave a Review') }}</div>
                <div class="d-flex gap-2 mb-3">
                    @for($i=1;$i<=5;$i++)
                    <i class="las la-star vl-review-star" data-value="{{ $i }}"
                       style="font-size:28px;color:var(--vl-border);cursor:pointer;transition:color .15s;"></i>
                    @endfor
                </div>
                <textarea id="review-text" rows="3"
                          style="width:100%;padding:12px 16px;background:var(--vl-surface);border:1px solid var(--vl-border);color:var(--vl-ivory);font-size:14px;font-family:inherit;outline:none;resize:vertical;margin-bottom:16px;"
                          placeholder="{{ __('Write your review…') }}"></textarea>
                <button id="review-submit-btn" data-product="{{ $product->id }}" class="vl-btn vl-btn-primary" style="font-size:10px;letter-spacing:3px;">
                    <i class="las la-paper-plane"></i> {{ __('Submit Review') }}
                </button>
            </div>
            @endauth
        </div>
        @endif

        @if($product->refund_policy)
        <div id="vl-dp-tab-refund" class="vl-dp-tab-panel">
            <div style="font-size:14px;line-height:1.8;color:var(--vl-muted);">
                {!! $product->refund_policy->description ?? '' !!}
            </div>
        </div>
        @endif
    </div>

    {{-- Related Products --}}
    @if($related_products->isNotEmpty())
    <div style="margin-top:56px;">
        <div style="font-size:10px;font-weight:400;letter-spacing:5px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:24px;font-family:'Inter',sans-serif;">{{ __('Related Products') }}</div>
        <div class="row g-3">
            @foreach($related_products as $rp)
            @php
                $rp_data = get_digital_product_dynamic_price($rp);
                $rp_img  = (get_attachment_image_by_id($rp->image_id ?? null, 'grid'))['img_url'] ?? null;
            @endphp
            <div class="col-6 col-md-4 col-lg-3">
                <div style="background:var(--vl-card);border:1px solid var(--vl-border);overflow:hidden;transition:border-color .3s;"
                     onmouseover="this.style.borderColor='var(--vl-champagne)'" onmouseout="this.style.borderColor='var(--vl-border)'">
                    <div style="aspect-ratio:4/3;overflow:hidden;background:var(--vl-surface);display:flex;align-items:center;justify-content:center;">
                        @if($rp_img)
                            <a href="{{ dynamicRoute($rp->slug) }}">
                                <img src="{{ $rp_img }}" alt="{{ $rp->name }}" loading="lazy"
                                     style="width:100%;height:100%;object-fit:contain;">
                            </a>
                        @else
                            <a href="{{ dynamicRoute($rp->slug) }}">
                                <i class="las la-file-download" style="font-size:36px;color:var(--vl-champagne);opacity:.3;"></i>
                            </a>
                        @endif
                    </div>
                    <div style="padding:14px;">
                        @if($rp->category)
                            <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:4px;">{{ $rp->category->name }}</div>
                        @endif
                        <a href="{{ dynamicRoute($rp->slug) }}" style="font-size:13px;color:var(--vl-ivory);text-decoration:none;display:block;margin-bottom:6px;line-height:1.4;">{{ \Illuminate\Support\Str::words($rp->name, 8) }}</a>
                        <div style="font-size:14px;color:var(--vl-champagne);font-family:'Inter',sans-serif;">{{ amount_with_currency_symbol($rp_data['sale_price']) }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';

    $(document).on('click', '.vl-dp-tab-btn', function () {
        var target = $(this).data('target');
        $('.vl-dp-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.vl-dp-tab-panel').removeClass('active');
        $('#' + target).addClass('active');
    });

    var selectedRating = 0;
    $(document).on('mouseover', '.vl-review-star', function () {
        var val = $(this).data('value');
        $('.vl-review-star').each(function (i) { $(this).css('color', i < val ? 'var(--vl-champagne)' : 'var(--vl-border)'); });
    }).on('mouseout', '.vl-review-star', function () {
        $('.vl-review-star').each(function (i) { $(this).css('color', i < selectedRating ? 'var(--vl-champagne)' : 'var(--vl-border)'); });
    }).on('click', '.vl-review-star', function () {
        selectedRating = $(this).data('value');
        $('.vl-review-star').each(function (i) { $(this).css('color', i < selectedRating ? 'var(--vl-champagne)' : 'var(--vl-border)'); });
    });

    $(document).on('click', '#review-submit-btn', function () {
        var btn = $(this), product_id = btn.data('product');
        if (!selectedRating) { toastr.warning('{{ __("Please select a rating") }}'); return; }
        var review_text = $('#review-text').val().trim();
        if (!review_text) { toastr.warning('{{ __("Please enter a review") }}'); return; }

        $.ajax({
            url: '{{ theme_digital_product_review_url() }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', product_id: product_id, rating: selectedRating, review_text: review_text },
            beforeSend: function () { btn.html('<i class="las la-spinner la-spin"></i> {{ __("Submitting...") }}'); },
            success: function (data) {
                if (data.type === 'success') { toastr.success(data.msg); setTimeout(() => location.reload(), 1000); }
                else { toastr.error(data.msg); btn.html('<i class="las la-paper-plane"></i> {{ __("Submit Review") }}'); }
            },
            error: function () { btn.html('<i class="las la-paper-plane"></i> {{ __("Submit Review") }}'); }
        });
    });
})(jQuery);
</script>
@endsection
