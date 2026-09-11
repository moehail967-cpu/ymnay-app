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

<div class="tn-page-banner">
    <div class="container tn-page-banner-content">
        <h1>{{ \Illuminate\Support\Str::words($product->name, 6) }}</h1>
        <div class="tn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ theme_digital_shop_url() }}">{{ __('Digital Shop') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ \Illuminate\Support\Str::words($product->name, 5) }}</span>
        </div>
    </div>
</div>

<div class="container tn-dp-detail-body">
    <div class="row g-4">

        {{-- Cover image --}}
        <div class="col-lg-3 col-md-4">
            <div class="tn-dp-img-sticky">
                @if($img_url)
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" class="tn-dp-main-img">
                @else
                    <div class="tn-dp-cover-placeholder tn-dp-cover-placeholder-lg">
                        <i class="las la-file-download"></i>
                    </div>
                @endif
            </div>
        </div>

        {{-- Product info --}}
        <div class="col-lg-6 col-md-8">
            @if($category)
                <div class="tn-dp-cat-badge">{{ $category->name }}</div>
            @endif

            <h1 class="tn-dp-title">{!! $product->name !!}</h1>

            @if($author_name)
            <div class="tn-dp-author-line"><i class="las la-user tn-dp-author-icon"></i> {{ $author_name }}</div>
            @endif

            @if($avg_rating > 0)
            <div class="tn-dp-rating-stars">
                @for($i = 1; $i <= 5; $i++)
                    <i class="las {{ $i <= $avg_rating ? 'la-star' : ($i - $avg_rating < 1 ? 'la-star-half-alt' : 'la-star') }} tn-dp-rating-star {{ $i <= $avg_rating ? 'filled' : '' }}"></i>
                @endfor
                <span class="tn-dp-rating-count">({{ $reviews->count() }})</span>
            </div>
            @endif

            <div class="tn-dp-price-row-main">
                <span class="tn-dp-price-main">{{ amount_with_currency_symbol($sale_price) }}</span>
                @if($discount > 0)
                    <span class="tn-dp-price-was">{{ amount_with_currency_symbol($regular_price) }}</span>
                    <span class="tn-dp-price-off">{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            {{-- Meta specs --}}
            @if($product->additionalFields?->file_format || $product->additionalFields?->number_of_pages || $product->additionalFields?->language || $product->additionalFields?->file_size || ($product->downloads_count ?? 0))
            <div class="tn-dp-meta-grid">
                <div class="row g-3">
                    @if($product->additionalFields?->file_format)
                    <div class="col-6">
                        <div class="tn-dp-meta-label">{{ __('Format') }}</div>
                        <div class="tn-dp-meta-val">{{ strtoupper($product->additionalFields->file_format) }}</div>
                    </div>
                    @endif
                    @if($product->additionalFields?->number_of_pages)
                    <div class="col-6">
                        <div class="tn-dp-meta-label">{{ __('Pages') }}</div>
                        <div class="tn-dp-meta-val">{{ $product->additionalFields->number_of_pages }}</div>
                    </div>
                    @endif
                    @if($product->additionalFields?->language)
                    <div class="col-6">
                        <div class="tn-dp-meta-label">{{ __('Language') }}</div>
                        <div class="tn-dp-meta-val">{{ $product->additionalFields->getLanguage?->name }}</div>
                    </div>
                    @endif
                    @if($product->additionalFields?->file_size)
                    <div class="col-6">
                        <div class="tn-dp-meta-label">{{ __('File Size') }}</div>
                        <div class="tn-dp-meta-val">{{ $product->additionalFields->file_size }}</div>
                    </div>
                    @endif
                    @if($product->downloads_count ?? 0)
                    <div class="col-6">
                        <div class="tn-dp-meta-label">{{ __('Downloads') }}</div>
                        <div class="tn-dp-meta-val">{{ $product->downloads_count }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if($product->short_description)
            <div class="tn-dp-short-desc">{!! $product->short_description !!}</div>
            @endif

            @if($product->tag && $product->tag->isNotEmpty())
            <div class="tn-dp-tags">
                <span class="tn-dp-tag-label">{{ __('Tags:') }}</span>
                @foreach($product->tag as $tag)
                    <span class="tn-dp-tag-item">{{ $tag->tag_name }}</span>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Purchase sidebar --}}
        <div class="col-lg-3">
            <div class="tn-dp-purchase-card">
                <div class="tn-dp-purchase-head">{{ __('Get This Product') }}</div>
                <div class="tn-dp-purchase-body">
                    <div class="tn-dp-price-main mb-1">{{ amount_with_currency_symbol($sale_price) }}</div>
                    @if($discount > 0)
                    <div class="tn-dp-sidebar-disc">
                        {{ __('Was') }} <span class="tn-dp-price-was">{{ amount_with_currency_symbol($regular_price) }}</span>
                        — <span class="tn-dp-sidebar-off">{{ $discount }}% {{ __('off') }}</span>
                    </div>
                    @else
                    <div class="mb-3"></div>
                    @endif

                    @auth('web')
                    <form action="{{ theme_digital_product_add_to_cart_url() }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="tn-dp-atc-btn">
                            <i class="las la-cart-plus"></i> {{ __('Add to Cart') }}
                        </button>
                    </form>
                    @else
                    <a href="{{ theme_login_url() }}" class="tn-dp-atc-btn">
                        <i class="las la-sign-in-alt"></i> {{ __('Sign In to Purchase') }}
                    </a>
                    @endauth

                    <button class="tn-dp-wish-btn add-to-wishlist-btn" data-product_id="{{ $product->id }}">
                        <i class="las la-heart"></i> {{ __('Wishlist') }}
                    </button>

                    <div class="tn-dp-trust">
                        <div class="tn-dp-trust-item">
                            <i class="las la-cloud-download-alt"></i>
                            {{ __('Instant digital download') }}
                        </div>
                        <div class="tn-dp-trust-item">
                            <i class="las la-shield-alt"></i>
                            {{ __('Secure checkout') }}
                        </div>
                        @if($product->refund_policy)
                        <div class="tn-dp-trust-item">
                            <i class="las la-undo"></i>
                            {{ __('Refund policy available') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Tabs --}}
    <div class="mt-5">
        <div class="tn-dp-tab-nav">
            <button class="tn-dp-tab-btn active" data-target="tn-dp-tab-desc">
                {{ __('Description') }}
            </button>
            @if($reviews->isNotEmpty())
            <button class="tn-dp-tab-btn" data-target="tn-dp-tab-reviews">
                {{ __('Reviews') }} ({{ $reviews->count() }})
            </button>
            @endif
            @if($product->refund_policy)
            <button class="tn-dp-tab-btn" data-target="tn-dp-tab-refund">
                {{ __('Refund Policy') }}
            </button>
            @endif
        </div>

        <div id="tn-dp-tab-desc" class="tn-dp-tab-panel active">
            @if($product->description)
                <div class="tn-dp-tab-desc">{!! $product->description !!}</div>
            @else
                <p class="tn-muted">{{ __('No description available.') }}</p>
            @endif
        </div>

        @if($reviews->isNotEmpty())
        <div id="tn-dp-tab-reviews" class="tn-dp-tab-panel">
            @foreach($reviews as $review)
            <div class="tn-dp-review-item">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="tn-dp-review-avatar">
                        {{ strtoupper(substr($review->user?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="tn-dp-review-name">{{ $review->user?->name ?? __('Anonymous') }}</div>
                        <div>
                            @for($i = 1; $i <= 5; $i++)
                                <i class="las la-star tn-dp-review-star {{ $i <= $review->rating ? 'filled' : '' }}"></i>
                            @endfor
                        </div>
                    </div>
                    <div class="tn-dp-review-date">{{ $review->created_at->format('d M Y') }}</div>
                </div>
                <p class="tn-dp-review-text">{{ $review->review_text }}</p>
            </div>
            @endforeach

            @auth('web')
            <div class="tn-dp-review-form mt-4">
                <div class="tn-dp-review-form-title">{{ __('Leave a Review') }}</div>
                <div class="tn-dp-star-picker">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="las la-star tn-dp-star-pick" data-value="{{ $i }}"></i>
                    @endfor
                </div>
                <textarea id="tn-review-text" rows="3" class="tn-input mb-3"
                          placeholder="{{ __('Write your review…') }}"></textarea>
                <button id="tn-review-submit" class="tn-dp-submit-btn" data-product="{{ $product->id }}">
                    <i class="las la-paper-plane"></i> {{ __('Submit Review') }}
                </button>
            </div>
            @endauth
        </div>
        @endif

        @if($product->refund_policy)
        <div id="tn-dp-tab-refund" class="tn-dp-tab-panel">
            <div class="tn-dp-tab-desc">{!! $product->refund_policy->description ?? '' !!}</div>
        </div>
        @endif
    </div>

    {{-- Related products --}}
    @if($related_products->isNotEmpty())
    <div class="mt-5">
        <h2 class="tn-dp-related-title">{{ __('Related Products') }}</h2>
        <div class="row g-3">
            @foreach($related_products as $rp)
            @php
                $rp_data = get_digital_product_dynamic_price($rp);
                $rp_img  = (get_attachment_image_by_id($rp->image_id ?? null, 'grid'))['img_url'] ?? null;
            @endphp
            <div class="col-md-4 col-6">
                <div class="tn-dp-card">
                    <div class="tn-dp-cover">
                        @if($rp_img)
                            <a href="{{ dynamicRoute($rp->slug) }}" class="tn-dp-cover-link">
                                <img src="{{ $rp_img }}" alt="{{ $rp->name }}" loading="lazy">
                            </a>
                        @else
                            <a href="{{ dynamicRoute($rp->slug) }}" class="tn-dp-cover-placeholder">
                                <i class="las la-file-download"></i>
                            </a>
                        @endif
                    </div>
                    <div class="tn-dp-body">
                        @if($rp->category)
                            <div class="tn-dp-cat-label">{{ $rp->category->name }}</div>
                        @endif
                        <a href="{{ dynamicRoute($rp->slug) }}" class="tn-dp-name">
                            {{ \Illuminate\Support\Str::words($rp->name, 8) }}
                        </a>
                        <div class="tn-dp-price-row">
                            <span class="tn-dp-price">{{ amount_with_currency_symbol($rp_data['sale_price']) }}</span>
                        </div>
                        <a href="{{ dynamicRoute($rp->slug) }}" class="tn-dp-view-btn">
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
    $(document).on('click', '.tn-dp-tab-btn', function () {
        var target = $(this).data('target');
        $('.tn-dp-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.tn-dp-tab-panel').removeClass('active');
        $('#' + target).addClass('active');
    });

    // Star picker
    var selectedRating = 0;
    $(document).on('mouseover', '.tn-dp-star-pick', function () {
        var val = $(this).data('value');
        $('.tn-dp-star-pick').each(function (i) {
            $(this).css('color', i < val ? '#f5c518' : 'var(--tn-border)');
        });
    }).on('mouseout', '.tn-dp-star-pick', function () {
        $('.tn-dp-star-pick').each(function (i) {
            $(this).css('color', i < selectedRating ? '#f5c518' : 'var(--tn-border)');
        });
    }).on('click', '.tn-dp-star-pick', function () {
        selectedRating = $(this).data('value');
        $('.tn-dp-star-pick').each(function (i) {
            $(this).css('color', i < selectedRating ? '#f5c518' : 'var(--tn-border)');
        });
    });

    // Submit review
    $(document).on('click', '#tn-review-submit', function () {
        var btn = $(this), product_id = btn.data('product');
        if (!selectedRating) { toastr.warning('{{ __("Please select a rating") }}'); return; }
        var review_text = $('#tn-review-text').val().trim();
        if (!review_text) { toastr.warning('{{ __("Please enter a review") }}'); return; }

        $.ajax({
            url: '{{ theme_digital_product_review_url() }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', product_id: product_id, rating: selectedRating, review_text: review_text },
            beforeSend: function () { btn.html('<i class="las la-spinner la-spin"></i> {{ __("Submitting…") }}'); },
            success: function (data) {
                if (data.type === 'success') {
                    toastr.success(data.msg);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(data.msg);
                    btn.html('<i class="las la-paper-plane"></i> {{ __("Submit Review") }}');
                }
            },
            error: function () {
                btn.html('<i class="las la-paper-plane"></i> {{ __("Submit Review") }}');
            }
        });
    });

})(jQuery);
</script>
@endsection
