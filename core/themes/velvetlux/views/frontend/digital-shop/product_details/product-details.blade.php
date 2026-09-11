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
<div class="vl-page-header">
    <div class="container">
        <div class="vl-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <a href="{{ theme_digital_shop_url() }}">{{ __('Digital Shop') }}</a>
            <span>/</span>
            <span>{{ \Illuminate\Support\Str::words($product->name, 6) }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:40px;padding-bottom:72px;">
    <div class="row g-5">

        {{-- Cover Image --}}
        <div class="col-lg-3 col-md-4">
            <div style="position:sticky;top:100px;">
                @if($img_url)
                    <img src="{{ $img_url }}" alt="{{ $product->name }}"
                         style="width:100%;aspect-ratio:4/3;object-fit:contain;padding:12px;background:var(--vl-surface);border:1px solid var(--vl-border);">
                @else
                    <div style="width:100%;aspect-ratio:4/3;background:var(--vl-surface);border:1px solid var(--vl-border);display:flex;align-items:center;justify-content:center;">
                        <i class="mdi mdi-file-download-outline" style="font-size:64px;color:var(--vl-muted);opacity:.4;"></i>
                    </div>
                @endif
            </div>
        </div>

        {{-- Product Info --}}
        <div class="col-lg-6 col-md-8">
            @if($category)
                <div style="display:inline-block;background:var(--vl-plum);color:var(--vl-champagne);font-size:9px;font-weight:400;letter-spacing:3px;text-transform:uppercase;padding:4px 14px;margin-bottom:14px;font-family:'Inter',sans-serif;">{{ $category->name }}</div>
            @endif

            <h1 style="font-family:'Cormorant Garamond',serif;font-size:30px;font-weight:300;color:var(--vl-ivory);line-height:1.3;margin-bottom:12px;letter-spacing:.5px;">{!! $product->name !!}</h1>

            @if($author_name)
            <div style="font-size:12px;color:var(--vl-muted);margin-bottom:12px;letter-spacing:.5px;">
                <i class="mdi mdi-account-outline" style="color:var(--vl-champagne);"></i> {{ $author_name }}
            </div>
            @endif

            @if($avg_rating > 0)
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:16px;">
                @for($i=1;$i<=5;$i++)
                    <i class="mdi mdi-star{{ $i <= $avg_rating ? '' : '-outline' }}"
                       style="color:{{ $i <= $avg_rating ? 'var(--vl-champagne)' : 'var(--vl-border)' }};font-size:15px;"></i>
                @endfor
                <span style="font-size:12px;color:var(--vl-muted);">({{ $reviews->count() }})</span>
            </div>
            @endif

            {{-- Price --}}
            <div style="margin-bottom:24px;">
                <span style="font-size:28px;color:var(--vl-champagne);font-family:'Inter',sans-serif;">{{ amount_with_currency_symbol($sale_price) }}</span>
                @if($discount > 0)
                    <span style="font-size:16px;color:var(--vl-muted);text-decoration:line-through;margin-left:10px;font-family:'Inter',sans-serif;">{{ amount_with_currency_symbol($regular_price) }}</span>
                    <span style="font-size:10px;letter-spacing:2px;background:rgba(212,184,150,.1);color:var(--vl-champagne);padding:4px 12px;margin-left:8px;font-family:'Inter',sans-serif;">{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            {{-- Meta specs --}}
            @if($product->additionalFields?->file_format || $product->additionalFields?->number_of_pages || $product->additionalFields?->language || $product->additionalFields?->file_size)
            <div style="background:var(--vl-surface);border:1px solid var(--vl-border);padding:20px;margin-bottom:20px;">
                <div class="row g-3">
                    @if($product->additionalFields?->file_format)
                    <div class="col-6">
                        <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-muted);margin-bottom:4px;font-family:'Inter',sans-serif;">{{ __('Format') }}</div>
                        <div style="font-size:14px;color:var(--vl-ivory);">{{ strtoupper($product->additionalFields->file_format) }}</div>
                    </div>
                    @endif
                    @if($product->additionalFields?->number_of_pages)
                    <div class="col-6">
                        <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-muted);margin-bottom:4px;font-family:'Inter',sans-serif;">{{ __('Pages') }}</div>
                        <div style="font-size:14px;color:var(--vl-ivory);">{{ $product->additionalFields->number_of_pages }}</div>
                    </div>
                    @endif
                    @if($product->additionalFields?->language)
                    <div class="col-6">
                        <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-muted);margin-bottom:4px;font-family:'Inter',sans-serif;">{{ __('Language') }}</div>
                        <div style="font-size:14px;color:var(--vl-ivory);">{{ $product->additionalFields->getLanguage?->name }}</div>
                    </div>
                    @endif
                    @if($product->additionalFields?->file_size)
                    <div class="col-6">
                        <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-muted);margin-bottom:4px;font-family:'Inter',sans-serif;">{{ __('File Size') }}</div>
                        <div style="font-size:14px;color:var(--vl-ivory);">{{ $product->additionalFields->file_size }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Short description --}}
            @if($product->short_description)
            <div style="font-size:14px;color:var(--vl-muted);line-height:1.8;margin-bottom:20px;">
                {!! $product->short_description !!}
            </div>
            @endif

            {{-- Tags --}}
            @if($product->tag && $product->tag->isNotEmpty())
            <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin-bottom:20px;">
                <span style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-muted);font-family:'Inter',sans-serif;">{{ __('Tags:') }}</span>
                @foreach($product->tag as $tag)
                    <span style="padding:4px 12px;background:var(--vl-surface);border:1px solid var(--vl-border);color:var(--vl-muted);font-size:10px;letter-spacing:1px;text-transform:uppercase;font-family:'Inter',sans-serif;">{{ $tag->tag_name }}</span>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Purchase Sidebar --}}
        <div class="col-lg-3">
            <div style="background:var(--vl-card);border:1px solid var(--vl-border);position:sticky;top:100px;">
                <div style="padding:16px 20px;background:var(--vl-plum);font-size:9px;letter-spacing:3px;text-transform:uppercase;color:var(--vl-champagne-l);font-family:'Inter',sans-serif;">{{ __('Get This Product') }}</div>
                <div style="padding:24px;">
                    <div style="font-size:26px;color:var(--vl-champagne);font-family:'Inter',sans-serif;margin-bottom:20px;">{{ amount_with_currency_symbol($sale_price) }}</div>

                    @auth('web')
                    <form action="{{ theme_digital_product_add_to_cart_url() }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="vl-btn vl-btn-primary" style="width:100%;justify-content:center;margin-bottom:10px;">
                            <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                        </button>
                    </form>
                    @else
                    <a href="{{ theme_login_url() }}" class="vl-btn vl-btn-primary" style="width:100%;justify-content:center;margin-bottom:10px;">
                        <i class="mdi mdi-login-variant"></i> {{ __('Sign In to Purchase') }}
                    </a>
                    @endauth

                    <button class="add-to-wishlist-btn vl-btn vl-btn-outline" data-product_id="{{ $product->id }}"
                            style="width:100%;justify-content:center;margin-bottom:20px;">
                        <i class="mdi mdi-heart-outline"></i> {{ __('Wishlist') }}
                    </button>

                    <div style="border-top:1px solid var(--vl-border);padding-top:16px;display:flex;flex-direction:column;gap:10px;">
                        <div style="font-size:12px;color:var(--vl-muted);display:flex;align-items:center;gap:8px;letter-spacing:.5px;">
                            <i class="mdi mdi-cloud-download-outline" style="color:var(--vl-champagne);font-size:18px;"></i>
                            {{ __('Instant digital download') }}
                        </div>
                        <div style="font-size:12px;color:var(--vl-muted);display:flex;align-items:center;gap:8px;letter-spacing:.5px;">
                            <i class="mdi mdi-shield-check-outline" style="color:var(--vl-champagne);font-size:18px;"></i>
                            {{ __('Secure checkout') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Tabs --}}
    <div style="margin-top:56px;">
        <div style="display:flex;border-bottom:1px solid var(--vl-border);margin-bottom:28px;">
            <button class="vl-dp-tab-btn vl-tab-nav-btn active" data-target="vl-dp-desc"
                    style="padding:14px 24px;border:none;background:transparent;font-size:10px;font-weight:400;letter-spacing:3px;text-transform:uppercase;color:var(--vl-champagne);border-bottom:2px solid var(--vl-champagne);margin-bottom:-1px;cursor:pointer;font-family:'Inter',sans-serif;">
                {{ __('Description') }}
            </button>
            @if($reviews->isNotEmpty())
            <button class="vl-dp-tab-btn vl-tab-nav-btn" data-target="vl-dp-reviews"
                    style="padding:14px 24px;border:none;background:transparent;font-size:10px;font-weight:400;letter-spacing:3px;text-transform:uppercase;color:var(--vl-muted);border-bottom:2px solid transparent;margin-bottom:-1px;cursor:pointer;font-family:'Inter',sans-serif;">
                {{ __('Reviews') }} ({{ $reviews->count() }})
            </button>
            @endif
            @if($product->refund_policy)
            <button class="vl-dp-tab-btn vl-tab-nav-btn" data-target="vl-dp-refund"
                    style="padding:14px 24px;border:none;background:transparent;font-size:10px;font-weight:400;letter-spacing:3px;text-transform:uppercase;color:var(--vl-muted);border-bottom:2px solid transparent;margin-bottom:-1px;cursor:pointer;font-family:'Inter',sans-serif;">
                {{ __('Refund Policy') }}
            </button>
            @endif
        </div>

        <div id="vl-dp-desc" class="vl-dp-tab-panel">
            <div style="font-size:14px;line-height:1.85;color:var(--vl-muted);">{!! $product->description ?? __('No description available.') !!}</div>
        </div>

        @if($reviews->isNotEmpty())
        <div id="vl-dp-reviews" class="vl-dp-tab-panel" style="display:none;">
            <div style="display:flex;flex-direction:column;gap:20px;margin-bottom:32px;">
                @foreach($reviews as $review)
                <div style="display:flex;gap:16px;padding-bottom:20px;border-bottom:1px solid var(--vl-border);">
                    <div style="width:44px;height:44px;background:var(--vl-plum);color:var(--vl-champagne);display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:300;flex-shrink:0;font-family:'Cormorant Garamond',serif;">
                        {{ strtoupper(substr($review->user?->name ?? 'A', 0, 1)) }}
                    </div>
                    <div style="flex:1;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                            <span style="font-size:14px;color:var(--vl-ivory);">{{ $review->user?->name ?? __('Anonymous') }}</span>
                            <span style="font-size:11px;color:var(--vl-muted);">{{ $review->created_at->format('d M Y') }}</span>
                        </div>
                        <div style="margin-bottom:8px;">
                            @for($i=1;$i<=5;$i++)
                                <i class="mdi mdi-star{{ $i <= $review->rating ? '' : '-outline' }}"
                                   style="color:{{ $i <= $review->rating ? 'var(--vl-champagne)' : 'var(--vl-border)' }};font-size:13px;"></i>
                            @endfor
                        </div>
                        <p style="font-size:13px;color:var(--vl-muted);margin:0;line-height:1.7;">{{ $review->review_text }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            @auth('web')
            <div style="background:var(--vl-surface);border:1px solid var(--vl-border);padding:24px;">
                <div style="font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:16px;font-family:'Inter',sans-serif;">{{ __('Leave a Review') }}</div>
                <div style="display:flex;gap:8px;margin-bottom:14px;">
                    @for($i=1;$i<=5;$i++)
                    <i class="mdi mdi-star-outline vl-dp-review-star" data-value="{{ $i }}"
                       style="font-size:28px;color:var(--vl-border);cursor:pointer;transition:color .15s;"></i>
                    @endfor
                </div>
                <textarea id="vl-review-text" rows="4"
                          style="width:100%;padding:12px 16px;background:var(--vl-card);border:1px solid var(--vl-border);color:var(--vl-ivory);font-size:14px;font-family:inherit;outline:none;resize:vertical;margin-bottom:14px;"
                          placeholder="{{ __('Share your experience…') }}"></textarea>
                <button id="vl-review-submit" data-product="{{ $product->id }}" class="vl-btn vl-btn-primary">
                    <i class="mdi mdi-send"></i> {{ __('Submit Review') }}
                </button>
            </div>
            @endauth
        </div>
        @endif

        @if($product->refund_policy)
        <div id="vl-dp-refund" class="vl-dp-tab-panel" style="display:none;">
            <div style="font-size:14px;line-height:1.85;color:var(--vl-muted);">{!! $product->refund_policy->description ?? '' !!}</div>
        </div>
        @endif
    </div>

    {{-- Related Products --}}
    @if($related_products->isNotEmpty())
    <div style="margin-top:56px;">
        <div style="font-size:10px;letter-spacing:5px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:20px;font-family:'Inter',sans-serif;">{{ __('Related Products') }}</div>
        <div class="row g-3">
            @foreach($related_products as $rp)
            @php
                $rp_data = get_digital_product_dynamic_price($rp);
                $rp_img  = (get_attachment_image_by_id($rp->image_id ?? null, 'grid'))['img_url'] ?? null;
            @endphp
            <div class="col-md-3 col-6">
                <div class="vl-card">
                    <div class="vl-card-img" style="aspect-ratio:4/3;">
                        @if($rp_img)
                            <a href="{{ dynamicRoute($rp->slug) }}">
                                <img src="{{ $rp_img }}" alt="{{ $rp->name }}" loading="lazy">
                            </a>
                        @else
                            <a href="{{ dynamicRoute($rp->slug) }}" style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;text-decoration:none;">
                                <i class="mdi mdi-file-download-outline" style="font-size:40px;color:var(--vl-muted);opacity:.4;"></i>
                            </a>
                        @endif
                        <div class="vl-card-overlay">
                            <a href="{{ dynamicRoute($rp->slug) }}" class="vl-card-act-btn">{{ __('View') }}</a>
                        </div>
                    </div>
                    <div style="padding:14px;">
                        <a href="{{ dynamicRoute($rp->slug) }}"
                           style="font-size:13px;font-weight:300;color:var(--vl-ivory);text-decoration:none;font-style:italic;display:block;margin-bottom:6px;">
                            {{ \Illuminate\Support\Str::words($rp->name, 7) }}
                        </a>
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
    $(document).on('click', '.vl-dp-tab-btn', function () {
        var target = $(this).data('target');
        $('.vl-dp-tab-btn').css({'color':'var(--vl-muted)','border-bottom-color':'transparent'});
        $(this).css({'color':'var(--vl-champagne)','border-bottom-color':'var(--vl-champagne)'});
        $('.vl-dp-tab-panel').hide();
        $('#' + target).show();
    });

    var selectedRating = 0;
    $(document).on('click', '.vl-dp-review-star', function () {
        selectedRating = $(this).data('value');
        $('.vl-dp-review-star').each(function (i) {
            $(this).attr('class', i < selectedRating ? 'mdi mdi-star vl-dp-review-star' : 'mdi mdi-star-outline vl-dp-review-star')
                   .css('color', i < selectedRating ? 'var(--vl-champagne)' : 'var(--vl-border)');
        });
    });

    $(document).on('click', '#vl-review-submit', function () {
        var btn = $(this), product_id = btn.data('product');
        if (!selectedRating) { toastr.warning('{{ __("Please select a rating") }}'); return; }
        var review_text = $('#vl-review-text').val().trim();
        if (!review_text) { toastr.warning('{{ __("Please enter a review") }}'); return; }

        $.ajax({
            url: '{{ theme_digital_product_review_url() }}', type: 'POST',
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
