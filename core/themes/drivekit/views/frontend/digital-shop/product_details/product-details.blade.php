@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $product->name !!} @endsection
@section('page-title') {!! $product->name !!} @endsection

@php
    $price_data    = get_digital_product_dynamic_price($product);
    $sale_price    = $price_data['sale_price'];
    $regular_price = $price_data['regular_price'];
    $discount      = $price_data['discount'];
    $campaign_active    = $price_data['campaign_active'];
    $img_data      = get_attachment_image_by_id($product->image_id ?? null, 'full');
    $img_url       = $img_data['img_url'] ?? null;
    $category      = $product->category;
    $author_name   = $product->additionalFields?->author?->name ?? null;
    $avg_rating    = $reviews->isNotEmpty() ? round($reviews->avg('rating'), 1) : 0;
@endphp

@section('content')

{{-- Breadcrumb --}}
<div class="dk-breadcrumb-bar">
    <div class="container">
        <ul class="dk-bc-list">
            <li><a href="{{ theme_home_url() }}"><i class="mdi mdi-home-outline"></i> {{ __('Home') }}</a></li>
            <li class="sep"><i class="mdi mdi-chevron-right"></i></li>
            <li><a href="{{ theme_digital_shop_url() }}">{{ __('Digital Shop') }}</a></li>
            <li class="sep"><i class="mdi mdi-chevron-right"></i></li>
            <li class="active">{{ \Illuminate\Support\Str::words($product->name, 6) }}</li>
        </ul>
    </div>
</div>

