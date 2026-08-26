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

<div class="fp-page-hero">
    <div class="container">
        <h1 class="fp-page-title" style="font-size:clamp(22px,3vw,36px);">{{ \Illuminate\Support\Str::words($product->name, 6) }}</h1>
        <ul class="fp-breadcrumb-list">
            <li><a href="{{ theme_home_url() }}">{{ __('Home') }}</a></li>
            <li><a href="{{ theme_digital_shop_url() }}">{{ __('Digital Shop') }}</a></li>
            <li>{{ \Illuminate\Support\Str::words($product->name, 5) }}</li>
        </ul>
    </div>
</div>

<section style="padding:48px 0;background:var(--fp-bg);">
    <div class="container">
        <div class="row g-4">

            {{-- Cover Image --}}
            <div class="col-lg-3 col-md-4">
                <div style="position:sticky;top:100px;">
                    @if($img_url)
                        <img src="{{ $img_url }}" alt="{{ $product->name }}"
                             style="width:100%;border-radius:var(--fp-radius);border:1px solid var(--fp-border);object-fit:cover;aspect-ratio:3/4;">
                    @else
                        <div style="width:100%;aspect-ratio:3/4;border-radius:var(--fp-radius);background:var(--fp-card);border:1px solid var(--fp-border);display:flex;align-items:center;justify-content:center;font-size:80px;">
                            <i class="mdi mdi-file-document-outline" style="color:var(--fp-green);opacity:.3;"></i>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Product Info --}}
            <div class="col-lg-6 col-md-8">

                @if($category)
                    <div style="font-family:var(--fp-font-head);font-size:11px;color:var(--fp-green);text-transform:uppercase;letter-spacing:2px;font-weight:700;margin-bottom:8px;">{{ $category->name }}</div>
                @endif

                <h1 style="font-family:var(--fp-font-head);font-size:clamp(22px,3vw,32px);font-weight:800;color:var(--fp-text);text-transform:uppercase;letter-spacing:1px;line-height:1.15;margin-bottom:12px;">{!! $product->name !!}</h1>

                @if($author_name)
                    <div style="font-size:13px;color:var(--fp-muted);margin-bottom:10px;">
                        <i class="mdi mdi-account-outline" style="color:var(--fp-green);"></i> {{ $author_name }}
                    </div>
                @endif

                @if($avg_rating > 0)
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:16px;color:var(--fp-green);">
                    @for($i=1;$i<=5;$i++)
                        <i class="mdi {{ $i <= $avg_rating ? 'mdi-star' : 'mdi-star-outline' }}" style="font-size:16px;"></i>
                    @endfor
                    <span style="font-size:12px;color:var(--fp-muted);">({{ $reviews->count() }})</span>
                </div>
                @endif

                <div style="display:flex;align-items:baseline;gap:10px;margin-bottom:20px;">
                    <span style="font-family:var(--fp-font-head);font-size:32px;font-weight:800;color:var(--fp-green);">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($discount > 0)
                        <span style="font-size:16px;color:var(--fp-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($regular_price) }}</span>
                        <span class="fp-card-badge" style="position:static;display:inline-block;">{{ $discount }}% {{ __('OFF') }}</span>
                    @endif
                </div>

                {{-- Specs --}}
                <div style="background:var(--fp-card);border:1px solid var(--fp-border);border-radius:var(--fp-radius);padding:16px;margin-bottom:20px;">
                    <div class="row g-2">
                        @foreach([['file_format',__('Format'),'strtoupper'],['number_of_pages',__('Pages'),null],['language',__('Language'),null],['file_size',__('File Size'),null]] as [$field,$label,$fn])
                        @if($product->additionalFields?->$field)
                        <div class="col-6">
                            <div style="font-family:var(--fp-font-head);font-size:10px;color:var(--fp-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:2px;">{{ $label }}</div>
                            <div style="font-family:var(--fp-font-head);font-weight:700;color:var(--fp-text);">{{ $fn ? $fn($product->additionalFields->$field) : $product->additionalFields->$field }}</div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>

                @if($product->short_description)
                <div style="color:var(--fp-muted);font-size:14px;line-height:1.7;margin-bottom:20px;">
                    {!! $product->short_description !!}
                </div>
                @endif

                @if($product->tag && $product->tag->isNotEmpty())
                <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
                    <span style="font-family:var(--fp-font-head);font-size:10px;color:var(--fp-muted);text-transform:uppercase;letter-spacing:1px;">{{ __('Tags:') }}</span>
                    @foreach($product->tag as $tag)
                        <span style="font-family:var(--fp-font-head);font-size:11px;border:1px solid var(--fp-border);color:var(--fp-muted);padding:2px 8px;border-radius:var(--fp-radius);text-transform:uppercase;letter-spacing:1px;">{{ $tag->tag_name }}</span>
                    @endforeach
                </div>
                @endif

            </div>

            {{-- Sidebar: Purchase --}}
            <div class="col-lg-3">
                <div class="fp-sidebar-card" style="position:sticky;top:100px;">
                    <div style="font-family:var(--fp-font-head);font-size:28px;font-weight:800;color:var(--fp-green);margin-bottom:4px;">{{ amount_with_currency_symbol($sale_price) }}</div>
                    @if($discount > 0)
                    <div style="font-size:13px;color:var(--fp-muted);margin-bottom:16px;">
                        {{ __('Was') }} <span style="text-decoration:line-through;">{{ amount_with_currency_symbol($regular_price) }}</span>
                        <span style="color:var(--fp-green);font-weight:700;">— {{ $discount }}% {{ __('off') }}</span>
                    </div>
                    @endif

                    @auth('web')
                    <form action="{{ theme_digital_product_add_to_cart_url() }}" method="POST" class="mb-3">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="fp-btn fp-btn-primary w-100 justify-content-center" style="padding:13px;font-size:15px;">
                            <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                        </button>
                    </form>
                    @else
                    <a href="{{ theme_login_url() }}" class="fp-btn fp-btn-primary w-100 justify-content-center mb-3" style="padding:13px;font-size:15px;display:flex;">
                        <i class="mdi mdi-login"></i> {{ __('Sign In to Purchase') }}
                    </a>
                    @endauth

                    <div class="add-to-wishlist-btn fp-btn fp-btn-outline w-100 justify-content-center mb-4" data-product_id="{{ $product->id }}" style="cursor:pointer;padding:12px;display:flex;">
                        <i class="mdi mdi-heart-outline"></i>&nbsp;{{ __('Wishlist') }}
                    </div>

                    <div style="border-top:1px solid var(--fp-border);padding-top:16px;display:flex;flex-direction:column;gap:10px;">
                        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--fp-muted);">
                            <i class="mdi mdi-download-outline" style="color:var(--fp-green);font-size:18px;"></i>
                            {{ __('Instant digital download') }}
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--fp-muted);">
                            <i class="mdi mdi-shield-check-outline" style="color:var(--fp-green);font-size:18px;"></i>
                            {{ __('Secure checkout') }}
                        </div>
                        @if($product->refund_policy)
                        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--fp-muted);">
                            <i class="mdi mdi-refresh" style="color:var(--fp-green);font-size:18px;"></i>
                            {{ __('Refund policy available') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- Tabs --}}
