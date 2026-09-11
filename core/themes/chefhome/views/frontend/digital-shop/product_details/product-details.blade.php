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

<div class="ch-page-banner">
    <div class="container ch-page-banner-content">
        <h1>{{ \Illuminate\Support\Str::words($product->name, 6) }}</h1>
        <div class="ch-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ theme_digital_shop_url() }}">{{ __('Digital Shop') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ \Illuminate\Support\Str::words($product->name, 5) }}</span>
        </div>
    </div>
</div>

<section style="padding:48px 0;background:var(--ch-cream);">
    <div class="container">
        <div class="row g-4">

            {{-- Cover Image --}}
            <div class="col-lg-3 col-md-4">
                <div style="position:sticky;top:100px;">
                    @if($img_url)
                        <img src="{{ $img_url }}" alt="{{ $product->name }}"
                             style="width:100%;border-radius:var(--ch-radius);border:1.5px solid var(--ch-border);object-fit:cover;aspect-ratio:3/4;box-shadow:var(--ch-shadow);">
                    @else
                        <div style="width:100%;aspect-ratio:3/4;border-radius:var(--ch-radius);background:var(--ch-warm);border:1.5px solid var(--ch-border);display:flex;align-items:center;justify-content:center;font-size:64px;">
                            <i class="las la-file-alt" style="color:var(--ch-muted);"></i>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Product Info --}}
            <div class="col-lg-6 col-md-8">

                @if($category)
                    <div style="font-size:11px;color:var(--ch-red);text-transform:uppercase;letter-spacing:1px;font-weight:700;margin-bottom:8px;">{{ $category->name }}</div>
                @endif

                <h1 style="font-size:clamp(20px,3vw,28px);font-weight:700;color:var(--ch-dark);line-height:1.25;margin-bottom:12px;">{!! $product->name !!}</h1>

                @if($author_name)
                    <div style="font-size:13px;color:var(--ch-muted);margin-bottom:10px;">
                        <i class="las la-user-circle" style="color:var(--ch-red);"></i> {{ $author_name }}
                    </div>
                @endif

                @if($avg_rating > 0)
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:16px;">
                    @for($i=1;$i<=5;$i++)
                        <i class="las {{ $i <= $avg_rating ? 'la-star' : ($i - $avg_rating < 1 ? 'la-star-half-alt' : 'la-star') }}"
                           style="color:{{ $i <= $avg_rating ? 'var(--ch-amber)' : 'var(--ch-border)' }};font-size:16px;"></i>
                    @endfor
                    <span style="font-size:12px;color:var(--ch-muted);">({{ $reviews->count() }})</span>
                </div>
                @endif

                <div style="display:flex;align-items:baseline;gap:10px;margin-bottom:20px;">
                    <span style="font-size:28px;font-weight:800;color:var(--ch-red);">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($discount > 0)
                        <span style="font-size:16px;color:var(--ch-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($regular_price) }}</span>
                        <span class="ch-card-badge ch-badge-sale" style="position:static;display:inline-block;">{{ $discount }}% {{ __('off') }}</span>
                    @endif
                </div>

                {{-- Specs --}}
                <div style="background:#fff;border:1px solid var(--ch-border);border-radius:var(--ch-radius-sm);padding:16px;margin-bottom:20px;">
                    <div class="row g-2">
                        @if($product->additionalFields?->file_format)
                        <div class="col-6">
                            <div style="font-size:11px;color:var(--ch-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('Format') }}</div>
                            <div style="font-weight:700;color:var(--ch-dark);">{{ strtoupper($product->additionalFields->file_format) }}</div>
                        </div>
                        @endif
                        @if($product->additionalFields?->number_of_pages)
                        <div class="col-6">
                            <div style="font-size:11px;color:var(--ch-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('Pages') }}</div>
                            <div style="font-weight:700;color:var(--ch-dark);">{{ $product->additionalFields->number_of_pages }}</div>
                        </div>
                        @endif
                        @if($product->additionalFields?->language)
                        <div class="col-6">
                            <div style="font-size:11px;color:var(--ch-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('Language') }}</div>
                            <div style="font-weight:700;color:var(--ch-dark);">{{ $product->additionalFields->language }}</div>
                        </div>
                        @endif
                        @if($product->additionalFields?->file_size)
                        <div class="col-6">
                            <div style="font-size:11px;color:var(--ch-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('File Size') }}</div>
                            <div style="font-weight:700;color:var(--ch-dark);">{{ $product->additionalFields->file_size }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                @if($product->short_description)
                <div style="color:var(--ch-muted);font-size:14px;line-height:1.7;margin-bottom:20px;">
                    {!! $product->short_description !!}
                </div>
                @endif

                @if($product->tag && $product->tag->isNotEmpty())
                <div class="mb-3 d-flex flex-wrap gap-1 align-items-center">
                    <span style="font-size:11px;color:var(--ch-muted);text-transform:uppercase;letter-spacing:.8px;">{{ __('Tags:') }}</span>
                    @foreach($product->tag as $tag)
                        <span class="ch-tag" style="cursor:default;">{{ $tag->tag_name }}</span>
                    @endforeach
                </div>
                @endif

            </div>

            {{-- Sidebar: Purchase --}}
            <div class="col-lg-3">
                <div class="ch-sidebar-card" style="position:sticky;top:100px;">

                    <div style="font-size:26px;font-weight:800;color:var(--ch-red);margin-bottom:4px;">{{ amount_with_currency_symbol($sale_price) }}</div>
                    @if($discount > 0)
                    <div style="font-size:13px;color:var(--ch-muted);margin-bottom:16px;">
                        {{ __('Was') }} <span style="text-decoration:line-through;">{{ amount_with_currency_symbol($regular_price) }}</span>
                        — <span style="color:var(--ch-red);font-weight:700;">{{ $discount }}% {{ __('off') }}</span>
                    </div>
                    @endif

                    @auth('web')
                    <form action="{{ theme_digital_product_add_to_cart_url() }}" method="POST" class="mb-2">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="ch-btn ch-btn-primary w-100 justify-content-center" style="font-size:15px;padding:13px;">
                            <i class="las la-cart-plus"></i> {{ __('Add to Cart') }}
                        </button>
                    </form>
                    @else
                    <a href="{{ theme_login_url() }}" class="ch-btn ch-btn-primary w-100 justify-content-center mb-2" style="font-size:15px;padding:13px;display:flex;">
                        <i class="las la-sign-in-alt"></i> {{ __('Sign In to Purchase') }}
                    </a>
                    @endauth

                    <div class="ch-btn ch-btn-outline w-100 justify-content-center add-to-wishlist-btn mb-4" data-product_id="{{ $product->id }}" style="cursor:pointer;padding:12px;font-size:14px;display:flex;">
                        <i class="las la-heart"></i> {{ __('Wishlist') }}
                    </div>

                    <div style="border-top:1px solid var(--ch-border);padding-top:16px;display:flex;flex-direction:column;gap:10px;">
                        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ch-muted);">
                            <i class="las la-download" style="color:var(--ch-red);font-size:18px;"></i>
                            {{ __('Instant digital download') }}
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ch-muted);">
                            <i class="las la-shield-alt" style="color:var(--ch-red);font-size:18px;"></i>
                            {{ __('Secure checkout') }}
                        </div>
                        @if($product->refund_policy)
                        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ch-muted);">
                            <i class="las la-undo" style="color:var(--ch-red);font-size:18px;"></i>
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
<section style="padding:48px 0;background:#fff;border-top:1px solid var(--ch-border);">
    <div class="container">

        <div class="ch-tab-nav mb-4">
            <button class="ch-tab-btn active" data-tab="ch-dp-desc">{{ __('Description') }}</button>
            @if($reviews->isNotEmpty())
            <button class="ch-tab-btn" data-tab="ch-dp-reviews">{{ __('Reviews') }} ({{ $reviews->count() }})</button>
            @endif
            @if($product->refund_policy)
            <button class="ch-tab-btn" data-tab="ch-dp-refund">{{ __('Refund Policy') }}</button>
            @endif
        </div>

        <div id="ch-dp-desc" class="ch-tab-panel active">
            @if($product->description)
                <div class="ch-article-content">{!! $product->description !!}</div>
            @else
                <p style="color:var(--ch-muted);">{{ __('No description available.') }}</p>
            @endif
        </div>

        @if($reviews->isNotEmpty())
        <div id="ch-dp-reviews" class="ch-tab-panel" style="display:none;">
            <div class="d-flex flex-column gap-3">
                @foreach($reviews as $review)
                <div style="background:var(--ch-warm);border:1px solid var(--ch-border);border-radius:var(--ch-radius-sm);padding:16px;">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                        <div style="width:38px;height:38px;border-radius:50%;background:var(--ch-red);display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;color:#fff;flex-shrink:0;">
                            {{ strtoupper(substr($review->user?->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight:700;color:var(--ch-dark);font-size:13px;">{{ $review->user?->name ?? __('Anonymous') }}</div>
                            <div style="display:flex;gap:2px;">
                                @for($i=1;$i<=5;$i++)
                                    <i class="las la-star" style="color:{{ $i <= $review->rating ? 'var(--ch-amber)' : 'var(--ch-border)' }};font-size:13px;"></i>
                                @endfor
                            </div>
                        </div>
                        <div style="margin-left:auto;font-size:12px;color:var(--ch-muted);">{{ $review->created_at->format('d M Y') }}</div>
                    </div>
                    <p style="color:var(--ch-muted);font-size:14px;line-height:1.6;margin:0;">{{ $review->review_text }}</p>
                </div>
                @endforeach
            </div>

            @auth('web')
            <div style="background:var(--ch-warm);border:1px solid var(--ch-border);border-radius:var(--ch-radius);padding:24px;margin-top:24px;">
                <div class="ch-sidebar-title" style="margin-bottom:12px;">{{ __('Leave a Review') }}</div>
                <div style="display:flex;gap:6px;margin-bottom:14px;">
                    @for($i=1;$i<=5;$i++)
                    <i class="las la-star ch-review-star" data-value="{{ $i }}" style="font-size:28px;color:var(--ch-border);cursor:pointer;transition:color .15s;"></i>
                    @endfor
                </div>
                <textarea id="ch-review-text" rows="3" class="ch-price-input mb-3"
                          style="height:auto;resize:vertical;"
                          placeholder="{{ __('Your review…') }}"></textarea>
                <button id="ch-review-submit" data-product="{{ $product->id }}" class="ch-btn ch-btn-primary">
                    {{ __('Submit Review') }}
                </button>
            </div>
            @endauth
        </div>

        @if($product->refund_policy)
        <div id="ch-dp-refund" class="ch-tab-panel" style="display:none;">
            <div class="ch-article-content">{!! $product->refund_policy->description ?? '' !!}</div>
        </div>
        @endif

        @endif {{-- end @if($reviews->isNotEmpty()) --}}

    </div>
</section>

{{-- Related Products --}}
@if($related_products->isNotEmpty())
<section style="padding:48px 0;background:var(--ch-warm);">
    <div class="container">
        <div class="ch-sec-heading" style="margin-bottom:24px;">
            <div class="ch-sec-title">{{ __('Related Products') }}</div>
        </div>
        <div class="row g-3">
            @foreach($related_products as $rp)
            @php
                $rp_dp  = get_digital_product_dynamic_price($rp);
                $rp_img = get_attachment_image_by_id($rp->image_id ?? null, 'grid')['img_url'] ?? null;
            @endphp
            <div class="col-md-4">
                <div class="ch-card">
                    <div class="ch-card-img" style="height:180px;">
                        @if($rp_img)
                            <a href="{{ dynamicRoute($rp->slug) }}"><img src="{{ $rp_img }}" alt="{{ $rp->name }}" loading="lazy"></a>
                        @else
                            <a href="{{ dynamicRoute($rp->slug) }}" class="ch-img-ph" style="height:180px;font-size:48px;">
                                <i class="las la-file-alt"></i>
                            </a>
                        @endif
                    </div>
                    <div class="ch-card-body">
                        @if($rp->category)
                            <div style="font-size:11px;color:var(--ch-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">{{ $rp->category->name }}</div>
                        @endif
                        <div class="ch-card-title">
                            <a href="{{ dynamicRoute($rp->slug) }}">{{ \Illuminate\Support\Str::words($rp->name, 7) }}</a>
                        </div>
                        <div class="ch-card-footer">
                            <div class="ch-price">{{ amount_with_currency_symbol($rp_dp['sale_price']) }}</div>
                            <a href="{{ dynamicRoute($rp->slug) }}" class="ch-add-btn" style="font-size:11px;padding:6px 10px;">
                                <i class="las la-eye"></i>
                            </a>
                        </div>
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

    $(document).on('click', '.ch-tab-btn', function(){
        var target = $(this).data('tab');
        $('.ch-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.ch-tab-panel').hide();
        $('#'+target).show();
    });

    var selectedRating = 0;
    $(document).on('mouseover', '.ch-review-star', function(){
        var val = $(this).data('value');
        $('.ch-review-star').each(function(i){ $(this).css('color', i < val ? 'var(--ch-amber)' : 'var(--ch-border)'); });
    }).on('mouseout', '.ch-review-star', function(){
        $('.ch-review-star').each(function(i){ $(this).css('color', i < selectedRating ? 'var(--ch-amber)' : 'var(--ch-border)'); });
    }).on('click', '.ch-review-star', function(){
        selectedRating = $(this).data('value');
        $('.ch-review-star').each(function(i){ $(this).css('color', i < selectedRating ? 'var(--ch-amber)' : 'var(--ch-border)'); });
    });

    $(document).on('click', '#ch-review-submit', function(){
        var btn = $(this);
        var product_id  = btn.data('product');
        var rating      = selectedRating;
        var review_text = $('#ch-review-text').val().trim();

        if (!rating) { toastr.warning('{{ __("Please select a rating") }}'); return; }
        if (!review_text) { toastr.warning('{{ __("Please enter a review") }}'); return; }

        $.ajax({
            url: '{{ theme_digital_product_review_url() }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', product_id: product_id, rating: rating, review_text: review_text },
            beforeSend: function(){ btn.text('{{ __("Submitting...") }}'); },
            success: function(data){
                if (data.type === 'success') { toastr.success(data.msg); setTimeout(function(){ location.reload(); }, 1000); }
                else { toastr.error(data.msg); btn.html('{{ __("Submit Review") }}'); }
            },
            error: function(){ btn.html('{{ __("Submit Review") }}'); }
        });
    });

})(jQuery);
</script>
@endsection