{{-- Product Section --}}
<section class="py-5">
    <div class="container">
        <div class="row g-4">

            {{-- Left: Cover Image --}}
            <div class="col-lg-3 col-md-4">
                <div style="position:sticky;top:100px;">
                    @if($img_url)
                        <img src="{{ $img_url }}" alt="{{ $product->name }}"
                             style="width:100%;border-radius:var(--dk-radius);border:1px solid var(--dk-border);object-fit:cover;aspect-ratio:3/4;">
                    @else
                        <div class="dk-card-no-img" style="width:100%;aspect-ratio:3/4;border-radius:var(--dk-radius);font-size:64px;">
                            <i class="mdi mdi-file-document-outline"></i>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Middle: Product Info --}}
            <div class="col-lg-6 col-md-8">

                @if($category)
                    <div class="dk-card-cat mb-2">{{ $category->name }}</div>
                @endif

                <h1 style="font-size:clamp(20px,3vw,28px);font-weight:900;color:var(--dk-white);line-height:1.2;margin-bottom:12px;">{!! $product->name !!}</h1>

                @if($author_name)
                    <div style="font-size:13px;color:var(--dk-silver);margin-bottom:8px;">
                        <i class="mdi mdi-account-outline" style="color:var(--dk-red);"></i> {{ $author_name }}
                    </div>
                @endif

                @if($avg_rating > 0)
                <div class="dk-stars mb-3" style="display:flex;align-items:center;gap:4px;">
                    @for($i=1;$i<=5;$i++)
                        <i class="mdi {{ $i <= $avg_rating ? 'mdi-star' : ($i - $avg_rating < 1 ? 'mdi-star-half-full' : 'mdi-star-outline') }}" style="color:#f5c518;font-size:18px;"></i>
                    @endfor
                    <span style="font-size:12px;color:var(--dk-silver);margin-left:4px;">({{ $reviews->count() }})</span>
                </div>
                @endif

                {{-- Price --}}
                <div class="dk-card-price mb-4" style="font-size:18px;">
                    <span class="dk-price-sale" style="font-size:28px;">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($discount > 0)
                        <span class="dk-price-orig" style="font-size:16px;">{{ amount_with_currency_symbol($regular_price) }}</span>
                        <span class="dk-card-badge dk-badge-sale" style="position:static;display:inline-block;">{{ $discount }}% {{ __('off') }}</span>
                    @endif
                </div>

                {{-- Meta: format, pages, language, file size --}}
                <div class="dk-product-specs mb-4" style="background:var(--dk-surface);border:1px solid var(--dk-border);border-radius:var(--dk-radius);padding:16px;">
                    <div class="row g-2">
                        @if($product->additionalFields?->file_format)
                        <div class="col-6">
                            <div style="font-size:11px;color:var(--dk-muted);text-transform:uppercase;letter-spacing:1px;">{{ __('Format') }}</div>
                            <div style="color:var(--dk-white);font-weight:600;font-size:14px;">{{ strtoupper($product->additionalFields->file_format) }}</div>
                        </div>
                        @endif
                        @if($product->additionalFields?->number_of_pages)
                        <div class="col-6">
                            <div style="font-size:11px;color:var(--dk-muted);text-transform:uppercase;letter-spacing:1px;">{{ __('Pages') }}</div>
                            <div style="color:var(--dk-white);font-weight:600;font-size:14px;">{{ $product->additionalFields->number_of_pages }}</div>
                        </div>
                        @endif
                        @if($product->additionalFields?->language)
                        <div class="col-6">
                            <div style="font-size:11px;color:var(--dk-muted);text-transform:uppercase;letter-spacing:1px;">{{ __('Language') }}</div>
                            <div style="color:var(--dk-white);font-weight:600;font-size:14px;">{{ $product->additionalFields->language }}</div>
                        </div>
                        @endif
                        @if($product->additionalFields?->file_size)
                        <div class="col-6">
                            <div style="font-size:11px;color:var(--dk-muted);text-transform:uppercase;letter-spacing:1px;">{{ __('File Size') }}</div>
                            <div style="color:var(--dk-white);font-weight:600;font-size:14px;">{{ $product->additionalFields->file_size }}</div>
                        </div>
                        @endif
                        @if($product->downloads_count ?? 0)
                        <div class="col-6">
                            <div style="font-size:11px;color:var(--dk-muted);text-transform:uppercase;letter-spacing:1px;">{{ __('Downloads') }}</div>
                            <div style="color:var(--dk-white);font-weight:600;font-size:14px;">{{ $product->downloads_count }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Description short --}}
                @if($product->short_description)
                <div style="color:var(--dk-silver);font-size:14px;line-height:1.7;margin-bottom:20px;">
                    {!! $product->short_description !!}
                </div>
                @endif

                {{-- Tags --}}
                @if($product->tag && $product->tag->isNotEmpty())
                <div class="mb-3" style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;">
                    <span style="font-size:11px;color:var(--dk-muted);text-transform:uppercase;letter-spacing:1px;">{{ __('Tags:') }}</span>
                    @foreach($product->tag as $tag)
                        <span style="background:var(--dk-panel);border:1px solid var(--dk-border);color:var(--dk-silver);padding:3px 10px;border-radius:20px;font-size:12px;">{{ $tag->tag_name }}</span>
                    @endforeach
                </div>
                @endif

            </div>

            {{-- Right: Add to Cart Sidebar --}}
            <div class="col-lg-3">
                <div class="dk-sidebar-card" style="position:sticky;top:100px;">

                    <div style="font-size:26px;font-weight:900;color:var(--dk-white);margin-bottom:4px;">
                        {{ amount_with_currency_symbol($sale_price) }}
                    </div>
                    @if($discount > 0)
                    <div style="font-size:13px;color:var(--dk-silver);margin-bottom:16px;">
                        {{ __('Was') }} <span style="text-decoration:line-through;">{{ amount_with_currency_symbol($regular_price) }}</span>
                        — <span style="color:var(--dk-red);font-weight:700;">{{ $discount }}% {{ __('off') }}</span>
                    </div>
                    @endif

                    @auth('web')
                    <form action="{{ theme_digital_product_add_to_cart_url() }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="dk-btn dk-btn-red dk-btn-block" style="justify-content:center;padding:14px;font-size:14px;margin-bottom:10px;">
                            <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                        </button>
                    </form>
                    @else
                    <a href="{{ theme_login_url() }}" class="dk-btn dk-btn-red dk-btn-block" style="justify-content:center;padding:14px;font-size:14px;margin-bottom:10px;display:flex;">
                        <i class="mdi mdi-login"></i> {{ __('Sign In to Purchase') }}
                    </a>
                    @endauth

                    <div class="dk-btn dk-btn-outline dk-btn-block add-to-wishlist-btn" data-product_id="{{ $product->id }}" style="cursor:pointer;justify-content:center;padding:12px;font-size:14px;margin-bottom:16px;display:flex;align-items:center;gap:6px;">
                        <i class="mdi mdi-heart-outline"></i> {{ __('Wishlist') }}
                    </div>

                    <div style="border-top:1px solid var(--dk-border);padding-top:16px;display:flex;flex-direction:column;gap:10px;">
                        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--dk-silver);">
                            <i class="mdi mdi-download-circle-outline" style="color:var(--dk-red);font-size:18px;"></i>
                            {{ __('Instant digital download') }}
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--dk-silver);">
                            <i class="mdi mdi-shield-check-outline" style="color:var(--dk-red);font-size:18px;"></i>
                            {{ __('Secure checkout') }}
                        </div>
                        @if($product->refund_policy)
                        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--dk-silver);">
                            <i class="mdi mdi-undo-variant" style="color:var(--dk-red);font-size:18px;"></i>
                            {{ __('Refund policy available') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- Description / Reviews Tabs --}}
