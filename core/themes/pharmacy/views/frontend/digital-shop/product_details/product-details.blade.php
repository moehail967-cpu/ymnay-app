@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $product->name !!} @endsection
@section('page-title') {!! $product->name !!} @endsection
@section('meta-data') {!! render_page_meta_data($product) !!} @endsection

@section('style')
<link rel="stylesheet" href="{{ global_asset('assets/common/css/star-rating.min.css') }}">
@endsection

@section('content')
@php
    $price_data  = get_digital_product_dynamic_price($product);
    $sale_price  = $price_data['sale_price']    ?? $price_data['price']    ?? 0;
    $reg_price   = $price_data['regular_price'] ?? $price_data['old_price'] ?? null;
    $discount    = $price_data['discount'] ?? null;
    $img_data    = get_attachment_image_by_id($product->image_id ?? null, 'full');
    $img_url     = $img_data['img_url'] ?? null;
    $category    = $product->category;
    $af          = $product->additionalFields;
    $author_name = $af?->author?->name ?? null;
    $avg_rating  = isset($reviews) && $reviews->isNotEmpty() ? round($reviews->avg('rating'), 1) : 0;
    $review_count = isset($reviews) ? $reviews->count() : 0;
@endphp

{{-- Page Hero --}}
<div class="pf-page-hero">
    <div class="container">
        <h1>{{ \Illuminate\Support\Str::words($product->name, 8) }}</h1>
        <div class="pf-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="las la-angle-right"></i></span>
            <a href="{{ theme_digital_shop_url() }}">{{ __('Digital Shop') }}</a>
            <span class="sep"><i class="las la-angle-right"></i></span>
            <span class="current">{{ \Illuminate\Support\Str::words($product->name, 6) }}</span>
        </div>
    </div>
</div>

