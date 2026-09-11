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

<div style="background:var(--gc-warm);border-bottom:1px solid var(--gc-border);padding:28px 0 22px;">
    <div class="container">
        <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:var(--gc-muted);font-style:italic;">
            <a href="{{ theme_home_url() }}" style="color:var(--gc-rose);text-decoration:none;">{{ __('Home') }}</a>
            <span>—</span>
            <a href="{{ theme_digital_shop_url() }}" style="color:var(--gc-rose);text-decoration:none;">{{ __('Digital Shop') }}</a>
            <span>—</span>
            <span>{{ \Illuminate\Support\Str::words($product->name, 6) }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:40px;padding-bottom:80px;">
    <div class="row g-4">

        {{-- Cover image --}}
        <div class="col-lg-3 col-md-4">
            <div style="position:sticky;top:100px;">
                @if($img_url)
                    <img src="{{ $img_url }}" alt="{{ $product->name }}"
                         style="width:100%;border-radius:var(--gc-radius);border:1px solid var(--gc-border);object-fit:cover;aspect-ratio:3/4;box-shadow:var(--gc-shadow);">
                @else
                    <div style="width:100%;aspect-ratio:3/4;border-radius:var(--gc-radius);background:var(--gc-warm);border:1px solid var(--gc-border);display:flex;align-items:center;justify-content:center;font-size:64px;color:var(--gc-rose);">
                        <i class="las la-file-download"></i>
                    </div>
                @endif
            </div>
        </div>

        {{-- Product info --}}
        <div class="col-lg-6 col-md-8">
            @if($category)
                <div style="display:inline-block;background:var(--gc-warm);color:var(--gc-rose);font-size:9px;font-weight:700;padding:4px 12px;border-radius:3px;margin-bottom:10px;text-transform:uppercase;letter-spacing:1.5px;">{{ $category->name }}</div>
            @endif

            <h1 style="font-size:24px;font-weight:400;color:var(--gc-dark);margin-bottom:10px;line-height:1.3;font-family:Georgia,serif;font-style:italic;">{!! $product->name !!}</h1>

            @if($author_name)
            <div style="font-size:13px;color:var(--gc-muted);margin-bottom:10px;font-style:italic;">
                <i class="las la-user" style="color:var(--gc-rose);"></i> {{ $author_name }}
            </div>
            @endif

            @if($avg_rating > 0)
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:14px;">
                {!! render_star_rating_markup($avg_rating) !!}
                <span style="font-size:12px;color:var(--gc-muted);font-style:italic;">({{ $reviews->count() }})</span>
            </div>
            @endif

            {{-- Price --}}
            <div style="display:flex;align-items:baseline;gap:12px;margin-bottom:20px;">
                <span style="font-size:28px;color:var(--gc-rose);font-family:Georgia,serif;font-style:italic;">{{ amount_with_currency_symbol($sale_price) }}</span>
                @if($discount > 0)
                    <span style="font-size:14px;color:var(--gc-muted);text-decoration:line-through;font-style:italic;">{{ amount_with_currency_symbol($regular_price) }}</span>
                    <span style="font-size:9px;font-weight:700;color:#fff;background:var(--gc-rose);padding:3px 10px;border-radius:3px;text-transform:uppercase;letter-spacing:1px;">{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            {{-- Meta specs --}}
            @if($product->additionalFields?->file_format || $product->additionalFields?->number_of_pages || $product->additionalFields?->language || $product->additionalFields?->file_size || ($product->downloads_count ?? 0))
            <div style="background:var(--gc-warm);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:16px;margin-bottom:20px;">
                <div class="row g-3">
                    @if($product->additionalFields?->file_format)
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--gc-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:2px;">{{ __('Format') }}</div>
                        <div style="color:var(--gc-dark);font-size:14px;font-family:Georgia,serif;font-style:italic;">{{ strtoupper($product->additionalFields->file_format) }}</div>
                    </div>
                    @endif
                    @if($product->additionalFields?->number_of_pages)
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--gc-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:2px;">{{ __('Pages') }}</div>
                        <div style="color:var(--gc-dark);font-size:14px;font-family:Georgia,serif;font-style:italic;">{{ $product->additionalFields->number_of_pages }}</div>
                    </div>
                    @endif
                    @if($product->additionalFields?->language)
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--gc-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:2px;">{{ __('Language') }}</div>
                        <div style="color:var(--gc-dark);font-size:14px;font-family:Georgia,serif;font-style:italic;">{{ $product->additionalFields->getLanguage?->name }}</div>
                    </div>
                    @endif
                    @if($product->additionalFields?->file_size)
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--gc-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:2px;">{{ __('File Size') }}</div>
                        <div style="color:var(--gc-dark);font-size:14px;font-family:Georgia,serif;font-style:italic;">{{ $product->additionalFields->file_size }}</div>
                    </div>
                    @endif
                    @if($product->downloads_count ?? 0)
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--gc-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:2px;">{{ __('Downloads') }}</div>
                        <div style="color:var(--gc-dark);font-size:14px;font-family:Georgia,serif;font-style:italic;">{{ $product->downloads_count }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if($product->short_description)
            <div style="font-size:14px;color:var(--gc-muted);line-height:1.8;margin-bottom:16px;font-family:Georgia,serif;font-style:italic;">
                {!! $product->short_description !!}
            </div>
            @endif

            @if($product->tag && $product->tag->isNotEmpty())
            <div style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;">
                <span style="font-size:11px;color:var(--gc-muted);font-style:italic;">{{ __('Tags:') }}</span>
                @foreach($product->tag as $tag)
                    <span style="background:var(--gc-warm);color:var(--gc-rose);padding:3px 10px;border-radius:3px;font-size:11px;font-style:italic;">{{ $tag->tag_name }}</span>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Purchase sidebar --}}
        <div class="col-lg-3">
            <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);overflow:hidden;box-shadow:var(--gc-shadow);position:sticky;top:100px;">
                <div style="background:var(--gc-warm);padding:16px 20px;border-bottom:1px solid var(--gc-border);">
                    <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);">{{ __('Get This Product') }}</div>
                </div>
                <div style="padding:20px;">
                    <div style="font-size:28px;color:var(--gc-rose);margin-bottom:4px;font-family:Georgia,serif;font-style:italic;">
                        {{ amount_with_currency_symbol($sale_price) }}
                    </div>
                    @if($discount > 0)
                    <div style="font-size:12px;color:var(--gc-muted);margin-bottom:16px;font-style:italic;">
                        {{ __('Was') }} <span style="text-decoration:line-through;">{{ amount_with_currency_symbol($regular_price) }}</span>
                        — <span style="color:var(--gc-rose);">{{ $discount }}% {{ __('off') }}</span>
                    </div>
                    @else
                    <div style="margin-bottom:16px;"></div>
                    @endif

                    @auth('web')
                    <form action="{{ theme_digital_product_add_to_cart_url() }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="gc-btn gc-btn-primary" style="width:100%;justify-content:center;margin-bottom:10px;">
                            <i class="las la-shopping-bag"></i> {{ __('Add to Cart') }}
                        </button>
                    </form>
                    @else
                    <a href="{{ theme_login_url() }}" class="gc-btn gc-btn-primary" style="width:100%;justify-content:center;margin-bottom:10px;display:flex;text-decoration:none;">
                        <i class="las la-sign-in-alt"></i> {{ __('Sign In to Purchase') }}
                    </a>
                    @endauth

                    <button class="add-to-wishlist-btn gc-btn gc-btn-ghost" data-product_id="{{ $product->id }}" style="width:100%;justify-content:center;margin-bottom:16px;">
                        <i class="las la-heart"></i> {{ __('Wishlist') }}
                    </button>

                    <div style="border-top:1px solid var(--gc-border);padding-top:14px;display:flex;flex-direction:column;gap:8px;font-family:Georgia,serif;">
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gc-muted);font-style:italic;">
                            <i class="las la-download" style="color:var(--gc-rose);font-size:16px;"></i>
                            {{ __('Instant digital download') }}
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gc-muted);font-style:italic;">
                            <i class="las la-shield-alt" style="color:var(--gc-rose);font-size:16px;"></i>
                            {{ __('Secure checkout') }}
                        </div>
                        @if($product->refund_policy)
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gc-muted);font-style:italic;">
                            <i class="las la-undo-alt" style="color:var(--gc-rose);font-size:16px;"></i>
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
        <div style="display:flex;gap:0;border-bottom:1px solid var(--gc-border);margin-bottom:28px;">
            <button class="gc-dp-tab-btn active" data-target="gc-dp-tab-desc"
                    style="padding:12px 24px;background:none;border:0;border-bottom:2px solid var(--gc-rose);font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--gc-dark);cursor:pointer;margin-bottom:-1px;transition:color .2s;font-family:Georgia,serif;">
                {{ __('Description') }}
            </button>
            @if($reviews->isNotEmpty())
            <button class="gc-dp-tab-btn" data-target="gc-dp-tab-reviews"
                    style="padding:12px 24px;background:none;border:0;border-bottom:2px solid transparent;font-size:12px;font-weight:400;color:var(--gc-muted);cursor:pointer;margin-bottom:-1px;transition:color .2s;font-family:Georgia,serif;letter-spacing:1px;text-transform:uppercase;">
                {{ __('Reviews') }} ({{ $reviews->count() }})
            </button>
            @endif
            @if($product->refund_policy)
            <button class="gc-dp-tab-btn" data-target="gc-dp-tab-refund"
                    style="padding:12px 24px;background:none;border:0;border-bottom:2px solid transparent;font-size:12px;font-weight:400;color:var(--gc-muted);cursor:pointer;margin-bottom:-1px;transition:color .2s;font-family:Georgia,serif;letter-spacing:1px;text-transform:uppercase;">
                {{ __('Refund Policy') }}
            </button>
            @endif
        </div>

        <div id="gc-dp-tab-desc" class="gc-dp-tab-panel" style="display:block;">
            @if($product->description)
                <div style="font-size:14px;line-height:1.85;color:var(--gc-dark);font-family:Georgia,serif;font-style:italic;">{!! $product->description !!}</div>
            @else
                <p style="color:var(--gc-muted);font-style:italic;">{{ __('No description available.') }}</p>
            @endif
        </div>

        @if($reviews->isNotEmpty())
        <div id="gc-dp-tab-reviews" class="gc-dp-tab-panel" style="display:none;">
            <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:28px;">
                @foreach($reviews as $review)
                <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:16px;box-shadow:var(--gc-shadow);">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                        <div style="width:40px;height:40px;border-radius:50%;background:var(--gc-rose);display:flex;align-items:center;justify-content:center;font-size:15px;color:#fff;flex-shrink:0;font-family:Georgia,serif;">
                            {{ strtoupper(substr($review->user?->name ?? 'U', 0, 1)) }}
                        </div>
                        <div style="flex:1;">
                            <div style="color:var(--gc-dark);font-size:14px;font-family:Georgia,serif;font-style:italic;">{{ $review->user?->name ?? __('Anonymous') }}</div>
                            <div>{!! render_star_rating_markup($review->rating) !!}</div>
                        </div>
                        <div style="font-size:11px;color:var(--gc-muted);font-style:italic;">{{ $review->created_at->format('d M Y') }}</div>
                    </div>
                    <p style="font-size:14px;color:var(--gc-dark);margin:0;line-height:1.7;font-family:Georgia,serif;font-style:italic;">{{ $review->review_text }}</p>
                </div>
                @endforeach
            </div>

            @auth('web')
            <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:24px;box-shadow:var(--gc-shadow);">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:14px;">{{ __('Leave a Review') }}</div>
                <div style="display:flex;gap:6px;margin-bottom:16px;">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="las la-star review-star" data-value="{{ $i }}"
                       style="font-size:28px;color:var(--gc-border);cursor:pointer;transition:color .15s;"></i>
                    @endfor
                </div>
                <textarea id="review-text" rows="3"
                          style="width:100%;padding:10px 14px;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);font-size:13px;font-family:Georgia,serif;outline:none;resize:vertical;margin-bottom:14px;font-style:italic;"
                          placeholder="{{ __('Share your experience with this piece…') }}"
                          onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'"></textarea>
                <button id="review-submit-btn" data-product="{{ $product->id }}" class="gc-btn gc-btn-primary">
                    <i class="las la-paper-plane"></i> {{ __('Submit Review') }}
                </button>
            </div>
            @endauth
        </div>
        @endif

        @if($product->refund_policy)
        <div id="gc-dp-tab-refund" class="gc-dp-tab-panel" style="display:none;">
            <div style="font-size:14px;line-height:1.85;color:var(--gc-dark);font-family:Georgia,serif;font-style:italic;">
                {!! $product->refund_policy->description ?? '' !!}
            </div>
        </div>
        @endif
    </div>

    {{-- Related products --}}
    @if($related_products->isNotEmpty())
    <div style="margin-top:56px;">
        <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid var(--gc-border);">{{ __('Related Products') }}</div>
        <div class="row g-3">
            @foreach($related_products as $rp)
            @php
                $rp_data = get_digital_product_dynamic_price($rp);
                $rp_img  = (get_attachment_image_by_id($rp->image_id ?? null, 'grid'))['img_url'] ?? null;
            @endphp
            <div class="col-md-4 col-6">
                <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);overflow:hidden;box-shadow:var(--gc-shadow);transition:all .25s;"
                     onmouseover="this.style.boxShadow='0 12px 40px -8px rgba(200,169,110,.3)';this.style.transform='translateY(-3px)'"
                     onmouseout="this.style.boxShadow='var(--gc-shadow)';this.style.transform='translateY(0)'">
                    <div style="aspect-ratio:3/4;overflow:hidden;background:var(--gc-warm);display:flex;align-items:center;justify-content:center;">
                        @if($rp_img)
                            <a href="{{ dynamicRoute($rp->slug) }}">
                                <img src="{{ $rp_img }}" alt="{{ $rp->name }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                            </a>
                        @else
                            <a href="{{ dynamicRoute($rp->slug) }}" style="font-size:48px;color:var(--gc-rose);text-decoration:none;">
                                <i class="las la-file-download"></i>
                            </a>
                        @endif
                    </div>
                    <div style="padding:14px;font-family:Georgia,serif;">
                        @if($rp->category)
                            <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:var(--gc-rose);margin-bottom:4px;">{{ $rp->category->name }}</div>
                        @endif
                        <a href="{{ dynamicRoute($rp->slug) }}"
                           style="font-size:13px;color:var(--gc-dark);text-decoration:none;display:block;margin-bottom:8px;line-height:1.4;font-style:italic;"
                           onmouseover="this.style.color='var(--gc-rose)'" onmouseout="this.style.color='var(--gc-dark)'">
                            {{ \Illuminate\Support\Str::words($rp->name, 8) }}
                        </a>
                        <div style="font-size:15px;color:var(--gc-rose);margin-bottom:10px;font-style:italic;">
                            {{ amount_with_currency_symbol($rp_data['sale_price']) }}
                        </div>
                        <a href="{{ dynamicRoute($rp->slug) }}" class="gc-btn gc-btn-primary" style="font-size:11px;justify-content:center;">
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

    $(document).on('click', '.gc-dp-tab-btn', function () {
        var target = $(this).data('target');
        $('.gc-dp-tab-btn').css({ borderBottomColor: 'transparent', color: 'var(--gc-muted)', fontWeight: '400' }).removeClass('active');
        $(this).css({ borderBottomColor: 'var(--gc-rose)', color: 'var(--gc-dark)', fontWeight: '700' }).addClass('active');
        $('.gc-dp-tab-panel').hide();
        $('#' + target).show();
    });

    var selectedRating = 0;
    $(document).on('mouseover', '.review-star', function () {
        var val = $(this).data('value');
        $('.review-star').each(function (i) {
            $(this).css('color', i < val ? '#f5c518' : 'var(--gc-border)');
        });
    }).on('mouseout', '.review-star', function () {
        $('.review-star').each(function (i) {
            $(this).css('color', i < selectedRating ? '#f5c518' : 'var(--gc-border)');
        });
    }).on('click', '.review-star', function () {
        selectedRating = $(this).data('value');
        $('.review-star').each(function (i) {
            $(this).css('color', i < selectedRating ? '#f5c518' : 'var(--gc-border)');
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
