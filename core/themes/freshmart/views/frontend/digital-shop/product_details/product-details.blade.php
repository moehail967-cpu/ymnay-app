@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $product->name !!} @endsection
@section('page-title') {!! $product->name !!} @endsection

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
<div class="container" style="padding:20px 0 0;">
    <div class="fm-breadcrumb">
        <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
        <span class="sep"><i class="las la-angle-right" style="font-size:11px;"></i></span>
        <a href="{{ theme_digital_shop_url() }}">{{ __('Digital Shop') }}</a>
        <span class="sep"><i class="las la-angle-right" style="font-size:11px;"></i></span>
        <span class="current">{{ \Illuminate\Support\Str::words($product->name, 6) }}</span>
    </div>
</div>

{{-- Product Section --}}
<div class="container" style="padding-top:32px;padding-bottom:60px;">
    <div class="row g-4">

        {{-- Cover Image --}}
        <div class="col-lg-3 col-md-4">
            <div style="position:sticky;top:100px;">
                @if($img_url)
                    <img src="{{ $img_url }}" alt="{{ $product->name }}"
                         style="width:100%;border-radius:12px;border:1.5px solid var(--fm-border);object-fit:cover;aspect-ratio:3/4;">
                @else
                    <div class="fm-dp-no-img" style="width:100%;aspect-ratio:3/4;border-radius:12px;font-size:64px;background:var(--fm-green-light);display:flex;align-items:center;justify-content:center;border:1.5px solid var(--fm-border);">
                        <i class="las la-file-download" style="color:var(--fm-green);"></i>
                    </div>
                @endif
            </div>
        </div>

        {{-- Product Info --}}
        <div class="col-lg-6 col-md-8">
            @if($category)
                <div style="display:inline-block;background:var(--fm-green-light);color:var(--fm-green);font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px;">{{ $category->name }}</div>
            @endif

            <h1 class="fm-product-title">{!! $product->name !!}</h1>

            @if($author_name)
            <div style="font-size:13px;color:var(--fm-muted);margin-bottom:10px;">
                <i class="las la-user" style="color:var(--fm-green);"></i> {{ $author_name }}
            </div>
            @endif

            @if($avg_rating > 0)
            <div class="d-flex align-items-center gap-2 mb-3">
                @for($i=1;$i<=5;$i++)
                    <i class="las la-{{ $i <= $avg_rating ? 'star' : ($i - $avg_rating < 1 ? 'star-half' : 'star') }}"
                       style="color:{{ $i <= $avg_rating ? '#f5c518' : 'var(--fm-border)' }};font-size:16px;"></i>
                @endfor
                <span style="font-size:12px;color:var(--fm-muted);">({{ $reviews->count() }})</span>
            </div>
            @endif

            {{-- Price --}}
            <div class="d-flex align-items-baseline gap-3 mb-4">
                <span class="fm-product-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                @if($discount > 0)
                    <span class="fm-product-price-orig">{{ amount_with_currency_symbol($regular_price) }}</span>
                    <span class="fm-product-price-save">{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            {{-- Meta specs --}}
            @if($product->additionalFields?->file_format || $product->additionalFields?->number_of_pages || $product->additionalFields?->language || $product->additionalFields?->file_size || $product->downloads_count)
            <div class="row g-2 mb-4" style="background:var(--fm-surface);border:1px solid var(--fm-border);border-radius:10px;padding:16px;margin:0;">
                @if($product->additionalFields?->file_format)
                <div class="col-6">
                    <div style="font-size:11px;color:var(--fm-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('Format') }}</div>
                    <div style="font-weight:700;color:var(--fm-dark);font-size:14px;">{{ strtoupper($product->additionalFields->file_format) }}</div>
                </div>
                @endif
                @if($product->additionalFields?->number_of_pages)
                <div class="col-6">
                    <div style="font-size:11px;color:var(--fm-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('Pages') }}</div>
                    <div style="font-weight:700;color:var(--fm-dark);font-size:14px;">{{ $product->additionalFields->number_of_pages }}</div>
                </div>
                @endif
                @if($product->additionalFields?->language)
                <div class="col-6">
                    <div style="font-size:11px;color:var(--fm-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('Language') }}</div>
                    <div style="font-weight:700;color:var(--fm-dark);font-size:14px;">{{ $product->additionalFields->getLanguage?->name }}</div>
                </div>
                @endif
                @if($product->additionalFields?->file_size)
                <div class="col-6">
                    <div style="font-size:11px;color:var(--fm-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('File Size') }}</div>
                    <div style="font-weight:700;color:var(--fm-dark);font-size:14px;">{{ $product->additionalFields->file_size }}</div>
                </div>
                @endif
                @if($product->downloads_count ?? 0)
                <div class="col-6">
                    <div style="font-size:11px;color:var(--fm-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('Downloads') }}</div>
                    <div style="font-weight:700;color:var(--fm-dark);font-size:14px;">{{ $product->downloads_count }}</div>
                </div>
                @endif
            </div>
            @endif

            {{-- Short description --}}
            @if($product->short_description)
            <div style="font-size:14px;color:var(--fm-muted);line-height:1.7;margin-bottom:20px;">
                {!! $product->short_description !!}
            </div>
            @endif

            {{-- Tags --}}
            @if($product->tag && $product->tag->isNotEmpty())
            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                <span style="font-size:12px;color:var(--fm-muted);font-weight:600;">{{ __('Tags:') }}</span>
                @foreach($product->tag as $tag)
                    <span style="background:var(--fm-green-light);color:var(--fm-green);padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">{{ $tag->tag_name }}</span>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Purchase Sidebar --}}
        <div class="col-lg-3">
            <div class="fm-checkout-summary" style="position:sticky;top:100px;">
                <div class="fm-checkout-summary-head">{{ __('Get This Product') }}</div>
                <div class="fm-checkout-summary-body">
                    <div style="font-size:26px;font-weight:800;color:var(--fm-green);margin-bottom:4px;">
                        {{ amount_with_currency_symbol($sale_price) }}
                    </div>
                    @if($discount > 0)
                    <div style="font-size:13px;color:var(--fm-muted);margin-bottom:16px;">
                        {{ __('Was') }} <span style="text-decoration:line-through;">{{ amount_with_currency_symbol($regular_price) }}</span>
                        — <span style="color:var(--fm-orange);font-weight:700;">{{ $discount }}% {{ __('off') }}</span>
                    </div>
                    @endif

                    @auth('web')
                    <form action="{{ theme_digital_product_add_to_cart_url() }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="fm-btn fm-btn-green w-100 justify-content-center mb-2" style="padding:13px;font-size:15px;">
                            <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                        </button>
                    </form>
                    @else
                    <a href="{{ theme_login_url() }}" class="fm-btn fm-btn-green w-100 justify-content-center mb-2" style="padding:13px;font-size:15px;display:flex;">
                        <i class="las la-sign-in-alt"></i> {{ __('Sign In to Purchase') }}
                    </a>
                    @endauth

                    <button class="fm-btn fm-btn-outline w-100 justify-content-center mb-3 add-to-wishlist-btn"
                            data-product_id="{{ $product->id }}" style="padding:11px;">
                        <i class="las la-heart"></i> {{ __('Wishlist') }}
                    </button>

                    <div style="border-top:1px solid var(--fm-border);padding-top:14px;display:flex;flex-direction:column;gap:10px;">
                        <div class="d-flex align-items-center gap-2" style="font-size:13px;color:var(--fm-muted);">
                            <i class="las la-cloud-download-alt" style="color:var(--fm-green);font-size:18px;"></i>
                            {{ __('Instant digital download') }}
                        </div>
                        <div class="d-flex align-items-center gap-2" style="font-size:13px;color:var(--fm-muted);">
                            <i class="las la-shield-alt" style="color:var(--fm-green);font-size:18px;"></i>
                            {{ __('Secure checkout') }}
                        </div>
                        @if($product->refund_policy)
                        <div class="d-flex align-items-center gap-2" style="font-size:13px;color:var(--fm-muted);">
                            <i class="las la-undo-alt" style="color:var(--fm-orange);font-size:18px;"></i>
                            {{ __('Refund policy available') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Tabs --}}
    <div style="margin-top:56px;">
        <div class="fm-tab-nav">
            <button class="fm-tab-nav-btn active" data-target="fm-dp-tab-desc">{{ __('Description') }}</button>
            @if($reviews->isNotEmpty())
            <button class="fm-tab-nav-btn" data-target="fm-dp-tab-reviews">{{ __('Reviews') }} ({{ $reviews->count() }})</button>
            @endif
            @if($product->refund_policy)
            <button class="fm-tab-nav-btn" data-target="fm-dp-tab-refund">{{ __('Refund Policy') }}</button>
            @endif
        </div>

        <div id="fm-dp-tab-desc" class="fm-tab-panel active">
            @if($product->description)
                <div style="font-size:14px;line-height:1.8;color:var(--fm-dark);">{!! $product->description !!}</div>
            @else
                <p style="color:var(--fm-muted);">{{ __('No description available.') }}</p>
            @endif
        </div>

        @if($reviews->isNotEmpty())
        <div id="fm-dp-tab-reviews" class="fm-tab-panel">
            <div class="d-flex flex-column gap-3 mb-4">
                @foreach($reviews as $review)
                <div style="background:#fff;border:1px solid var(--fm-border);border-radius:10px;padding:16px;">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div style="width:40px;height:40px;border-radius:50%;background:var(--fm-green);display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:15px;flex-shrink:0;">
                            {{ strtoupper(substr($review->user?->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight:700;color:var(--fm-dark);font-size:14px;">{{ $review->user?->name ?? __('Anonymous') }}</div>
                            <div>
                                @for($i=1;$i<=5;$i++)
                                    <i class="las la-star" style="color:{{ $i <= $review->rating ? '#f5c518' : 'var(--fm-border)' }};font-size:13px;"></i>
                                @endfor
                            </div>
                        </div>
                        <div style="margin-left:auto;font-size:11px;color:var(--fm-muted);">{{ $review->created_at->format('d M Y') }}</div>
                    </div>
                    <p style="font-size:14px;color:var(--fm-dark);margin:0;">{{ $review->review_text }}</p>
                </div>
                @endforeach
            </div>

            @auth('web')
            <div style="background:#fff;border:1px solid var(--fm-border);border-radius:10px;padding:20px;">
                <div style="font-weight:700;color:var(--fm-dark);margin-bottom:12px;font-size:15px;">{{ __('Leave a Review') }}</div>
                <div class="d-flex gap-2 mb-3">
                    @for($i=1;$i<=5;$i++)
                    <i class="las la-star review-star" data-value="{{ $i }}"
                       style="font-size:28px;color:var(--fm-border);cursor:pointer;transition:color .15s;"></i>
                    @endfor
                </div>
                <textarea id="review-text" rows="3" class="fm-input mb-3" style="height:auto;resize:vertical;"
                          placeholder="{{ __('Write your review…') }}"></textarea>
                <button id="review-submit-btn" data-product="{{ $product->id }}" class="fm-btn fm-btn-green">
                    <i class="las la-paper-plane"></i> {{ __('Submit Review') }}
                </button>
            </div>
            @endauth
        </div>
        @endif

        @if($product->refund_policy)
        <div id="fm-dp-tab-refund" class="fm-tab-panel">
            <div style="font-size:14px;line-height:1.8;color:var(--fm-dark);">
                {!! $product->refund_policy->description ?? '' !!}
            </div>
        </div>
        @endif
    </div>

    {{-- Related Products --}}
    @if($related_products->isNotEmpty())
    <div style="margin-top:56px;">
        <h2 style="font-size:22px;font-weight:800;color:var(--fm-dark);margin-bottom:20px;">{{ __('Related Products') }}</h2>
        <div class="row g-3">
            @foreach($related_products as $rp)
            @php
                $rp_data = get_digital_product_dynamic_price($rp);
                $rp_img  = (get_attachment_image_by_id($rp->image_id ?? null, 'grid'))['img_url'] ?? null;
            @endphp
            <div class="col-md-4">
                <div class="fm-dp-card">
                    <div class="fm-dp-card-img">
                        @if($rp_img)
                            <a href="{{ dynamicRoute($rp->slug) }}">
                                <img src="{{ $rp_img }}" alt="{{ $rp->name }}" loading="lazy">
                            </a>
                        @else
                            <a href="{{ dynamicRoute($rp->slug) }}" class="fm-dp-no-img">
                                <i class="las la-file-download"></i>
                            </a>
                        @endif
                    </div>
                    <div class="fm-dp-card-body">
                        @if($rp->category)
                            <div class="fm-dp-cat">{{ $rp->category->name }}</div>
                        @endif
                        <a href="{{ dynamicRoute($rp->slug) }}" class="fm-dp-name">{{ \Illuminate\Support\Str::words($rp->name, 8) }}</a>
                        <div class="fm-card-price mt-2">
                            <span class="fm-price-sale">{{ amount_with_currency_symbol($rp_data['sale_price']) }}</span>
                        </div>
                        <a href="{{ dynamicRoute($rp->slug) }}" class="fm-btn fm-btn-outline fm-btn-sm w-100 justify-content-center mt-2">
                            <i class="las la-eye"></i> {{ __('View') }}
                        </a>
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

    // Tab switching
    $(document).on('click', '.fm-tab-nav-btn', function () {
        var target = $(this).data('target');
        $('.fm-tab-nav-btn').removeClass('active');
        $(this).addClass('active');
        $('.fm-tab-panel').removeClass('active');
        $('#' + target).addClass('active');
    });

    // Star hover & select
    var selectedRating = 0;
    $(document).on('mouseover', '.review-star', function () {
        var val = $(this).data('value');
        $('.review-star').each(function (i) { $(this).css('color', i < val ? '#f5c518' : 'var(--fm-border)'); });
    }).on('mouseout', '.review-star', function () {
        $('.review-star').each(function (i) { $(this).css('color', i < selectedRating ? '#f5c518' : 'var(--fm-border)'); });
    }).on('click', '.review-star', function () {
        selectedRating = $(this).data('value');
        $('.review-star').each(function (i) { $(this).css('color', i < selectedRating ? '#f5c518' : 'var(--fm-border)'); });
    });

    // Submit review
    $(document).on('click', '#review-submit-btn', function () {
        var btn = $(this), product_id = btn.data('product');
        if (!selectedRating) { toastr.warning('{{ __("Please select a rating") }}'); return; }
        var review_text = $('#review-text').val().trim();
        if (!review_text) { toastr.warning('{{ __("Please enter a review") }}'); return; }

        $.ajax({
            url: '{{ theme_digital_product_review_url() }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', product_id: product_id, rating: selectedRating, review_text: review_text },
            beforeSend: function () { btn.text('{{ __("Submitting...") }}'); },
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
