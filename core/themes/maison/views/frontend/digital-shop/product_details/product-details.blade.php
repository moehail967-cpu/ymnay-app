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

{{-- Page Banner --}}
<div style="background:var(--ms-warm);border-bottom:1px solid var(--ms-border);padding:32px 0 24px;">
    <div class="container">
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--ms-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--ms-linen-d);text-decoration:none;font-weight:600;"
               onmouseover="this.style.color='var(--ms-olive)'" onmouseout="this.style.color='var(--ms-linen-d)'">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;color:var(--ms-border);"></i>
            <a href="{{ theme_digital_shop_url() }}" style="color:var(--ms-linen-d);text-decoration:none;font-weight:600;"
               onmouseover="this.style.color='var(--ms-olive)'" onmouseout="this.style.color='var(--ms-linen-d)'">{{ __('Digital Shop') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;color:var(--ms-border);"></i>
            <span>{{ \Illuminate\Support\Str::words($product->name, 6) }}</span>
        </div>
    </div>
</div>

{{-- Product Section --}}
<section style="background:var(--ms-cream);padding:48px 0 64px;">
    <div class="container">
        <div class="row g-4">

            {{-- Cover Image --}}
            <div class="col-lg-3 col-md-4">
                <div style="position:sticky;top:100px;">
                    @if($img_url)
                        <img src="{{ $img_url }}" alt="{{ $product->name }}"
                             style="width:100%;border-radius:var(--ms-radius);border:1px solid var(--ms-border);object-fit:cover;aspect-ratio:3/4;box-shadow:var(--ms-shadow);">
                    @else
                        <div style="width:100%;aspect-ratio:3/4;border-radius:var(--ms-radius);border:1px solid var(--ms-border);background:var(--ms-warm);display:flex;align-items:center;justify-content:center;box-shadow:var(--ms-shadow);">
                            <i class="mdi mdi-file-download-outline" style="font-size:64px;color:var(--ms-border);"></i>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Product Info --}}
            <div class="col-lg-6 col-md-8">
                @if($category)
                    <div style="font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:8px;">{{ $category->name }}</div>
                @endif
                <h1 style="font-size:clamp(20px,3vw,30px);font-weight:400;color:var(--ms-dark);line-height:1.2;margin-bottom:12px;">{!! $product->name !!}</h1>

                @if($author_name)
                    <div style="font-size:13px;color:var(--ms-muted);margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                        <i class="mdi mdi-account-outline" style="color:var(--ms-olive);"></i> {{ $author_name }}
                    </div>
                @endif

                @if($avg_rating > 0)
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:14px;">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="las {{ $i <= $avg_rating ? 'la-star' : 'la-star' }}"
                           style="font-size:16px;color:{{ $i <= $avg_rating ? '#C8A45A' : 'var(--ms-border)' }};"></i>
                    @endfor
                    <span style="font-size:12px;color:var(--ms-muted);">({{ $reviews->count() }})</span>
                </div>
                @endif

                {{-- Price --}}
                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:24px;">
                    <span style="font-size:28px;font-weight:600;color:var(--ms-dark);">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($discount > 0)
                        <span style="font-size:16px;color:var(--ms-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($regular_price) }}</span>
                        <span style="background:var(--ms-olive);color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:2px;letter-spacing:.05em;">{{ $discount }}% {{ __('off') }}</span>
                    @endif
                </div>

                {{-- Specs Grid --}}
                <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:16px;margin-bottom:20px;">
                    <div class="row g-3">
                        @if($product->additionalFields?->file_format)
                        <div class="col-6">
                            <div style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);">{{ __('Format') }}</div>
                            <div style="color:var(--ms-dark);font-weight:600;font-size:14px;margin-top:4px;">{{ strtoupper($product->additionalFields->file_format) }}</div>
                        </div>
                        @endif
                        @if($product->additionalFields?->number_of_pages)
                        <div class="col-6">
                            <div style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);">{{ __('Pages') }}</div>
                            <div style="color:var(--ms-dark);font-weight:600;font-size:14px;margin-top:4px;">{{ $product->additionalFields->number_of_pages }}</div>
                        </div>
                        @endif
                        @if($product->additionalFields?->getLanguage?->name)
                        <div class="col-6">
                            <div style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);">{{ __('Language') }}</div>
                            <div style="color:var(--ms-dark);font-weight:600;font-size:14px;margin-top:4px;">{{ $product->additionalFields->getLanguage->name }}</div>
                        </div>
                        @endif
                        @if($product->additionalFields?->file_size)
                        <div class="col-6">
                            <div style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);">{{ __('File Size') }}</div>
                            <div style="color:var(--ms-dark);font-weight:600;font-size:14px;margin-top:4px;">{{ $product->additionalFields->file_size }}</div>
                        </div>
                        @endif
                        @if($product->downloads_count ?? 0)
                        <div class="col-6">
                            <div style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);">{{ __('Downloads') }}</div>
                            <div style="color:var(--ms-dark);font-weight:600;font-size:14px;margin-top:4px;">{{ $product->downloads_count }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                @if($product->short_description)
                <div style="font-size:14px;color:var(--ms-charcoal);line-height:1.8;margin-bottom:16px;">
                    {!! $product->short_description !!}
                </div>
                @endif

                @if($product->tag && $product->tag->isNotEmpty())
                <div style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;margin-bottom:8px;">
                    <span style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);">{{ __('Tags:') }}</span>
                    @foreach($product->tag as $tag)
                        <span style="background:var(--ms-warm);border:1px solid var(--ms-border);color:var(--ms-muted);padding:3px 10px;border-radius:2px;font-size:11px;font-weight:600;letter-spacing:.04em;text-transform:uppercase;">{{ $tag->tag_name }}</span>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Purchase Sidebar --}}
            <div class="col-lg-3">
                <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:24px;position:sticky;top:100px;box-shadow:var(--ms-shadow);">
                    <div style="font-size:26px;font-weight:600;color:var(--ms-dark);margin-bottom:6px;">{{ amount_with_currency_symbol($sale_price) }}</div>
                    @if($discount > 0)
                    <div style="font-size:13px;color:var(--ms-muted);margin-bottom:16px;">
                        {{ __('Was') }} <span style="text-decoration:line-through;">{{ amount_with_currency_symbol($regular_price) }}</span>
                        — <span style="color:var(--ms-olive);font-weight:700;">{{ $discount }}% {{ __('off') }}</span>
                    </div>
                    @endif

                    @auth('web')
                    <form action="{{ theme_digital_product_add_to_cart_url() }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="ms-btn-full ms-btn-dark" style="margin-bottom:10px;">
                            <i class="mdi mdi-cart-plus" style="margin-right:8px;font-size:16px;"></i>
                            {{ __('Get Now') }}
                        </button>
                    </form>
                    @else
                    <a href="{{ theme_login_url() }}" class="ms-btn-full ms-btn-dark" style="display:flex;margin-bottom:10px;">
                        <i class="mdi mdi-login" style="margin-right:8px;font-size:16px;"></i>
                        {{ __('Sign In to Purchase') }}
                    </a>
                    @endauth

                    <button class="ms-btn-full ms-btn-border add-to-wishlist-btn"
                            data-product_id="{{ $product->id }}"
                            style="cursor:pointer;margin-bottom:20px;display:flex;">
                        <i class="mdi mdi-heart-outline" style="margin-right:8px;font-size:16px;"></i>
                        {{ __('Add to Wishlist') }}
                    </button>

                    {{-- Trust badges --}}
                    <div style="border-top:1px solid var(--ms-border);padding-top:16px;display:flex;flex-direction:column;gap:10px;">
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--ms-muted);">
                            <i class="mdi mdi-download-circle-outline" style="font-size:18px;color:var(--ms-olive);flex-shrink:0;"></i>
                            {{ __('Instant digital download') }}
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--ms-muted);">
                            <i class="mdi mdi-shield-check-outline" style="font-size:18px;color:var(--ms-olive);flex-shrink:0;"></i>
                            {{ __('Secure checkout') }}
                        </div>
                        @if($product->refund_policy)
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--ms-muted);">
                            <i class="mdi mdi-undo-variant" style="font-size:18px;color:var(--ms-olive);flex-shrink:0;"></i>
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
<section style="background:#fff;border-top:1px solid var(--ms-border);border-bottom:1px solid var(--ms-border);padding:48px 0 64px;">
    <div class="container">

        {{-- Tab Nav --}}
        <div style="display:flex;gap:0;border-bottom:2px solid var(--ms-border);margin-bottom:32px;flex-wrap:wrap;">
            <button class="ms-tab-btn active" data-tab="ms-tab-desc"
                    style="padding:12px 24px;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--ms-muted);background:transparent;border:0;border-bottom:2px solid transparent;margin-bottom:-2px;cursor:pointer;transition:all .2s;">
                {{ __('Description') }}
            </button>
            @if($reviews->isNotEmpty())
            <button class="ms-tab-btn" data-tab="ms-tab-reviews"
                    style="padding:12px 24px;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--ms-muted);background:transparent;border:0;border-bottom:2px solid transparent;margin-bottom:-2px;cursor:pointer;transition:all .2s;">
                {{ __('Reviews') }} ({{ $reviews->count() }})
            </button>
            @endif
            @if($product->refund_policy)
            <button class="ms-tab-btn" data-tab="ms-tab-refund"
                    style="padding:12px 24px;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--ms-muted);background:transparent;border:0;border-bottom:2px solid transparent;margin-bottom:-2px;cursor:pointer;transition:all .2s;">
                {{ __('Refund Policy') }}
            </button>
            @endif
        </div>

        <div id="ms-tab-desc" class="ms-dp-tab-panel" style="display:block;">
            @if($product->description)
                <div style="font-size:14px;color:var(--ms-charcoal);line-height:1.8;">{!! $product->description !!}</div>
            @else
                <p style="color:var(--ms-muted);">{{ __('No description available.') }}</p>
            @endif
        </div>

        @if($reviews->isNotEmpty())
        <div id="ms-tab-reviews" class="ms-dp-tab-panel" style="display:none;">
            <div style="display:flex;flex-direction:column;gap:16px;margin-bottom:28px;">
                @foreach($reviews as $review)
                <div style="border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:18px;background:var(--ms-warm);">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                        <div style="width:40px;height:40px;border-radius:50%;background:var(--ms-linen);display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;color:#fff;flex-shrink:0;">
                            {{ strtoupper(substr($review->user?->name ?? 'A', 0, 1)) }}
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:600;color:var(--ms-dark);font-size:14px;">{{ $review->user?->name ?? __('Anonymous') }}</div>
                            <div style="display:flex;gap:2px;margin-top:3px;">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="las la-star" style="font-size:13px;color:{{ $i <= $review->rating ? '#C8A45A' : 'var(--ms-border)' }};"></i>
                                @endfor
                            </div>
                        </div>
                        <div style="font-size:11px;color:var(--ms-muted);">{{ $review->created_at->format('d M Y') }}</div>
                    </div>
                    <p style="font-size:14px;color:var(--ms-charcoal);line-height:1.7;margin:0;">{{ $review->review_text }}</p>
                </div>
                @endforeach
            </div>

            @auth('web')
            <div style="background:var(--ms-warm);border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:24px;">
                <div style="font-size:11px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:16px;">{{ __('Leave a Review') }}</div>
                <div style="display:flex;gap:6px;margin-bottom:14px;">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="las la-star ms-dp-star" data-value="{{ $i }}"
                       style="font-size:28px;color:var(--ms-border);cursor:pointer;transition:color .15s;"></i>
                    @endfor
                </div>
                <textarea id="ms-dp-review-text" rows="3"
                          style="width:100%;background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);color:var(--ms-dark);padding:12px 16px;font-size:14px;font-family:inherit;outline:none;resize:vertical;margin-bottom:14px;"
                          placeholder="{{ __('Your review…') }}"
                          onfocus="this.style.borderColor='var(--ms-linen)'"
                          onblur="this.style.borderColor='var(--ms-border)'"></textarea>
                <button id="ms-dp-review-submit" data-product="{{ $product->id }}" class="ms-btn-dark">
                    <i class="mdi mdi-send-outline" style="margin-right:6px;"></i> {{ __('Submit Review') }}
                </button>
            </div>
            @endauth
        </div>
        @endif

        @if($product->refund_policy)
        <div id="ms-tab-refund" class="ms-dp-tab-panel" style="display:none;">
            <div style="font-size:14px;color:var(--ms-charcoal);line-height:1.8;">{!! $product->refund_policy->description ?? '' !!}</div>
        </div>
        @endif

    </div>