{{-- Product Section --}}
<div class="pf-dp-section">
<div class="container">
    <div class="row g-4">

        {{-- Cover Image --}}
        <div class="col-lg-3 col-md-4">
            <div class="pf-dp-cover-wrap">
                @if($img_url)
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" class="pf-dp-cover-img">
                @else
                    <div class="pf-dp-cover-placeholder">
                        <i class="las la-file-download"></i>
                    </div>
                @endif
            </div>
        </div>

        {{-- Product Info --}}
        <div class="col-lg-6 col-md-8">
            @if($category)
                <div class="pf-dp-cat-label">{{ $category->name }}</div>
            @endif

            <h1 class="pf-dp-title">{{ $product->name }}</h1>

            @if($author_name)
                <div class="pf-dp-author">
                    <i class="las la-user-circle"></i> {{ $author_name }}
                </div>
            @endif

            @if($avg_rating > 0)
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="d-flex gap-1">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="las la-star pf-dp-star-display {{ $i <= $avg_rating ? 'filled' : '' }}"></i>
                    @endfor
                </div>
                <span class="pf-dp-review-meta-count">({{ $review_count }})</span>
            </div>
            @endif

            {{-- Price --}}
            <div class="pf-dp-price-row">
                <span class="pf-dp-price-big">{{ amount_with_currency_symbol($sale_price) }}</span>
                @if($discount > 0 && $reg_price)
                    <span class="pf-dp-price-was">{{ amount_with_currency_symbol($reg_price) }}</span>
                    <span class="pf-dp-discount-pill">{{ $discount }}% {{ __('off') }}</span>
                @endif
            </div>

            {{-- Specs Grid --}}
            @php $has_specs = $af && ($af->file_format || $af->number_of_pages || $af->getLanguage || $af->file_size || ($product->downloads_count ?? 0)); @endphp
            @if($has_specs)
            <div class="pf-dp-specs">
                <div class="row g-3">
                    @if($af->file_format ?? false)
                    <div class="col-6">
                        <div class="pf-dp-spec-key">{{ __('Format') }}</div>
                        <div class="pf-dp-spec-val">{{ strtoupper($af->file_format) }}</div>
                    </div>
                    @endif
                    @if($af->number_of_pages ?? false)
                    <div class="col-6">
                        <div class="pf-dp-spec-key">{{ __('Pages') }}</div>
                        <div class="pf-dp-spec-val">{{ $af->number_of_pages }}</div>
                    </div>
                    @endif
                    @if($af->getLanguage ?? false)
                    <div class="col-6">
                        <div class="pf-dp-spec-key">{{ __('Language') }}</div>
                        <div class="pf-dp-spec-val">{{ $af->getLanguage?->name ?? $af->getLanguage }}</div>
                    </div>
                    @endif
                    @if($af->file_size ?? false)
                    <div class="col-6">
                        <div class="pf-dp-spec-key">{{ __('File Size') }}</div>
                        <div class="pf-dp-spec-val">{{ $af->file_size }}</div>
                    </div>
                    @endif
                    @if($af->software_version ?? false)
                    <div class="col-6">
                        <div class="pf-dp-spec-key">{{ __('Version') }}</div>
                        <div class="pf-dp-spec-val">{{ $af->software_version }}</div>
                    </div>
                    @endif
                    @if($product->downloads_count ?? 0)
                    <div class="col-6">
                        <div class="pf-dp-spec-key">{{ __('Downloads') }}</div>
                        <div class="pf-dp-spec-val">{{ $product->downloads_count }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if($product->short_description ?? false)
            <div class="pf-dp-short-desc">{!! $product->short_description !!}</div>
            @endif

            @if(isset($product->tag) && $product->tag->isNotEmpty())
            <div class="pf-dp-tags-row">
                <span class="pf-dp-tag-label">{{ __('Tags:') }}</span>
                @foreach($product->tag as $tag)
                    <span class="pf-dp-tag-pill">{{ $tag->tag_name }}</span>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Purchase Sidebar --}}
        <div class="col-lg-3">
            <div class="pf-dp-purchase-box">
                <div class="pf-dp-purchase-price">{{ amount_with_currency_symbol($sale_price) }}</div>

                @if($discount > 0 && $reg_price)
                <div class="pf-dp-purchase-was-row">
                    {{ __('Was') }} <del>{{ amount_with_currency_symbol($reg_price) }}</del>
                    — <span>{{ $discount }}% {{ __('off') }}</span>
                </div>
                @endif

                @auth('web')
                <form action="{{ theme_digital_product_add_to_cart_url() }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="pf-btn pf-btn-teal w-100 justify-content-center mb-2">
                        <i class="las la-download"></i> {{ __('Get Now') }}
                    </button>
                </form>
                @else
                <a href="{{ theme_login_url() }}" class="pf-btn pf-btn-teal w-100 justify-content-center mb-2" style="display:flex;">
                    <i class="las la-sign-in-alt"></i> {{ __('Sign In to Purchase') }}
                </a>
                @endauth

                <button class="add-to-wishlist-btn pf-btn pf-btn-outline w-100 justify-content-center mb-4" data-product_id="{{ $product->id }}">
                    <i class="las la-heart"></i> {{ __('Add to Wishlist') }}
                </button>

                <div class="pf-dp-trust-list">
                    <div class="pf-dp-trust-item">
                        <i class="las la-download"></i>
                        {{ __('Instant digital download') }}
                    </div>
                    <div class="pf-dp-trust-item">
                        <i class="las la-shield-alt"></i>
                        {{ __('Secure checkout') }}
                    </div>
                    @if($product->refund_policy ?? false)
                    <div class="pf-dp-trust-item">
                        <i class="las la-undo-alt"></i>
                        {{ __('Refund policy available') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
</div>

{{-- Tabs Section --}}
<div class="pf-dp-tabs-section">
<div class="container">
    <div class="pf-tab-nav">
        <button class="pf-tab-nav-btn active" data-target="pf-dp-desc">{{ __('Description') }}</button>
        @if($review_count > 0)
        <button class="pf-tab-nav-btn" data-target="pf-dp-reviews">{{ __('Reviews') }} ({{ $review_count }})</button>
        @else
        <button class="pf-tab-nav-btn" data-target="pf-dp-reviews">{{ __('Reviews') }}</button>
        @endif
        @if($product->refund_policy ?? false)
        <button class="pf-tab-nav-btn" data-target="pf-dp-refund">{{ __('Refund Policy') }}</button>
        @endif
    </div>

    <div id="pf-dp-desc" class="pf-tab-panel active">
        @if($product->description)
            <div class="pf-article-content">{!! $product->description !!}</div>
        @else
            <p class="pf-no-desc">{{ __('No description available.') }}</p>
        @endif
    </div>

    <div id="pf-dp-reviews" class="pf-tab-panel">
        @if(isset($reviews) && $reviews->isNotEmpty())
        <div class="pf-dp-reviews-list">
            @foreach($reviews as $review)
            <div class="pf-dp-review-item">
                <div class="pf-dp-review-header">
                    <div class="pf-dp-review-avatar">
                        {{ strtoupper(substr($review->user?->name ?? 'A', 0, 1)) }}
                    </div>
                    <div style="flex:1;">
                        <div class="pf-dp-review-name">{{ $review->user?->name ?? __('Anonymous') }}</div>
                        <div class="pf-dp-review-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="las la-star pf-dp-star-display {{ $i <= $review->rating ? 'filled' : '' }}"></i>
                            @endfor
                        </div>
                    </div>
                    <div class="pf-dp-review-date">{{ $review->created_at->format('d M Y') }}</div>
                </div>
                <p class="pf-dp-review-text">{{ $review->review_text }}</p>
            </div>
            @endforeach
        </div>
        @endif

        @auth('web')
        <div class="pf-dp-review-form">
            <div class="pf-dp-review-form-title">{{ __('Leave a Review') }}</div>
            <div class="pf-dp-stars" id="pf-dp-stars">
                @for($i = 1; $i <= 5; $i++)
                    <i class="las la-star pf-dp-star" data-value="{{ $i }}"></i>
                @endfor
            </div>
            <textarea id="pf-dp-review-text" class="pf-dp-review-textarea" rows="3"
                      placeholder="{{ __('Your review…') }}"></textarea>
            <button id="pf-dp-review-submit" class="pf-btn pf-btn-teal" data-product="{{ $product->id }}">
                <i class="las la-paper-plane"></i> {{ __('Submit Review') }}
            </button>
        </div>
        @endauth
    </div>

    @if($product->refund_policy ?? false)
    <div id="pf-dp-refund" class="pf-tab-panel">
        <div class="pf-article-content">{!! $product->refund_policy->description ?? '' !!}</div>
    </div>
    @endif
</div>
</div>

{{-- Related Products --}}
@if(isset($related_products) && $related_products->isNotEmpty())
<div class="pf-dp-related">
    <div class="container">
        <div class="text-center mb-4">
            <div class="pf-dp-related-eyebrow">{{ __('More Like This') }}</div>
            <h2 class="pf-section-heading">{{ __('Related Products') }}</h2>
        </div>
        <div class="row g-4">
            @foreach($related_products as $rp)
            @php
                $rp_data = get_digital_product_dynamic_price($rp);
                $rp_sale = $rp_data['sale_price'] ?? $rp_data['price'] ?? 0;
                $rp_disc = $rp_data['discount'] ?? null;
                $rp_url  = theme_product_url($rp->slug);
                $rp_img  = theme_product_image($rp->image_id ?? null, 'grid');
            @endphp
            <div class="col-md-4">
                <div class="pf-product-card">
                    @if($rp_disc)
                        <div class="pf-product-badge pf-badge-sale">{{ $rp_disc }}% {{ __('OFF') }}</div>
                    @endif
                    <a href="{{ $rp_url }}" class="pf-product-img">
                        @if($rp_img)
                            <img src="{{ $rp_img }}" alt="{{ $rp->name }}" loading="lazy">
                        @else
                            <i class="las la-file-download" style="font-size:48px;color:var(--pf-teal);"></i>
                        @endif
                    </a>
                    <div class="pf-product-body">
                        @if($rp->category)
                            <div class="pf-product-brand">{{ $rp->category->name }}</div>
                        @endif
                        <div class="pf-product-name">
                            <a href="{{ $rp_url }}">{{ \Illuminate\Support\Str::words($rp->name, 8) }}</a>
                        </div>
                        <div class="pf-product-footer">
                            <span class="pf-price-current">{{ amount_with_currency_symbol($rp_sale) }}</span>
                            <a href="{{ $rp_url }}" class="pf-add-btn" title="{{ __('View Product') }}">
                                <i class="las la-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
(function($){
    'use strict';

    // Tab switching
    $(document).on('click', '.pf-tab-nav-btn', function(){
        var target = $(this).data('target');
        $('.pf-tab-nav-btn').removeClass('active');
        $(this).addClass('active');
        $('.pf-tab-panel').removeClass('active');
        $('#' + target).addClass('active');
    });

    // Star rating
    var selectedRating = 0;
    $(document).on('mouseover', '.pf-dp-star', function(){
        var val = $(this).data('value');
        $('.pf-dp-star').each(function(i){ $(this).toggleClass('hover', i < val).css('color',''); });
    }).on('mouseout', '.pf-dp-star', function(){
        $('.pf-dp-star').removeClass('hover');
    }).on('click', '.pf-dp-star', function(){
        selectedRating = $(this).data('value');
        $('.pf-dp-star').each(function(i){
            $(this).toggleClass('active', i < selectedRating).removeClass('hover');
        });
    });

    // Submit review
    $(document).on('click', '#pf-dp-review-submit', function(){
        var btn = $(this);
        var product_id = btn.data('product');
        var rating = selectedRating;
        var review_text = $('#pf-dp-review-text').val().trim();
        if(!rating){ toastr.warning('{{ __("Please select a rating") }}'); return; }
        if(!review_text){ toastr.warning('{{ __("Please enter a review") }}'); return; }
        $.ajax({
            url: '{{ theme_digital_product_review_url() }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', product_id: product_id, rating: rating, review_text: review_text },
            beforeSend: function(){ btn.text('{{ __("Submitting…") }}').prop('disabled', true); },
            success: function(data){
                if(data.type === 'success'){
                    toastr.success(data.msg);
                    setTimeout(function(){ location.reload(); }, 1000);
                } else {
                    toastr.error(data.msg);
                    btn.html('<i class="las la-paper-plane"></i> {{ __("Submit Review") }}').prop('disabled', false);
                }
            },
            error: function(){
                btn.html('<i class="las la-paper-plane"></i> {{ __("Submit Review") }}').prop('disabled', false);
            }
        });
    });

})(jQuery);
</script>
@endsection
