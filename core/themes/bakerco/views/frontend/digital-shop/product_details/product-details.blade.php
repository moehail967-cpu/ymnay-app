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

<div class="bk-page-banner">
    <div class="container">
        <h1>{{ \Illuminate\Support\Str::words($product->name, 6) }}</h1>
        <div class="bk-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="mdi mdi-chevron-right"></i></span>
            <a href="{{ theme_digital_shop_url() }}">{{ __('Digital Shop') }}</a>
            <span><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ \Illuminate\Support\Str::words($product->name, 5) }}</span>
        </div>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-4">

            {{-- Cover Image --}}
            <div class="col-lg-3 col-md-4">
                <div style="position:sticky;top:100px;">
                    @if($img_url)
                        <img src="{{ $img_url }}" alt="{{ $product->name }}"
                             style="width:100%;border-radius:var(--bk-radius);border:1.5px solid var(--bk-border);object-fit:cover;aspect-ratio:3/4;">
                    @else
                        <div class="bk-placeholder" style="width:100%;aspect-ratio:3/4;border-radius:var(--bk-radius);font-size:64px;">
                            <i class="mdi mdi-file-document-outline"></i>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Product Info --}}
            <div class="col-lg-6 col-md-8">

                @if($category)
                    <div class="bk-card-cat mb-2">{{ $category->name }}</div>
                @endif

                <h1 style="font-family:var(--bk-font-head);font-size:clamp(20px,3vw,28px);font-weight:700;color:var(--bk-dark);line-height:1.2;margin-bottom:12px;">{!! $product->name !!}</h1>

                @if($author_name)
                    <div style="font-size:13px;color:var(--bk-muted);margin-bottom:8px;">
                        <i class="mdi mdi-account-outline" style="color:var(--bk-rose);"></i> {{ $author_name }}
                    </div>
                @endif

                @if($avg_rating > 0)
                <div class="bk-stars mb-3">
                    @for($i=1;$i<=5;$i++)
                        <i class="mdi {{ $i <= $avg_rating ? 'mdi-star' : ($i - $avg_rating < 1 ? 'mdi-star-half-full' : 'mdi-star-outline') }}"></i>
                    @endfor
                    <span>({{ $reviews->count() }})</span>
                </div>
                @endif

                <div class="bk-card-price mb-4" style="font-size:18px;">
                    <span class="bk-price-sale" style="font-size:28px;">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($discount > 0)
                        <span class="bk-price-orig">{{ amount_with_currency_symbol($regular_price) }}</span>
                        <span class="bk-card-badge" style="position:static;display:inline-block;">{{ $discount }}% {{ __('off') }}</span>
                    @endif
                </div>

                {{-- Specs --}}
                <div class="bk-section-box mb-4">
                    <div class="row g-2">
                        @if($product->additionalFields?->file_format)
                        <div class="col-6">
                            <div style="font-size:11px;color:var(--bk-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('Format') }}</div>
                            <div style="font-weight:700;color:var(--bk-dark);">{{ strtoupper($product->additionalFields->file_format) }}</div>
                        </div>
                        @endif
                        @if($product->additionalFields?->number_of_pages)
                        <div class="col-6">
                            <div style="font-size:11px;color:var(--bk-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('Pages') }}</div>
                            <div style="font-weight:700;color:var(--bk-dark);">{{ $product->additionalFields->number_of_pages }}</div>
                        </div>
                        @endif
                        @if($product->additionalFields?->language)
                        <div class="col-6">
                            <div style="font-size:11px;color:var(--bk-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('Language') }}</div>
                            <div style="font-weight:700;color:var(--bk-dark);">{{ $product->additionalFields->language }}</div>
                        </div>
                        @endif
                        @if($product->additionalFields?->file_size)
                        <div class="col-6">
                            <div style="font-size:11px;color:var(--bk-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('File Size') }}</div>
                            <div style="font-weight:700;color:var(--bk-dark);">{{ $product->additionalFields->file_size }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                @if($product->short_description)
                <div style="color:var(--bk-muted);font-size:14px;line-height:1.7;margin-bottom:20px;">
                    {!! $product->short_description !!}
                </div>
                @endif

                @if($product->tag && $product->tag->isNotEmpty())
                <div class="mb-3 d-flex flex-wrap gap-1 align-items-center">
                    <span style="font-size:11px;color:var(--bk-muted);text-transform:uppercase;letter-spacing:.8px;">{{ __('Tags:') }}</span>
                    @foreach($product->tag as $tag)
                        <span class="bk-tag-pill">{{ $tag->tag_name }}</span>
                    @endforeach
                </div>
                @endif

            </div>

            {{-- Sidebar: Purchase --}}
            <div class="col-lg-3">
                <div class="bk-sidebar-card" style="position:sticky;top:100px;">

                    <div class="bk-pd-price" style="font-size:26px;">{{ amount_with_currency_symbol($sale_price) }}</div>
                    @if($discount > 0)
                    <div style="font-size:13px;color:var(--bk-muted);margin-bottom:16px;">
                        {{ __('Was') }} <span style="text-decoration:line-through;">{{ amount_with_currency_symbol($regular_price) }}</span>
                        — <span style="color:var(--bk-rose);font-weight:700;">{{ $discount }}% {{ __('off') }}</span>
                    </div>
                    @endif

                    @auth('web')
                    <form action="{{ theme_digital_product_add_to_cart_url() }}" method="POST" class="mb-2">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="bk-btn bk-btn-rose w-100 justify-content-center" style="font-size:15px;padding:13px;">
                            <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                        </button>
                    </form>
                    @else
                    <a href="{{ theme_login_url() }}" class="bk-btn bk-btn-rose w-100 justify-content-center mb-2" style="font-size:15px;padding:13px;">
                        <i class="mdi mdi-login"></i> {{ __('Sign In to Purchase') }}
                    </a>
                    @endauth

                    <div class="bk-btn bk-btn-outline w-100 justify-content-center add-to-wishlist-btn mb-4" data-product_id="{{ $product->id }}" style="cursor:pointer;padding:12px;font-size:14px;">
                        <i class="mdi mdi-heart-outline"></i> {{ __('Wishlist') }}
                    </div>

                    <div style="border-top:1.5px solid var(--bk-border);padding-top:16px;display:flex;flex-direction:column;gap:10px;">
                        <div class="d-flex align-items-center gap-2" style="font-size:13px;color:var(--bk-muted);">
                            <i class="mdi mdi-download-circle-outline" style="color:var(--bk-rose);font-size:18px;"></i>
                            {{ __('Instant digital download') }}
                        </div>
                        <div class="d-flex align-items-center gap-2" style="font-size:13px;color:var(--bk-muted);">
                            <i class="mdi mdi-shield-check-outline" style="color:var(--bk-rose);font-size:18px;"></i>
                            {{ __('Secure checkout') }}
                        </div>
                        @if($product->refund_policy)
                        <div class="d-flex align-items-center gap-2" style="font-size:13px;color:var(--bk-muted);">
                            <i class="mdi mdi-undo-variant" style="color:var(--bk-rose);font-size:18px;"></i>
                            {{ __('Refund policy available') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- Tabs: Description / Reviews / Refund --}}
<section class="py-5" style="background:#fff;border-top:1.5px solid var(--bk-border);">
    <div class="container">

        <div class="bk-tab-nav mb-4">
            <button class="bk-tab-btn active" data-tab="bk-tab-desc">{{ __('Description') }}</button>
            @if($reviews->isNotEmpty())
            <button class="bk-tab-btn" data-tab="bk-tab-reviews">{{ __('Reviews') }} ({{ $reviews->count() }})</button>
            @endif
            @if($product->refund_policy)
            <button class="bk-tab-btn" data-tab="bk-tab-refund">{{ __('Refund Policy') }}</button>
            @endif
        </div>

        <div id="bk-tab-desc" class="bk-tab-panel active">
            @if($product->description)
                <div class="bk-product-meta">{!! $product->description !!}</div>
            @else
                <p style="color:var(--bk-muted);">{{ __('No description available.') }}</p>
            @endif
        </div>

        @if($reviews->isNotEmpty())
        <div id="bk-tab-reviews" class="bk-tab-panel">
            <div class="d-flex flex-column gap-3">
                @foreach($reviews as $review)
                <div class="bk-review-card">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div style="width:38px;height:38px;border-radius:50%;background:var(--bk-rose);display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;color:#fff;flex-shrink:0;">
                            {{ strtoupper(substr($review->user?->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div class="bk-review-name">{{ $review->user?->name ?? __('Anonymous') }}</div>
                            <div class="bk-stars">
                                @for($i=1;$i<=5;$i++)
                                    <i class="mdi {{ $i <= $review->rating ? 'mdi-star' : 'mdi-star-outline' }}" style="{{ $i <= $review->rating ? '' : 'color:var(--bk-border);' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <div style="margin-left:auto;" class="bk-review-date">{{ $review->created_at->format('d M Y') }}</div>
                    </div>
                    <p class="bk-review-text">{{ $review->review_text }}</p>
                </div>
                @endforeach
            </div>

            @auth('web')
            <div class="bk-section-box mt-4">
                <div class="bk-section-box-title">{{ __('Leave a Review') }}</div>
                <div class="d-flex gap-2 mb-3">
                    @for($i=1;$i<=5;$i++)
                    <i class="mdi mdi-star bk-review-star" data-value="{{ $i }}" style="font-size:28px;color:var(--bk-border);cursor:pointer;transition:color .15s;"></i>
                    @endfor
                </div>
                <textarea id="bk-review-text" rows="3" class="bk-textarea mb-3" placeholder="{{ __('Your review…') }}"></textarea>
                <button id="bk-review-submit" data-product="{{ $product->id }}" class="bk-btn bk-btn-rose">
                    {{ __('Submit Review') }}
                </button>
            </div>
            @endauth
        </div>
        @endif

        @if($product->refund_policy)
        <div id="bk-tab-refund" class="bk-tab-panel">
            <div class="bk-product-meta">{!! $product->refund_policy->description ?? '' !!}</div>
        </div>
        @endif

    </div>
</section>

{{-- Related Products --}}
@if($related_products->isNotEmpty())
<section class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <span class="bk-section-tag">{{ __('More Like This') }}</span>
            <h2 class="bk-section-title">{{ __('Related Products') }}</h2>
        </div>
        <div class="row g-3">
            @foreach($related_products as $rp)
            @php
                $rp_dp  = get_digital_product_dynamic_price($rp);
                $rp_img = get_attachment_image_by_id($rp->image_id ?? null, 'grid')['img_url'] ?? null;
            @endphp
            <div class="col-md-4">
                <div class="bk-card">
                    <div class="bk-card-img" style="height:180px;">
                        @if($rp_img)
                            <a href="{{ dynamicRoute($rp->slug) }}"><img src="{{ $rp_img }}" alt="{{ $rp->name }}" loading="lazy"></a>
                        @else
                            <a href="{{ dynamicRoute($rp->slug) }}" class="bk-placeholder" style="height:180px;font-size:48px;">
                                <i class="mdi mdi-file-document-outline"></i>
                            </a>
                        @endif
                    </div>
                    <div class="bk-card-body">
                        @if($rp->category)
                            <div class="bk-card-cat">{{ $rp->category->name }}</div>
                        @endif
                        <div class="bk-card-name">
                            <a href="{{ dynamicRoute($rp->slug) }}" style="color:inherit;">{{ \Illuminate\Support\Str::words($rp->name, 7) }}</a>
                        </div>
                        <div class="bk-card-price">
                            <span class="bk-price-sale">{{ amount_with_currency_symbol($rp_dp['sale_price']) }}</span>
                            @if($rp_dp['discount'] > 0)
                                <span class="bk-price-orig">{{ amount_with_currency_symbol($rp_dp['regular_price']) }}</span>
                            @endif
                        </div>
                        <a href="{{ dynamicRoute($rp->slug) }}" class="bk-card-atc">
                            <i class="mdi mdi-eye-outline"></i> {{ __('View Product') }}
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
(function($){
    'use strict';

    $(document).on('click', '.bk-tab-btn', function(){
        var target = $(this).data('tab');
        $('.bk-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.bk-tab-panel').removeClass('active');
        $('#'+target).addClass('active');
    });

    var selectedRating = 0;
    $(document).on('mouseover', '.bk-review-star', function(){
        var val = $(this).data('value');
        $('.bk-review-star').each(function(i){ $(this).css('color', i < val ? '#F59E0B' : 'var(--bk-border)'); });
    }).on('mouseout', '.bk-review-star', function(){
        $('.bk-review-star').each(function(i){ $(this).css('color', i < selectedRating ? '#F59E0B' : 'var(--bk-border)'); });
    }).on('click', '.bk-review-star', function(){
        selectedRating = $(this).data('value');
        $('.bk-review-star').each(function(i){ $(this).css('color', i < selectedRating ? '#F59E0B' : 'var(--bk-border)'); });
    });

    $(document).on('click', '#bk-review-submit', function(){
        var btn = $(this);
        var product_id  = btn.data('product');
        var rating      = selectedRating;
        var review_text = $('#bk-review-text').val().trim();

        if (!rating) { toastr.warning('{{ __("Please select a rating") }}'); return; }
        if (!review_text) { toastr.warning('{{ __("Please enter a review") }}'); return; }

        $.ajax({
            url: '{{ theme_digital_product_review_url() }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', product_id: product_id, rating: rating, review_text: review_text },
            beforeSend: function(){ btn.text('{{ __("Submitting...") }}'); },
            success: function(data){
                if (data.type === 'success') { toastr.success(data.msg); setTimeout(function(){ location.reload(); }, 1000); }
                else { toastr.error(data.msg); btn.html('<i class="mdi mdi-check"></i> {{ __("Submit Review") }}'); }
            },
            error: function(){ btn.html('<i class="mdi mdi-check"></i> {{ __("Submit Review") }}'); }
        });
    });

})(jQuery);
</script>
@endsection
