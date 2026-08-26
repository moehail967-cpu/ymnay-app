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
<div style="background:var(--tr-cream);border-bottom:1px solid var(--tr-border);padding:12px 0;">
    <div class="container">
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
            <a href="{{ theme_home_url() }}" style="color:var(--tr-stone);text-decoration:none;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="color:var(--tr-border);font-size:14px;"></i>
            <a href="{{ theme_digital_shop_url() }}" style="color:var(--tr-stone);text-decoration:none;">{{ __('Digital Shop') }}</a>
            <i class="mdi mdi-chevron-right" style="color:var(--tr-border);font-size:14px;"></i>
            <span style="color:var(--tr-bark);font-weight:600;">{{ \Illuminate\Support\Str::words($product->name, 6) }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:36px;padding-bottom:60px;">
    <div class="row g-4">

        {{-- Cover Image --}}
        <div class="col-lg-3 col-md-4">
            <div style="position:sticky;top:100px;">
                @if($img_url)
                    <img src="{{ $img_url }}" alt="{{ $product->name }}"
                         style="width:100%;border-radius:var(--tr-radius);border:1px solid var(--tr-border);object-fit:contain;aspect-ratio:4/3;box-shadow:var(--tr-shadow);background:var(--tr-cream);padding:12px;">
                @else
                    <div style="width:100%;aspect-ratio:4/3;border-radius:var(--tr-radius);background:var(--tr-cream);border:1px solid var(--tr-border);display:flex;align-items:center;justify-content:center;">
                        <i class="mdi mdi-file-download-outline" style="font-size:64px;color:var(--tr-stone);opacity:.4;"></i>
                    </div>
                @endif
            </div>
        </div>

        {{-- Product Info --}}
        <div class="col-lg-6 col-md-8">
            @if($category)
                <div style="display:inline-block;background:rgba(92,122,62,.1);color:var(--tr-olive);font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px;">{{ $category->name }}</div>
            @endif

            <h1 style="font-size:26px;font-weight:800;color:var(--tr-bark);line-height:1.3;margin-bottom:10px;">{!! $product->name !!}</h1>

            @if($author_name)
            <div style="font-size:13px;color:var(--tr-stone);margin-bottom:10px;">
                <i class="mdi mdi-account-outline" style="color:var(--tr-olive);"></i> {{ $author_name }}
            </div>
            @endif

            @if($avg_rating > 0)
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                @for($i=1;$i<=5;$i++)
                    <i class="mdi mdi-star{{ $i <= $avg_rating ? '' : '-outline' }}"
                       style="color:{{ $i <= $avg_rating ? 'var(--tr-sand)' : 'var(--tr-border)' }};font-size:16px;"></i>
                @endfor
                <span style="font-size:12px;color:var(--tr-stone);">({{ $reviews->count() }})</span>
            </div>
            @endif

            {{-- Price --}}
            <div style="display:flex;align-items:baseline;gap:12px;margin-bottom:20px;">
                <span style="font-size:28px;font-weight:800;color:var(--tr-olive);">{{ amount_with_currency_symbol($sale_price) }}</span>
                @if($discount > 0)
                    <span style="font-size:15px;color:var(--tr-stone);text-decoration:line-through;">{{ amount_with_currency_symbol($regular_price) }}</span>
                    <span style="font-size:12px;font-weight:800;color:var(--tr-terra);background:rgba(194,91,42,.1);padding:3px 10px;border-radius:20px;">{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            {{-- Meta specs --}}
            @if($product->additionalFields?->file_format || $product->additionalFields?->number_of_pages || $product->additionalFields?->language || $product->additionalFields?->file_size || $product->downloads_count)
            <div class="row g-2 mb-4" style="background:var(--tr-cream);border:1px solid var(--tr-border);border-radius:var(--tr-radius);padding:16px;margin:0;">
                @if($product->additionalFields?->file_format)
                <div class="col-6">
                    <div style="font-size:10px;color:var(--tr-stone);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('Format') }}</div>
                    <div style="font-weight:700;color:var(--tr-bark);font-size:14px;">{{ strtoupper($product->additionalFields->file_format) }}</div>
                </div>
                @endif
                @if($product->additionalFields?->number_of_pages)
                <div class="col-6">
                    <div style="font-size:10px;color:var(--tr-stone);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('Pages') }}</div>
                    <div style="font-weight:700;color:var(--tr-bark);font-size:14px;">{{ $product->additionalFields->number_of_pages }}</div>
                </div>
                @endif
                @if($product->additionalFields?->language)
                <div class="col-6">
                    <div style="font-size:10px;color:var(--tr-stone);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('Language') }}</div>
                    <div style="font-weight:700;color:var(--tr-bark);font-size:14px;">{{ $product->additionalFields->getLanguage?->name }}</div>
                </div>
                @endif
                @if($product->additionalFields?->file_size)
                <div class="col-6">
                    <div style="font-size:10px;color:var(--tr-stone);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('File Size') }}</div>
                    <div style="font-weight:700;color:var(--tr-bark);font-size:14px;">{{ $product->additionalFields->file_size }}</div>
                </div>
                @endif
                @if($product->downloads_count ?? 0)
                <div class="col-6">
                    <div style="font-size:10px;color:var(--tr-stone);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('Downloads') }}</div>
                    <div style="font-weight:700;color:var(--tr-bark);font-size:14px;">{{ $product->downloads_count }}</div>
                </div>
                @endif
            </div>
            @endif

            {{-- Short description --}}
            @if($product->short_description)
            <div style="font-size:14px;color:var(--tr-stone);line-height:1.7;margin-bottom:20px;">
                {!! $product->short_description !!}
            </div>
            @endif

            {{-- Tags --}}
            @if($product->tag && $product->tag->isNotEmpty())
            <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;">
                <span style="font-size:12px;color:var(--tr-stone);font-weight:600;">{{ __('Tags:') }}</span>
                @foreach($product->tag as $tag)
                    <span style="background:var(--tr-cream);color:var(--tr-stone);border:1px solid var(--tr-border);padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">{{ $tag->tag_name }}</span>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Purchase Sidebar --}}
        <div class="col-lg-3">
            <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;position:sticky;top:100px;box-shadow:var(--tr-shadow);">
                <div style="padding:14px 18px;background:var(--tr-bark);font-size:14px;font-weight:800;color:var(--tr-sand);">{{ __('Get This Product') }}</div>
                <div style="padding:20px;">
                    <div style="font-size:28px;font-weight:800;color:var(--tr-olive);margin-bottom:4px;">
                        {{ amount_with_currency_symbol($sale_price) }}
                    </div>
                    @if($discount > 0)
                    <div style="font-size:13px;color:var(--tr-stone);margin-bottom:16px;">
                        {{ __('Was') }} <span style="text-decoration:line-through;">{{ amount_with_currency_symbol($regular_price) }}</span>
                        — <span style="color:var(--tr-terra);font-weight:700;">{{ $discount }}% {{ __('off') }}</span>
                    </div>
                    @endif

                    @auth('web')
                    <form action="{{ theme_digital_product_add_to_cart_url() }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit"
                                style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:13px;background:var(--tr-olive);color:#fff;border:none;border-radius:var(--tr-radius);font-size:15px;font-weight:700;cursor:pointer;margin-bottom:10px;">
                            <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                        </button>
                    </form>
                    @else
                    <a href="{{ theme_login_url() }}"
                       style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:13px;background:var(--tr-olive);color:#fff;border-radius:var(--tr-radius);font-size:15px;font-weight:700;text-decoration:none;margin-bottom:10px;">
                        <i class="mdi mdi-login-variant"></i> {{ __('Sign In to Purchase') }}
                    </a>
                    @endauth

                    <button class="add-to-wishlist-btn" data-product_id="{{ $product->id }}"
                            style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:10px;background:transparent;border:1.5px solid var(--tr-olive);color:var(--tr-olive);border-radius:var(--tr-radius);font-size:13px;font-weight:700;cursor:pointer;margin-bottom:16px;">
                        <i class="mdi mdi-heart-outline"></i> {{ __('Wishlist') }}
                    </button>

                    <div style="border-top:1px solid var(--tr-border);padding-top:14px;display:flex;flex-direction:column;gap:10px;">
                        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--tr-stone);">
                            <i class="mdi mdi-cloud-download-outline" style="color:var(--tr-olive);font-size:18px;"></i>
                            {{ __('Instant digital download') }}
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--tr-stone);">
                            <i class="mdi mdi-shield-check-outline" style="color:var(--tr-olive);font-size:18px;"></i>
                            {{ __('Secure checkout') }}
                        </div>
                        @if($product->refund_policy)
                        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--tr-stone);">
                            <i class="mdi mdi-refresh" style="color:var(--tr-terra);font-size:18px;"></i>
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
        <div style="display:flex;gap:0;border-bottom:2px solid var(--tr-border);margin-bottom:28px;">
            <button class="tr-dp-tab-btn active" data-target="tr-dp-tab-desc"
                    style="padding:12px 20px;border:none;background:transparent;font-size:14px;font-weight:700;color:var(--tr-olive);border-bottom:2px solid var(--tr-olive);margin-bottom:-2px;cursor:pointer;">
                {{ __('Description') }}
            </button>
            @if($reviews->isNotEmpty())
            <button class="tr-dp-tab-btn" data-target="tr-dp-tab-reviews"
                    style="padding:12px 20px;border:none;background:transparent;font-size:14px;font-weight:700;color:var(--tr-stone);border-bottom:2px solid transparent;margin-bottom:-2px;cursor:pointer;">
                {{ __('Reviews') }} ({{ $reviews->count() }})
            </button>
            @endif
            @if($product->refund_policy)
            <button class="tr-dp-tab-btn" data-target="tr-dp-tab-refund"
                    style="padding:12px 20px;border:none;background:transparent;font-size:14px;font-weight:700;color:var(--tr-stone);border-bottom:2px solid transparent;margin-bottom:-2px;cursor:pointer;">
                {{ __('Refund Policy') }}
            </button>
            @endif
        </div>

        <div id="tr-dp-tab-desc" class="tr-dp-tab-panel">
            @if($product->description)
                <div style="font-size:14px;line-height:1.85;color:#444;">{!! $product->description !!}</div>
            @else
                <p style="color:var(--tr-stone);">{{ __('No description available.') }}</p>
            @endif
        </div>

        @if($reviews->isNotEmpty())
        <div id="tr-dp-tab-reviews" class="tr-dp-tab-panel" style="display:none;">
            <div style="display:flex;flex-direction:column;gap:16px;margin-bottom:28px;">
                @foreach($reviews as $review)
                <div style="display:flex;gap:14px;padding-bottom:16px;border-bottom:1px solid var(--tr-border);">
                    <div style="width:42px;height:42px;border-radius:50%;background:var(--tr-olive);display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;font-weight:800;flex-shrink:0;">
                        {{ strtoupper(substr($review->user?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div style="flex:1;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                            <span style="font-size:14px;font-weight:700;color:var(--tr-bark);">{{ $review->user?->name ?? __('Anonymous') }}</span>
                            <span style="font-size:11px;color:var(--tr-stone);">{{ $review->created_at->format('d M Y') }}</span>
                        </div>
                        <div style="margin-bottom:6px;">
                            @for($i=1;$i<=5;$i++)
                                <i class="mdi mdi-star{{ $i <= $review->rating ? '' : '-outline' }}"
                                   style="color:{{ $i <= $review->rating ? 'var(--tr-sand)' : 'var(--tr-border)' }};font-size:13px;"></i>
                            @endfor
                        </div>
                        <p style="font-size:13px;color:#555;margin:0;line-height:1.6;">{{ $review->review_text }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            @auth('web')
            <div style="background:var(--tr-cream);border:1px solid var(--tr-border);border-radius:var(--tr-radius);padding:20px;">
                <div style="font-size:14px;font-weight:800;color:var(--tr-bark);margin-bottom:14px;">{{ __('Leave a Review') }}</div>
                <div style="display:flex;gap:6px;margin-bottom:12px;">
                    @for($i=1;$i<=5;$i++)
                    <i class="mdi mdi-star-outline tr-dp-review-star" data-value="{{ $i }}"
                       style="font-size:28px;color:var(--tr-border);cursor:pointer;transition:color .15s;"></i>
                    @endfor
                </div>
                <textarea id="review-text" rows="3"
                          style="width:100%;padding:10px 14px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-size:14px;font-family:inherit;outline:none;resize:vertical;margin-bottom:12px;"
                          placeholder="{{ __('Write your review…') }}"
                          onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'"></textarea>
                <button id="review-submit-btn" data-product="{{ $product->id }}"
                        style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;background:var(--tr-olive);color:#fff;border:none;border-radius:var(--tr-radius);font-size:13px;font-weight:700;cursor:pointer;">
                    <i class="mdi mdi-send"></i> {{ __('Submit Review') }}
                </button>
            </div>
            @endauth
        </div>

        @endif

        @if($product->refund_policy)
        <div id="tr-dp-tab-refund" class="tr-dp-tab-panel" style="display:none;">
            <div style="font-size:14px;line-height:1.85;color:#444;">
                {!! $product->refund_policy->description ?? '' !!}
            </div>
        </div>
        @endif
    </div>

    {{-- Related Products --}}
    @if($related_products->isNotEmpty())
    <div style="margin-top:56px;">
        <h2 style="font-size:20px;font-weight:800;color:var(--tr-bark);margin-bottom:20px;">{{ __('Related Products') }}</h2>
        <div class="row g-3">
            @foreach($related_products as $rp)
            @php
                $rp_data = get_digital_product_dynamic_price($rp);
                $rp_img  = (get_attachment_image_by_id($rp->image_id ?? null, 'grid'))['img_url'] ?? null;
            @endphp
            <div class="col-md-4 col-6">
                <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;box-shadow:var(--tr-shadow);">
                    <div style="aspect-ratio:4/3;background:var(--tr-cream);overflow:hidden;">
                        @if($rp_img)
                            <a href="{{ dynamicRoute($rp->slug) }}">
                                <img src="{{ $rp_img }}" alt="{{ $rp->name }}" loading="lazy"
                                     style="width:100%;height:100%;object-fit:contain;padding:8px;">
                            </a>
                        @else
                            <a href="{{ dynamicRoute($rp->slug) }}"
                               style="display:flex;align-items:center;justify-content:center;height:100%;text-decoration:none;">
                                <i class="mdi mdi-file-download-outline" style="font-size:48px;color:var(--tr-stone);opacity:.4;"></i>
                            </a>
                        @endif
                    </div>
                    <div style="padding:14px;">
                        @if($rp->category)
                            <div style="font-size:10px;font-weight:700;color:var(--tr-olive);text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;">{{ $rp->category->name }}</div>
                        @endif
                        <a href="{{ dynamicRoute($rp->slug) }}"
                           style="font-size:13px;font-weight:700;color:var(--tr-bark);text-decoration:none;display:block;margin-bottom:8px;line-height:1.4;">
                            {{ \Illuminate\Support\Str::words($rp->name, 8) }}
                        </a>
                        <div style="font-size:15px;font-weight:800;color:var(--tr-olive);margin-bottom:10px;">{{ amount_with_currency_symbol($rp_data['sale_price']) }}</div>
                        <a href="{{ dynamicRoute($rp->slug) }}"
                           style="display:flex;align-items:center;justify-content:center;gap:6px;padding:7px;border:1.5px solid var(--tr-olive);color:var(--tr-olive);border-radius:var(--tr-radius);font-size:12px;font-weight:700;text-decoration:none;">
                            <i class="mdi mdi-eye-outline"></i> {{ __('View') }}
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

    $(document).on('click', '.tr-dp-tab-btn', function () {
        var target = $(this).data('target');
        $('.tr-dp-tab-btn').css({'color':'var(--tr-stone)','border-bottom-color':'transparent'});
        $(this).css({'color':'var(--tr-olive)','border-bottom-color':'var(--tr-olive)'});
        $('.tr-dp-tab-panel').hide();
        $('#' + target).show();
    });

    var selectedRating = 0;
    $(document).on('mouseover', '.tr-dp-review-star', function () {
        var val = $(this).data('value');
        $('.tr-dp-review-star').each(function (i) {
            $(this).attr('class', i < val ? 'mdi mdi-star tr-dp-review-star' : 'mdi mdi-star-outline tr-dp-review-star')
                   .css('color', i < val ? 'var(--tr-sand)' : 'var(--tr-border)');
        });
    }).on('mouseout', '.tr-dp-review-star', function () {
        $('.tr-dp-review-star').each(function (i) {
            $(this).attr('class', i < selectedRating ? 'mdi mdi-star tr-dp-review-star' : 'mdi mdi-star-outline tr-dp-review-star')
                   .css('color', i < selectedRating ? 'var(--tr-sand)' : 'var(--tr-border)');
        });
    }).on('click', '.tr-dp-review-star', function () {
        selectedRating = $(this).data('value');
        $('.tr-dp-review-star').each(function (i) {
            $(this).attr('class', i < selectedRating ? 'mdi mdi-star tr-dp-review-star' : 'mdi mdi-star-outline tr-dp-review-star')
                   .css('color', i < selectedRating ? 'var(--tr-sand)' : 'var(--tr-border)');
        });
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
            beforeSend: function () { btn.html('<i class="mdi mdi-loading mdi-spin"></i> {{ __("Submitting…") }}'); },
            success: function (data) {
                if (data.type === 'success') { toastr.success(data.msg); setTimeout(() => location.reload(), 1000); }
                else { toastr.error(data.msg); btn.html('<i class="mdi mdi-send"></i> {{ __("Submit Review") }}'); }
            },
            error: function () { btn.html('<i class="mdi mdi-send"></i> {{ __("Submit Review") }}'); }
        });
    });

})(jQuery);
</script>
@endsection