<section class="py-4" style="background:var(--dk-surface);border-top:1px solid var(--dk-border);">
    <div class="container">

        <div class="dk-tab-strip" style="margin-bottom:24px;">
            <button class="dk-tab-btn active" data-tab="dk-tab-desc">{{ __('Description') }}</button>
            @if($reviews->isNotEmpty())
            <button class="dk-tab-btn" data-tab="dk-tab-reviews">{{ __('Reviews') }} ({{ $reviews->count() }})</button>
            @endif
            @if($product->refund_policy)
            <button class="dk-tab-btn" data-tab="dk-tab-refund">{{ __('Refund Policy') }}</button>
            @endif
        </div>

        <div id="dk-tab-desc" class="dk-tab-panel active">
            @if($product->description)
                <div class="dk-tab-panel-content">{!! $product->description !!}</div>
            @else
                <p style="color:var(--dk-silver);">{{ __('No description available.') }}</p>
            @endif
        </div>

        @if($reviews->isNotEmpty())
        <div id="dk-tab-reviews" class="dk-tab-panel" style="display:none;">
            <div class="all-reviews" style="display:flex;flex-direction:column;gap:16px;">
                @foreach($reviews as $review)
                <div style="background:var(--dk-panel);border:1px solid var(--dk-border);border-radius:var(--dk-radius);padding:16px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                        <div style="width:38px;height:38px;border-radius:50%;background:var(--dk-red);display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;color:#fff;flex-shrink:0;">
                            {{ strtoupper(substr($review->user?->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight:700;color:var(--dk-white);font-size:14px;">{{ $review->user?->name ?? __('Anonymous') }}</div>
                            <div class="dk-stars">
                                @for($i=1;$i<=5;$i++)
                                    <i class="mdi {{ $i <= $review->rating ? 'mdi-star' : 'mdi-star-outline' }}" style="color:{{ $i <= $review->rating ? '#f5c518' : 'var(--dk-muted)' }};font-size:14px;"></i>
                                @endfor
                            </div>
                        </div>
                        <div style="margin-left:auto;font-size:11px;color:var(--dk-muted);">{{ $review->created_at->format('d M Y') }}</div>
                    </div>
                    <p style="color:var(--dk-silver);font-size:14px;margin:0;">{{ $review->review_text }}</p>
                </div>
                @endforeach
            </div>

            {{-- Leave Review --}}
            @auth('web')
            <div style="margin-top:24px;background:var(--dk-panel);border:1px solid var(--dk-border);border-radius:var(--dk-radius);padding:20px;">
                <div style="font-weight:700;color:var(--dk-white);margin-bottom:12px;">{{ __('Leave a Review') }}</div>
                <div style="display:flex;gap:6px;margin-bottom:12px;">
                    @for($i=1;$i<=5;$i++)
                    <i class="mdi mdi-star review-star" data-value="{{ $i }}" style="font-size:28px;color:var(--dk-muted);cursor:pointer;transition:color .15s;"></i>
                    @endfor
                </div>
                <textarea id="review-text" rows="3" style="width:100%;background:var(--dk-surface);border:1.5px solid var(--dk-border);border-radius:var(--dk-radius);color:var(--dk-white);padding:10px 14px;font-size:14px;outline:none;resize:vertical;" placeholder="{{ __('Your review…') }}"></textarea>
                <button id="review-submit-btn" data-product="{{ $product->id }}" class="dk-btn dk-btn-red mt-2" style="padding:10px 24px;">
                    {{ __('Submit Review') }}
                </button>
            </div>
            @endauth
        </div>
        @endif

        @if($product->refund_policy)
        <div id="dk-tab-refund" class="dk-tab-panel" style="display:none;">
            <div class="dk-tab-panel-content">{!! $product->refund_policy->description ?? '' !!}</div>
        </div>
        @endif

    </div>
</section>

{{-- Related Products --}}
@if($related_products->isNotEmpty())
<section class="py-5">
    <div class="container">
        <div class="dk-section-header mb-4">
            <h2 class="dk-section-title">{{ __('Related Products') }}</h2>
        </div>
        <div class="row g-3">
            @foreach($related_products as $rp)
            @php
                $rp_data  = get_digital_product_dynamic_price($rp);
                $rp_img_d = get_attachment_image_by_id($rp->image_id ?? null, 'grid');
                $rp_img   = $rp_img_d['img_url'] ?? null;
            @endphp
            <div class="col-md-4">
                <a href="{{ dynamicRoute($rp->slug) }}" class="dk-blog-card-link">
                    <div class="dk-card" style="display:flex;flex-direction:column;">
                        <div class="dk-card-img">
                            @if($rp_img)
                                <img src="{{ $rp_img }}" alt="{{ $rp->name }}" style="height:160px;object-fit:cover;width:100%;">
                            @else
                                <div class="dk-card-no-img" style="height:160px;font-size:48px;"><i class="mdi mdi-file-document-outline"></i></div>
                            @endif
                        </div>
                        <div class="dk-card-body">
                            @if($rp->category)
                                <div class="dk-card-cat">{{ $rp->category->name }}</div>
                            @endif
                            <div class="dk-card-name">{{ \Illuminate\Support\Str::words($rp->name, 8) }}</div>
                            <div class="dk-card-price">
                                <span class="dk-price-sale">{{ amount_with_currency_symbol($rp_data['sale_price']) }}</span>
                                @if($rp_data['discount'] > 0)
                                    <span class="dk-price-orig">{{ amount_with_currency_symbol($rp_data['regular_price']) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
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

    // Tab switching
    $(document).on('click', '.dk-tab-btn', function(){
        var target = $(this).data('tab');
        $('.dk-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.dk-tab-panel').hide();
        $('#'+target).show();
    });

    // Star rating hover
    var selectedRating = 0;
    $(document).on('mouseover', '.review-star', function(){
        var val = $(this).data('value');
        $('.review-star').each(function(i){ $(this).css('color', i < val ? '#f5c518' : 'var(--dk-muted)'); });
    }).on('mouseout', '.review-star', function(){
        $('.review-star').each(function(i){ $(this).css('color', i < selectedRating ? '#f5c518' : 'var(--dk-muted)'); });
    }).on('click', '.review-star', function(){
        selectedRating = $(this).data('value');
        $(this).addClass('rating-selected').attr('data-value', selectedRating);
        $('.review-star').each(function(i){ $(this).css('color', i < selectedRating ? '#f5c518' : 'var(--dk-muted)'); });
    });

    // Submit review
    $(document).on('click', '#review-submit-btn', function(){
        var btn = $(this);
        var product_id = btn.data('product');
        var rating = selectedRating;
        var review_text = $('#review-text').val().trim();

        if (!rating) { toastr.warning('{{ __("Please select a rating") }}'); return; }
        if (!review_text) { toastr.warning('{{ __("Please enter a review") }}'); return; }

        $.ajax({
            url: '{{ theme_digital_product_review_url() }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', product_id: product_id, rating: rating, review_text: review_text },
            beforeSend: function(){ btn.text('{{ __("Submitting...") }}'); },
            success: function(data){
                if (data.type === 'success') { toastr.success(data.msg); setTimeout(()=>location.reload(), 1000); }
                else { toastr.error(data.msg); btn.text('{{ __("Submit Review") }}'); }
            },
            error: function(){ btn.text('{{ __("Submit Review") }}'); }
        });
    });
})(jQuery);
</script>
@endsection