</section>

{{-- Related Products --}}
@if($related_products->isNotEmpty())
<section style="background:var(--ms-cream);padding:56px 0 72px;">
    <div class="container">
        <div style="text-align:center;margin-bottom:32px;">
            <div style="font-size:10px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:8px;">{{ __('More Like This') }}</div>
            <h2 style="font-size:clamp(20px,3vw,30px);font-weight:300;color:var(--ms-dark);">{{ __('Related Products') }}</h2>
        </div>
        <div class="row g-4">
            @foreach($related_products as $rp)
            @php
                $rp_data  = get_digital_product_dynamic_price($rp);
                $rp_img_d = get_attachment_image_by_id($rp->image_id ?? null, 'grid');
                $rp_img   = $rp_img_d['img_url'] ?? null;
            @endphp
            <div class="col-md-4">
                <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);overflow:hidden;box-shadow:var(--ms-shadow);transition:box-shadow .25s,transform .25s;"
                     onmouseover="this.style.boxShadow='0 18px 50px -10px rgba(200,184,154,.4)';this.style.transform='translateY(-3px)'"
                     onmouseout="this.style.boxShadow='var(--ms-shadow)';this.style.transform='none'">
                    <div style="aspect-ratio:4/3;overflow:hidden;background:var(--ms-warm);">
                        @if($rp_img)
                            <a href="{{ dynamicRoute($rp->slug) }}">
                                <img src="{{ $rp_img }}" alt="{{ $rp->name }}" style="width:100%;height:100%;object-fit:cover;display:block;">
                            </a>
                        @else
                            <a href="{{ dynamicRoute($rp->slug) }}" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--ms-border);">
                                <i class="mdi mdi-file-download-outline" style="font-size:48px;"></i>
                            </a>
                        @endif
                    </div>
                    <div style="padding:16px;">
                        @if($rp->category)
                            <div style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:4px;">{{ $rp->category->name }}</div>
                        @endif
                        <a href="{{ dynamicRoute($rp->slug) }}"
                           style="display:block;font-size:14px;font-weight:500;color:var(--ms-dark);text-decoration:none;margin-bottom:8px;line-height:1.4;transition:color .2s;"
                           onmouseover="this.style.color='var(--ms-linen-d)'"
                           onmouseout="this.style.color='var(--ms-dark)'">
                            {{ \Illuminate\Support\Str::words($rp->name, 8) }}
                        </a>
                        <div style="font-size:15px;font-weight:600;color:var(--ms-dark);">
                            {{ amount_with_currency_symbol($rp_data['sale_price']) }}
                            @if($rp_data['discount'] > 0)
                                <span style="font-size:12px;color:var(--ms-muted);text-decoration:line-through;font-weight:400;margin-left:4px;">{{ amount_with_currency_symbol($rp_data['regular_price']) }}</span>
                            @endif
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

    // Tab switching
    $(document).on('click', '.ms-tab-btn', function(){
        var target = $(this).data('tab');
        $('.ms-tab-btn').css({ 'color':'var(--ms-muted)', 'border-bottom-color':'transparent' });
        $(this).css({ 'color':'var(--ms-dark)', 'border-bottom-color':'var(--ms-dark)' });
        $('.ms-dp-tab-panel').hide();
        $('#'+target).show();
    });

    // Star rating
    var selectedRating = 0;
    $(document).on('mouseover', '.ms-dp-star', function(){
        var val = $(this).data('value');
        $('.ms-dp-star').each(function(i){ $(this).css('color', i < val ? '#C8A45A' : 'var(--ms-border)'); });
    }).on('mouseout', '.ms-dp-star', function(){
        $('.ms-dp-star').each(function(i){ $(this).css('color', i < selectedRating ? '#C8A45A' : 'var(--ms-border)'); });
    }).on('click', '.ms-dp-star', function(){
        selectedRating = $(this).data('value');
        $('.ms-dp-star').each(function(i){ $(this).css('color', i < selectedRating ? '#C8A45A' : 'var(--ms-border)'); });
    });

    // Submit review
    $(document).on('click', '#ms-dp-review-submit', function(){
        var btn = $(this);
        var product_id = btn.data('product');
        var rating = selectedRating;
        var review_text = $('#ms-dp-review-text').val().trim();
        if(!rating){ toastr.warning('{{ __("Please select a rating") }}'); return; }
        if(!review_text){ toastr.warning('{{ __("Please enter a review") }}'); return; }
        $.ajax({
            url: '{{ theme_digital_product_review_url() }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', product_id: product_id, rating: rating, review_text: review_text },
            beforeSend: function(){ btn.text('{{ __("Submitting…") }}').prop('disabled', true); },
            success: function(data){
                if(data.type === 'success'){ toastr.success(data.msg); setTimeout(function(){ location.reload(); }, 1000); }
                else{ toastr.error(data.msg); btn.html('<i class="mdi mdi-send-outline" style="margin-right:6px;"></i> {{ __("Submit Review") }}').prop('disabled', false); }
            },
            error: function(){ btn.html('<i class="mdi mdi-send-outline" style="margin-right:6px;"></i> {{ __("Submit Review") }}').prop('disabled', false); }
        });
    });

})(jQuery);
</script>
@endsection
