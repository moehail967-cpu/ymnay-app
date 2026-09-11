@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $product->name !!} @endsection

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

<div class="kv-page-banner">
    <div class="container kv-page-banner-content">
        <h1>{{ \Illuminate\Support\Str::words($product->name, 6) }}</h1>
        <div class="kv-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ theme_digital_shop_url() }}">{{ __('Digital Shop') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ \Illuminate\Support\Str::words($product->name, 5) }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:36px;padding-bottom:72px;">
    <div class="row g-4">

        {{-- Cover image --}}
        <div class="col-lg-3 col-md-4">
            <div style="position:sticky;top:100px;">
                @if($img_url)
                    <img src="{{ $img_url }}" alt="{{ $product->name }}"
                         style="width:100%;border-radius:var(--kv-radius);border:2.5px solid var(--kv-border);object-fit:cover;aspect-ratio:3/4;">
                @else
                    <div style="width:100%;aspect-ratio:3/4;border-radius:var(--kv-radius);background:var(--kv-yellow-lt);border:2.5px solid var(--kv-border);display:flex;align-items:center;justify-content:center;font-size:72px;color:var(--kv-red);">
                        <i class="las la-file-download"></i>
                    </div>
                @endif
            </div>
        </div>

        {{-- Product info --}}
        <div class="col-lg-6 col-md-8">
            @if($category)
                <span class="kv-card-badge kv-badge-new" style="position:static;display:inline-block;margin-bottom:10px;">{{ $category->name }}</span>
            @endif

            <h1 style="font-size:22px;font-weight:800;color:var(--kv-dark);margin-bottom:10px;line-height:1.3;">{!! $product->name !!}</h1>

            @if($author_name)
            <div style="font-size:13px;color:var(--kv-muted);margin-bottom:10px;">
                <i class="las la-user" style="color:var(--kv-red);"></i> {{ $author_name }}
            </div>
            @endif

            @if($avg_rating > 0)
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:14px;">
                {!! render_star_rating_markup($avg_rating) !!}
                <span style="font-size:12px;color:var(--kv-muted);">({{ $reviews->count() }})</span>
            </div>
            @endif

            {{-- Price --}}
            <div style="display:flex;align-items:baseline;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
                <span class="kv-price" style="font-size:28px;">{{ amount_with_currency_symbol($sale_price) }}</span>
                @if($discount > 0)
                    <span class="kv-price-old" style="font-size:15px;">{{ amount_with_currency_symbol($regular_price) }}</span>
                    <span class="kv-card-badge kv-badge-sale" style="position:static;display:inline-block;">{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            {{-- Meta specs --}}
            @if($product->additionalFields?->file_format || $product->additionalFields?->number_of_pages || $product->additionalFields?->language || $product->additionalFields?->file_size || ($product->downloads_count ?? 0))
            <div class="kv-sidebar-card mb-3">
                <div class="row g-3">
                    @if($product->additionalFields?->file_format)
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--kv-muted);font-weight:700;text-transform:uppercase;margin-bottom:2px;">{{ __('Format') }}</div>
                        <div style="color:var(--kv-dark);font-size:14px;font-weight:800;">{{ strtoupper($product->additionalFields->file_format) }}</div>
                    </div>
                    @endif
                    @if($product->additionalFields?->number_of_pages)
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--kv-muted);font-weight:700;text-transform:uppercase;margin-bottom:2px;">{{ __('Pages') }}</div>
                        <div style="color:var(--kv-dark);font-size:14px;font-weight:800;">{{ $product->additionalFields->number_of_pages }}</div>
                    </div>
                    @endif
                    @if($product->additionalFields?->language)
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--kv-muted);font-weight:700;text-transform:uppercase;margin-bottom:2px;">{{ __('Language') }}</div>
                        <div style="color:var(--kv-dark);font-size:14px;font-weight:800;">{{ $product->additionalFields->getLanguage?->name }}</div>
                    </div>
                    @endif
                    @if($product->additionalFields?->file_size)
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--kv-muted);font-weight:700;text-transform:uppercase;margin-bottom:2px;">{{ __('File Size') }}</div>
                        <div style="color:var(--kv-dark);font-size:14px;font-weight:800;">{{ $product->additionalFields->file_size }}</div>
                    </div>
                    @endif
                    @if($product->downloads_count ?? 0)
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--kv-muted);font-weight:700;text-transform:uppercase;margin-bottom:2px;">{{ __('Downloads') }}</div>
                        <div style="color:var(--kv-dark);font-size:14px;font-weight:800;">{{ $product->downloads_count }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if($product->short_description)
            <div style="font-size:14px;color:var(--kv-muted);line-height:1.8;margin-bottom:16px;">
                {!! $product->short_description !!}
            </div>
            @endif

            @if($product->tag && $product->tag->isNotEmpty())
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span style="font-size:12px;color:var(--kv-muted);font-weight:700;">{{ __('Tags:') }}</span>
                @foreach($product->tag as $tag)
                    <span class="kv-tag">{{ $tag->tag_name }}</span>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Purchase sidebar --}}
        <div class="col-lg-3">
            <div class="kv-sidebar-card" style="position:sticky;top:100px;">
                <div class="kv-sidebar-title">{{ __('Get This Product') }}</div>

                <div class="kv-price mb-1" style="font-size:26px;">{{ amount_with_currency_symbol($sale_price) }}</div>
                @if($discount > 0)
                <div style="font-size:12px;color:var(--kv-muted);margin-bottom:14px;">
                    {{ __('Was') }} <span style="text-decoration:line-through;">{{ amount_with_currency_symbol($regular_price) }}</span>
                    — <span style="color:var(--kv-red);font-weight:700;">{{ $discount }}% {{ __('off') }}</span>
                </div>
                @else
                <div style="margin-bottom:14px;"></div>
                @endif

                @auth('web')
                <form action="{{ theme_digital_product_add_to_cart_url() }}" method="POST" class="mb-2">
                    @csrf
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="kv-btn kv-btn-red w-100" style="justify-content:center;">
                        <i class="las la-shopping-bag"></i> {{ __('Add to Cart') }}
                    </button>
                </form>
                @else
                <a href="{{ theme_login_url() }}" class="kv-btn kv-btn-red w-100 mb-2 d-flex" style="justify-content:center;text-decoration:none;">
                    <i class="las la-sign-in-alt"></i> {{ __('Sign In to Purchase') }}
                </a>
                @endauth

                <button class="add-to-wishlist-btn kv-btn kv-btn-outline w-100 mb-3" data-product_id="{{ $product->id }}" style="justify-content:center;">
                    <i class="las la-heart"></i> {{ __('Wishlist') }}
                </button>

                <div style="display:flex;flex-direction:column;gap:8px;border-top:2px solid var(--kv-border);padding-top:14px;">
                    <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--kv-muted);">
                        <i class="las la-download" style="color:var(--kv-red);font-size:16px;"></i>
                        {{ __('Instant digital download') }}
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--kv-muted);">
                        <i class="las la-shield-alt" style="color:var(--kv-red);font-size:16px;"></i>
                        {{ __('Secure checkout') }}
                    </div>
                    @if($product->refund_policy)
                    <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--kv-muted);">
                        <i class="las la-undo-alt" style="color:var(--kv-red);font-size:16px;"></i>
                        {{ __('Refund policy available') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- Tabs --}}
    <div style="margin-top:48px;">
        <div style="display:flex;gap:0;border-bottom:2.5px solid var(--kv-border);margin-bottom:24px;">
            <button class="kv-dp-tab-btn active" data-target="kv-dp-tab-desc"
                    style="padding:12px 22px;background:none;border:0;border-bottom:3px solid var(--kv-red);font-size:13px;font-weight:800;color:var(--kv-dark);cursor:pointer;margin-bottom:-2.5px;transition:color .2s;">
                {{ __('Description') }}
            </button>
            @if($reviews->isNotEmpty())
            <button class="kv-dp-tab-btn" data-target="kv-dp-tab-reviews"
                    style="padding:12px 22px;background:none;border:0;border-bottom:3px solid transparent;font-size:13px;font-weight:600;color:var(--kv-muted);cursor:pointer;margin-bottom:-2.5px;transition:color .2s;">
                {{ __('Reviews') }} ({{ $reviews->count() }})
            </button>
            @endif
            @if($product->refund_policy)
            <button class="kv-dp-tab-btn" data-target="kv-dp-tab-refund"
                    style="padding:12px 22px;background:none;border:0;border-bottom:3px solid transparent;font-size:13px;font-weight:600;color:var(--kv-muted);cursor:pointer;margin-bottom:-2.5px;transition:color .2s;">
                {{ __('Refund Policy') }}
            </button>
            @endif
        </div>

        <div id="kv-dp-tab-desc" class="kv-dp-tab-panel">
            @if($product->description)
                <div style="font-size:14px;line-height:1.85;color:var(--kv-dark);">{!! $product->description !!}</div>
            @else
                <p style="color:var(--kv-muted);">{{ __('No description available.') }}</p>
            @endif
        </div>

        @if($reviews->isNotEmpty())
        <div id="kv-dp-tab-reviews" class="kv-dp-tab-panel" style="display:none;">
            <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:24px;">
                @foreach($reviews as $review)
                <div class="kv-sidebar-card">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                        <div style="width:42px;height:42px;border-radius:50%;background:var(--kv-red);display:flex;align-items:center;justify-content:center;font-size:16px;color:#fff;flex-shrink:0;font-weight:800;">
                            {{ strtoupper(substr($review->user?->name ?? 'U', 0, 1)) }}
                        </div>
                        <div style="flex:1;">
                            <div style="color:var(--kv-dark);font-size:14px;font-weight:800;">{{ $review->user?->name ?? __('Anonymous') }}</div>
                            <div>{!! render_star_rating_markup($review->rating) !!}</div>
                        </div>
                        <div style="font-size:11px;color:var(--kv-muted);">{{ $review->created_at->format('d M Y') }}</div>
                    </div>
                    <p style="font-size:14px;color:var(--kv-dark);margin:0;line-height:1.7;">{{ $review->review_text }}</p>
                </div>
                @endforeach
            </div>

            @auth('web')
            <div class="kv-sidebar-card">
                <div class="kv-sidebar-title">{{ __('Leave a Review') }}</div>
                <div class="d-flex gap-2 mb-3">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="las la-star kv-review-star" data-value="{{ $i }}"
                       style="font-size:30px;color:var(--kv-border);cursor:pointer;transition:color .15s;"></i>
                    @endfor
                </div>
                <textarea id="kv-review-text" rows="3" class="kv-price-input mb-3"
                          placeholder="{{ __('Share your experience…') }}"
                          style="height:auto;resize:vertical;"></textarea>
                <button id="kv-review-submit" class="kv-btn kv-btn-red" data-product="{{ $product->id }}">
                    <i class="las la-paper-plane"></i> {{ __('Submit Review') }}
                </button>
            </div>
            @endauth
        </div>
        @endif

        @if($product->refund_policy)
        <div id="kv-dp-tab-refund" class="kv-dp-tab-panel" style="display:none;">
            <div style="font-size:14px;line-height:1.85;color:var(--kv-dark);">
                {!! $product->refund_policy->description ?? '' !!}
            </div>
        </div>
        @endif
    </div>

    {{-- Related products --}}
    @if($related_products->isNotEmpty())
    <div style="margin-top:48px;">
        <div class="kv-sidebar-title" style="font-size:16px;margin-bottom:20px;">{{ __('Related Products') }}</div>
        <div class="row g-3">
            @foreach($related_products as $rp)
            @php
                $rp_data = get_digital_product_dynamic_price($rp);
                $rp_img  = (get_attachment_image_by_id($rp->image_id ?? null, 'grid'))['img_url'] ?? null;
            @endphp
            <div class="col-md-3 col-6">
                <div class="kv-card">
                    <div class="kv-card-img" style="aspect-ratio:3/4;">
                        @if($rp_img)
                            <a href="{{ dynamicRoute($rp->slug) }}">
                                <img src="{{ $rp_img }}" alt="{{ $rp->name }}" loading="lazy">
                            </a>
                        @else
                            <a href="{{ dynamicRoute($rp->slug) }}" class="kv-img-ph">
                                <i class="las la-file-download" style="font-size:52px;color:var(--kv-red);"></i>
                            </a>
                        @endif
                    </div>
                    <div class="kv-card-body">
                        <div class="kv-card-title">
                            <a href="{{ dynamicRoute($rp->slug) }}">{{ \Illuminate\Support\Str::words($rp->name, 7) }}</a>
                        </div>
                        <div class="kv-card-footer">
                            <div class="kv-price">{{ amount_with_currency_symbol($rp_data['sale_price']) }}</div>
                            <a href="{{ dynamicRoute($rp->slug) }}" class="kv-add-btn" aria-label="{{ __('View') }}">
                                <i class="las la-eye"></i>
                            </a>
                        </div>
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

    $(document).on('click', '.kv-dp-tab-btn', function () {
        var target = $(this).data('target');
        $('.kv-dp-tab-btn').css({ borderBottomColor: 'transparent', color: 'var(--kv-muted)', fontWeight: '600' }).removeClass('active');
        $(this).css({ borderBottomColor: 'var(--kv-red)', color: 'var(--kv-dark)', fontWeight: '800' }).addClass('active');
        $('.kv-dp-tab-panel').hide();
        $('#' + target).show();
    });

    var selectedRating = 0;
    $(document).on('mouseover', '.kv-review-star', function () {
        var val = $(this).data('value');
        $('.kv-review-star').each(function (i) {
            $(this).css('color', i < val ? 'var(--kv-yellow)' : 'var(--kv-border)');
        });
    }).on('mouseout', '.kv-review-star', function () {
        $('.kv-review-star').each(function (i) {
            $(this).css('color', i < selectedRating ? 'var(--kv-yellow)' : 'var(--kv-border)');
        });
    }).on('click', '.kv-review-star', function () {
        selectedRating = $(this).data('value');
        $('.kv-review-star').each(function (i) {
            $(this).css('color', i < selectedRating ? 'var(--kv-yellow)' : 'var(--kv-border)');
        });
    });

    $(document).on('click', '#kv-review-submit', function () {
        var btn = $(this), product_id = btn.data('product');
        if (!selectedRating) { toastr.warning('{{ __("Please select a rating") }}'); return; }
        var review_text = $('#kv-review-text').val().trim();
        if (!review_text) { toastr.warning('{{ __("Please write a review") }}'); return; }

        $.ajax({
            url: '{{ theme_digital_product_review_url() }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', product_id: product_id, rating: selectedRating, review_text: review_text },
            beforeSend: function () { btn.text('{{ __("Submitting...") }}'); },
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
