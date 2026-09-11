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

{{-- Page banner --}}
<div style="background:var(--gl-gold-pale);border-bottom:1px solid var(--gl-border);padding:28px 0 22px;">
    <div class="container">
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gl-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="opacity:.5;"></i>
            <a href="{{ theme_digital_shop_url() }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Digital Shop') }}</a>
            <i class="mdi mdi-chevron-right" style="opacity:.5;"></i>
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
                         style="width:100%;border-radius:var(--gl-radius);border:1px solid var(--gl-border);object-fit:cover;aspect-ratio:3/4;box-shadow:var(--gl-shadow);">
                @else
                    <div style="width:100%;aspect-ratio:3/4;border-radius:var(--gl-radius);background:var(--gl-gold-pale);border:1px solid var(--gl-border);display:flex;align-items:center;justify-content:center;font-size:64px;color:var(--gl-gold);">
                        <i class="mdi mdi-file-download-outline"></i>
                    </div>
                @endif
            </div>
        </div>

        {{-- Product info --}}
        <div class="col-lg-6 col-md-8">
            @if($category)
                <div style="display:inline-block;background:var(--gl-gold-pale);color:var(--gl-gold);font-size:10px;font-weight:700;padding:4px 12px;border-radius:50px;margin-bottom:10px;text-transform:uppercase;letter-spacing:.8px;">{{ $category->name }}</div>
            @endif

            <h1 style="font-size:24px;font-weight:300;color:var(--gl-dark);margin-bottom:10px;letter-spacing:-.3px;line-height:1.3;">{!! $product->name !!}</h1>

            @if($author_name)
            <div style="font-size:13px;color:var(--gl-muted);margin-bottom:10px;">
                <i class="mdi mdi-account-outline" style="color:var(--gl-gold);"></i> {{ $author_name }}
            </div>
            @endif

            @if($avg_rating > 0)
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:14px;">
                @for($i = 1; $i <= 5; $i++)
                    <i class="mdi mdi-star{{ $i <= $avg_rating ? '' : ($i - $avg_rating < 1 ? '-half-full' : '-outline') }}"
                       style="color:{{ $i <= $avg_rating ? '#f5c518' : 'var(--gl-border)' }};font-size:16px;"></i>
                @endfor
                <span style="font-size:12px;color:var(--gl-muted);">({{ $reviews->count() }})</span>
            </div>
            @endif

            {{-- Price --}}
            <div style="display:flex;align-items:baseline;gap:12px;margin-bottom:20px;">
                <span style="font-size:28px;font-weight:700;color:var(--gl-gold);">{{ amount_with_currency_symbol($sale_price) }}</span>
                @if($discount > 0)
                    <span style="font-size:14px;color:var(--gl-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($regular_price) }}</span>
                    <span style="font-size:12px;font-weight:700;color:#fff;background:var(--gl-gold);padding:3px 10px;border-radius:50px;">{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            {{-- Meta specs --}}
            @if($product->additionalFields?->file_format || $product->additionalFields?->number_of_pages || $product->additionalFields?->language || $product->additionalFields?->file_size || ($product->downloads_count ?? 0))
            <div style="background:var(--gl-gold-pale);border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:16px;margin-bottom:20px;">
                <div class="row g-3">
                    @if($product->additionalFields?->file_format)
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--gl-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('Format') }}</div>
                        <div style="font-weight:700;color:var(--gl-dark);font-size:14px;">{{ strtoupper($product->additionalFields->file_format) }}</div>
                    </div>
                    @endif
                    @if($product->additionalFields?->number_of_pages)
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--gl-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('Pages') }}</div>
                        <div style="font-weight:700;color:var(--gl-dark);font-size:14px;">{{ $product->additionalFields->number_of_pages }}</div>
                    </div>
                    @endif
                    @if($product->additionalFields?->language)
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--gl-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('Language') }}</div>
                        <div style="font-weight:700;color:var(--gl-dark);font-size:14px;">{{ $product->additionalFields->getLanguage?->name }}</div>
                    </div>
                    @endif
                    @if($product->additionalFields?->file_size)
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--gl-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('File Size') }}</div>
                        <div style="font-weight:700;color:var(--gl-dark);font-size:14px;">{{ $product->additionalFields->file_size }}</div>
                    </div>
                    @endif
                    @if($product->downloads_count ?? 0)
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--gl-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">{{ __('Downloads') }}</div>
                        <div style="font-weight:700;color:var(--gl-dark);font-size:14px;">{{ $product->downloads_count }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Short description --}}
            @if($product->short_description)
            <div style="font-size:14px;color:var(--gl-muted);line-height:1.7;margin-bottom:16px;">
                {!! $product->short_description !!}
            </div>
            @endif

            {{-- Tags --}}
            @if($product->tag && $product->tag->isNotEmpty())
            <div style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;">
                <span style="font-size:12px;color:var(--gl-muted);font-weight:600;">{{ __('Tags:') }}</span>
                @foreach($product->tag as $tag)
                    <span style="background:var(--gl-gold-pale);color:var(--gl-gold);padding:3px 10px;border-radius:50px;font-size:11px;font-weight:600;">{{ $tag->tag_name }}</span>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Purchase sidebar --}}
        <div class="col-lg-3">
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;box-shadow:var(--gl-shadow);position:sticky;top:100px;">
                <div style="background:var(--gl-gold-pale);padding:16px 20px;border-bottom:1px solid var(--gl-border);">
                    <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);">{{ __('Get This Product') }}</div>
                </div>
                <div style="padding:20px;">
                    <div style="font-size:28px;font-weight:700;color:var(--gl-gold);margin-bottom:4px;">
                        {{ amount_with_currency_symbol($sale_price) }}
                    </div>
                    @if($discount > 0)
                    <div style="font-size:12px;color:var(--gl-muted);margin-bottom:16px;">
                        {{ __('Was') }} <span style="text-decoration:line-through;">{{ amount_with_currency_symbol($regular_price) }}</span>
                        — <span style="color:var(--gl-gold);font-weight:700;">{{ $discount }}% {{ __('off') }}</span>
                    </div>
                    @else
                    <div style="margin-bottom:16px;"></div>
                    @endif

                    @auth('web')
                    <form action="{{ theme_digital_product_add_to_cart_url() }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit"
                                style="width:100%;padding:12px;background:var(--gl-dark);color:#fff;border:0;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .2s;margin-bottom:10px;"
                                onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                            <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                        </button>
                    </form>
                    @else
                    <a href="{{ theme_login_url() }}"
                       style="width:100%;padding:12px;background:var(--gl-dark);color:#fff;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .2s;margin-bottom:10px;text-decoration:none;"
                       onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                        <i class="mdi mdi-login-variant"></i> {{ __('Sign In to Purchase') }}
                    </a>
                    @endauth

                    <button class="add-to-wishlist-btn" data-product_id="{{ $product->id }}"
                            style="width:100%;padding:10px;background:#fff;color:var(--gl-dark);border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;transition:all .2s;margin-bottom:16px;"
                            onmouseover="this.style.borderColor='var(--gl-gold)';this.style.color='var(--gl-gold)'"
                            onmouseout="this.style.borderColor='var(--gl-border)';this.style.color='var(--gl-dark)'">
                        <i class="mdi mdi-heart-outline"></i> {{ __('Wishlist') }}
                    </button>

                    <div style="border-top:1px solid var(--gl-border);padding-top:14px;display:flex;flex-direction:column;gap:8px;">
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gl-muted);">
                            <i class="mdi mdi-cloud-download-outline" style="color:var(--gl-gold);font-size:16px;"></i>
                            {{ __('Instant digital download') }}
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gl-muted);">
                            <i class="mdi mdi-shield-check-outline" style="color:var(--gl-gold);font-size:16px;"></i>
                            {{ __('Secure checkout') }}
                        </div>
                        @if($product->refund_policy)
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gl-muted);">
                            <i class="mdi mdi-refresh" style="color:var(--gl-gold);font-size:16px;"></i>
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
        <div style="display:flex;gap:0;border-bottom:2px solid var(--gl-border);margin-bottom:28px;">
            <button class="gl-dp-tab-btn active" data-target="gl-dp-tab-desc"
                    style="padding:12px 24px;background:none;border:0;border-bottom:2px solid var(--gl-gold);font-size:13px;font-weight:700;color:var(--gl-dark);cursor:pointer;margin-bottom:-2px;transition:color .2s;letter-spacing:.3px;">
                {{ __('Description') }}
            </button>
            @if($reviews->isNotEmpty())
            <button class="gl-dp-tab-btn" data-target="gl-dp-tab-reviews"
                    style="padding:12px 24px;background:none;border:0;border-bottom:2px solid transparent;font-size:13px;font-weight:600;color:var(--gl-muted);cursor:pointer;margin-bottom:-2px;transition:color .2s;letter-spacing:.3px;">
                {{ __('Reviews') }} ({{ $reviews->count() }})
            </button>
            @endif
            @if($product->refund_policy)
            <button class="gl-dp-tab-btn" data-target="gl-dp-tab-refund"
                    style="padding:12px 24px;background:none;border:0;border-bottom:2px solid transparent;font-size:13px;font-weight:600;color:var(--gl-muted);cursor:pointer;margin-bottom:-2px;transition:color .2s;letter-spacing:.3px;">
                {{ __('Refund Policy') }}
            </button>
            @endif
        </div>

        <div id="gl-dp-tab-desc" class="gl-dp-tab-panel" style="display:block;">
            @if($product->description)
                <div style="font-size:14px;line-height:1.8;color:var(--gl-dark);">{!! $product->description !!}</div>
            @else
                <p style="color:var(--gl-muted);">{{ __('No description available.') }}</p>
            @endif
        </div>

        @if($reviews->isNotEmpty())
        <div id="gl-dp-tab-reviews" class="gl-dp-tab-panel" style="display:none;">
            <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:28px;">
                @foreach($reviews as $review)
                <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:16px;box-shadow:var(--gl-shadow);">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                        <div style="width:40px;height:40px;border-radius:50%;background:var(--gl-dark);display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:15px;flex-shrink:0;">
                            {{ strtoupper(substr($review->user?->name ?? 'U', 0, 1)) }}
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:700;color:var(--gl-dark);font-size:14px;">{{ $review->user?->name ?? __('Anonymous') }}</div>
                            <div>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="mdi mdi-star{{ $i <= $review->rating ? '' : '-outline' }}"
                                       style="color:{{ $i <= $review->rating ? '#f5c518' : 'var(--gl-border)' }};font-size:13px;"></i>
                                @endfor
                            </div>
                        </div>
                        <div style="font-size:11px;color:var(--gl-muted);">{{ $review->created_at->format('d M Y') }}</div>
                    </div>
                    <p style="font-size:14px;color:var(--gl-dark);margin:0;line-height:1.6;">{{ $review->review_text }}</p>
                </div>
                @endforeach
            </div>

            @auth('web')
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:24px;box-shadow:var(--gl-shadow);">
                <div style="font-weight:700;color:var(--gl-dark);margin-bottom:14px;font-size:15px;">{{ __('Leave a Review') }}</div>
                <div style="display:flex;gap:6px;margin-bottom:16px;">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="mdi mdi-star-outline review-star" data-value="{{ $i }}"
                       style="font-size:28px;color:var(--gl-border);cursor:pointer;transition:color .15s;"></i>
                    @endfor
                </div>
                <textarea id="review-text" rows="3"
                          style="width:100%;padding:10px 14px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:13px;font-family:inherit;outline:none;resize:vertical;margin-bottom:14px;"
                          placeholder="{{ __('Write your review…') }}"></textarea>
                <button id="review-submit-btn" data-product="{{ $product->id }}"
                        style="padding:10px 24px;background:var(--gl-dark);color:#fff;border:0;border-radius:var(--gl-radius);font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;display:flex;align-items:center;gap:6px;transition:background .2s;"
                        onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                    <i class="mdi mdi-send"></i> {{ __('Submit Review') }}
                </button>
            </div>
            @endauth
        </div>
        @endif

        @if($product->refund_policy)
        <div id="gl-dp-tab-refund" class="gl-dp-tab-panel" style="display:none;">
            <div style="font-size:14px;line-height:1.8;color:var(--gl-dark);">
                {!! $product->refund_policy->description ?? '' !!}
            </div>
        </div>
        @endif
    </div>

    {{-- Related products --}}
    @if($related_products->isNotEmpty())
    <div style="margin-top:56px;">
        <h2 style="font-size:20px;font-weight:300;color:var(--gl-dark);margin-bottom:20px;letter-spacing:-.3px;padding-bottom:12px;border-bottom:1px solid var(--gl-border);">{{ __('Related Products') }}</h2>
        <div class="row g-3">
            @foreach($related_products as $rp)
            @php
                $rp_data = get_digital_product_dynamic_price($rp);
                $rp_img  = (get_attachment_image_by_id($rp->image_id ?? null, 'grid'))['img_url'] ?? null;
            @endphp
            <div class="col-md-4 col-6">
                <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;box-shadow:var(--gl-shadow);transition:box-shadow .2s,transform .2s;"
                     onmouseover="this.style.boxShadow='0 12px 40px -8px rgba(184,150,90,.2)';this.style.transform='translateY(-3px)'"
                     onmouseout="this.style.boxShadow='var(--gl-shadow)';this.style.transform='translateY(0)'">
                    <div style="aspect-ratio:3/4;overflow:hidden;background:var(--gl-gold-pale);display:flex;align-items:center;justify-content:center;">
                        @if($rp_img)
                            <a href="{{ dynamicRoute($rp->slug) }}">
                                <img src="{{ $rp_img }}" alt="{{ $rp->name }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                            </a>
                        @else
                            <a href="{{ dynamicRoute($rp->slug) }}" style="font-size:48px;color:var(--gl-gold);text-decoration:none;">
                                <i class="mdi mdi-file-download-outline"></i>
                            </a>
                        @endif
                    </div>
                    <div style="padding:14px;">
                        @if($rp->category)
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--gl-gold);margin-bottom:4px;">{{ $rp->category->name }}</div>
                        @endif
                        <a href="{{ dynamicRoute($rp->slug) }}"
                           style="font-size:13px;font-weight:600;color:var(--gl-dark);text-decoration:none;display:block;margin-bottom:8px;line-height:1.4;"
                           onmouseover="this.style.color='var(--gl-gold)'" onmouseout="this.style.color='var(--gl-dark)'">
                            {{ \Illuminate\Support\Str::words($rp->name, 8) }}
                        </a>
                        <div style="font-size:15px;font-weight:700;color:var(--gl-gold);margin-bottom:10px;">
                            {{ amount_with_currency_symbol($rp_data['sale_price']) }}
                        </div>
                        <a href="{{ dynamicRoute($rp->slug) }}"
                           style="display:flex;align-items:center;justify-content:center;gap:6px;padding:8px;background:var(--gl-dark);color:#fff;border-radius:var(--gl-radius);font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;text-decoration:none;transition:background .2s;"
                           onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
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

    // Tab switching
    $(document).on('click', '.gl-dp-tab-btn', function () {
        var target = $(this).data('target');
        $('.gl-dp-tab-btn').css({ borderBottomColor: 'transparent', color: 'var(--gl-muted)', fontWeight: '600' }).removeClass('active');
        $(this).css({ borderBottomColor: 'var(--gl-gold)', color: 'var(--gl-dark)', fontWeight: '700' }).addClass('active');
        $('.gl-dp-tab-panel').hide();
        $('#' + target).show();
    });

    // Star hover & select
    var selectedRating = 0;
    $(document).on('mouseover', '.review-star', function () {
        var val = $(this).data('value');
        $('.review-star').each(function (i) {
            $(this).css('color', i < val ? '#f5c518' : 'var(--gl-border)');
        });
    }).on('mouseout', '.review-star', function () {
        $('.review-star').each(function (i) {
            $(this).css('color', i < selectedRating ? '#f5c518' : 'var(--gl-border)');
        });
    }).on('click', '.review-star', function () {
        selectedRating = $(this).data('value');
        $('.review-star').each(function (i) {
            $(this).css('color', i < selectedRating ? '#f5c518' : 'var(--gl-border)');
        });
    });

    // Submit review
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
                    btn.html('<i class="mdi mdi-send"></i> {{ __("Submit Review") }}');
                }
            },
            error: function () {
                btn.html('<i class="mdi mdi-send"></i> {{ __("Submit Review") }}');
            }
        });
    });

})(jQuery);
</script>
@endsection