<section style="padding:48px 0;background:var(--fp-surface);border-top:1px solid var(--fp-border);">
    <div class="container">

        <div class="fp-tab-nav mb-4">
            <button class="fp-tab-nav-btn active" data-tab="fp-dp-desc">{{ __('Description') }}</button>
            @if($reviews->isNotEmpty())
            <button class="fp-tab-nav-btn" data-tab="fp-dp-reviews">{{ __('Reviews') }} ({{ $reviews->count() }})</button>
            @endif
            @if($product->refund_policy)
            <button class="fp-tab-nav-btn" data-tab="fp-dp-refund">{{ __('Refund Policy') }}</button>
            @endif
        </div>

        <div id="fp-dp-desc" class="fp-tab-panel active" style="color:var(--fp-text);line-height:1.8;">
            @if($product->description)
                {!! $product->description !!}
            @else
                <p style="color:var(--fp-muted);">{{ __('No description available.') }}</p>
            @endif
        </div>

        @if($reviews->isNotEmpty())
        <div id="fp-dp-reviews" class="fp-tab-panel">
            <div class="d-flex flex-column gap-3">
                @foreach($reviews as $review)
                <div style="background:var(--fp-card);border:1px solid var(--fp-border);border-radius:var(--fp-radius);padding:16px;">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                        <div style="width:38px;height:38px;border-radius:var(--fp-radius);background:var(--fp-green-glow);border:1px solid var(--fp-green);display:flex;align-items:center;justify-content:center;font-family:var(--fp-font-head);font-size:16px;font-weight:800;color:var(--fp-green);flex-shrink:0;">
                            {{ strtoupper(substr($review->user?->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-family:var(--fp-font-head);font-weight:700;color:var(--fp-text);font-size:13px;text-transform:uppercase;letter-spacing:.5px;">{{ $review->user?->name ?? __('Anonymous') }}</div>
                            <div style="color:var(--fp-green);font-size:13px;">
                                @for($i=1;$i<=5;$i++)<i class="mdi mdi-{{ $i <= $review->rating ? 'star' : 'star-outline' }}"></i>@endfor
                            </div>
                        </div>
                        <div style="margin-left:auto;font-size:12px;color:var(--fp-muted);">{{ $review->created_at->format('d M Y') }}</div>
                    </div>
                    <p style="color:var(--fp-muted);font-size:14px;line-height:1.6;margin:0;">{{ $review->review_text }}</p>
                </div>
                @endforeach
            </div>

            @auth('web')
            <div style="background:var(--fp-card);border:1px solid var(--fp-border);border-radius:var(--fp-radius);padding:24px;margin-top:24px;">
                <div class="fp-sidebar-title" style="margin-bottom:12px;">{{ __('Leave a Review') }}</div>
                <div style="display:flex;gap:6px;margin-bottom:14px;font-size:28px;color:var(--fp-border);">
                    @for($i=1;$i<=5;$i++)
                    <i class="mdi mdi-star fp-dp-review-star" data-value="{{ $i }}" style="cursor:pointer;transition:color .15s;"></i>
                    @endfor
                </div>
                <textarea id="fp-dp-review-text" rows="3" class="fp-price-input mb-3"
                          style="height:auto;resize:vertical;width:100%;"
                          placeholder="{{ __('Your review…') }}"></textarea>
                <button id="fp-dp-review-submit" data-product="{{ $product->id }}" class="fp-btn fp-btn-primary">
                    {{ __('Submit Review') }}
                </button>
            </div>
            @endauth
        </div>

        @if($product->refund_policy)
        <div id="fp-dp-refund" class="fp-tab-panel" style="color:var(--fp-text);line-height:1.8;">
            {!! $product->refund_policy->description ?? '' !!}
        </div>
        @endif

        @endif

    </div>
</section>

{{-- Related Products --}}
@if($related_products->isNotEmpty())
<section style="padding:48px 0;background:var(--fp-bg);border-top:1px solid var(--fp-border);">
    <div class="container">
        <div class="fp-sec-heading" style="margin-bottom:24px;">
            <div class="fp-sec-title">{{ __('Related') }} <span>{{ __('Products') }}</span></div>
        </div>
        <div class="row g-3">
            @foreach($related_products as $rp)
            @php $rp_dp = get_digital_product_dynamic_price($rp); $rp_img = get_attachment_image_by_id($rp->image_id ?? null, 'grid')['img_url'] ?? null; @endphp
            <div class="col-md-4">
                <div class="fp-card">
                    <div class="fp-card-img">
                        @if($rp_img)
                            <a href="{{ dynamicRoute($rp->slug) }}"><img src="{{ $rp_img }}" alt="{{ $rp->name }}" loading="lazy"></a>
                        @else
                            <a href="{{ dynamicRoute($rp->slug) }}" class="fp-img-ph">
                                <i class="mdi mdi-file-document-outline" style="font-size:48px;color:var(--fp-green);opacity:.3;"></i>
                            </a>
                        @endif
                    </div>
                    <div class="fp-card-body">
                        @if($rp->category)
                            <div class="fp-card-cat">{{ $rp->category->name }}</div>
                        @endif
                        <div class="fp-card-title">
                            <a href="{{ dynamicRoute($rp->slug) }}">{{ \Illuminate\Support\Str::words($rp->name, 7) }}</a>
                        </div>
                        <div class="fp-card-footer">
                            <div class="fp-price">{{ amount_with_currency_symbol($rp_dp['sale_price']) }}</div>
                            <a href="{{ dynamicRoute($rp->slug) }}" class="fp-atc-btn fp-btn-sm"><i class="mdi mdi-eye-outline"></i></a>
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
(function ($) {
    'use strict';

    $(document).on('click', '.fp-tab-nav-btn', function () {
        var target = $(this).data('tab');
        $('.fp-tab-nav-btn').removeClass('active');
        $(this).addClass('active');
        $('.fp-tab-panel').removeClass('active').hide();
        $('#' + target).addClass('active').show();
    });

    var selectedRating = 0;
    $(document).on('mouseover', '.fp-dp-review-star', function () {
        var val = $(this).data('value');
        $('.fp-dp-review-star').each(function (i) { $(this).css('color', i < val ? 'var(--fp-green)' : 'var(--fp-border)'); });
    }).on('mouseout', '.fp-dp-review-star', function () {
        $('.fp-dp-review-star').each(function (i) { $(this).css('color', i < selectedRating ? 'var(--fp-green)' : 'var(--fp-border)'); });
    }).on('click', '.fp-dp-review-star', function () {
        selectedRating = $(this).data('value');
        $('.fp-dp-review-star').each(function (i) { $(this).css('color', i < selectedRating ? 'var(--fp-green)' : 'var(--fp-border)'); });
    });

    $(document).on('click', '#fp-dp-review-submit', function () {
        var btn = $(this), product_id = btn.data('product'), review_text = $('#fp-dp-review-text').val().trim();
        if (!selectedRating) { toastr.warning('{{ __("Please select a rating") }}'); return; }
        if (!review_text) { toastr.warning('{{ __("Please enter a review") }}'); return; }
        $.ajax({
            url: '{{ theme_digital_product_review_url() }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', product_id: product_id, rating: selectedRating, review_text: review_text },
            beforeSend: function () { btn.text('{{ __("Submitting...") }}'); },
            success: function (data) {
                if (data.type === 'success') { toastr.success(data.msg); setTimeout(() => location.reload(), 1000); }
                else { toastr.error(data.msg); btn.text('{{ __("Submit Review") }}'); }
            },
            error: function () { btn.text('{{ __("Submit Review") }}'); }
        });
    });
})(jQuery);
</script>
@endsection
