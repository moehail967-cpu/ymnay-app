@extends('tenant.frontend.frontend-page-master')
@section('title') {!! $product->name !!} @endsection

@section('style')
<link rel="stylesheet" href="{{ global_asset('assets/common/css/star-rating.min.css') }}">
<style>
.lx-dp-tab-btn { padding:12px 24px;font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--lx-muted);background:none;border:none;border-bottom:2px solid transparent;margin-bottom:-1px;cursor:pointer;font-family:inherit;transition:all .2s; }
.lx-dp-tab-btn.active { color:var(--lx-gold);border-bottom-color:var(--lx-gold); }
.lx-dp-tab-panel { display:none; }
.lx-dp-tab-panel.active { display:block; }
</style>
@endsection

@section('content')
@php
    $dp       = get_digital_product_dynamic_price($product);
    $sale     = $dp['sale_price'];
    $original = $dp['regular_price'];
    $discount = $dp['discount'];
    $price    = $sale > 0 ? $sale : $original;
    $img      = get_attachment_image_by_id($product->image_id ?? null, null);
    $img_url  = $img['img_url'] ?? null;
    $af       = $product->additionalFields ?? null;
@endphp

<div class="lg-breadcrumb-bar">
    <div class="container">
        <div class="lg-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ theme_digital_shop_url() }}">{{ __('Digital') }}</a>
            <span class="sep">/</span>
            <span class="active">{{ $product->name }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:40px 0 80px;">
    <div class="row g-5">

        {{-- Cover image --}}
        <div class="col-lg-4">
            <div style="position:sticky;top:100px;">
                <div style="aspect-ratio:3/4;background:var(--lx-surface);overflow:hidden;border:1px solid var(--lx-border);">
                    @if($img_url)
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:72px;color:var(--lx-gold);">
                            <i class="las la-file-download"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Info --}}
        <div class="col-lg-5">
            @if($product->category)
                <div class="lx-card-material mb-2">{{ $product->category->name }}</div>
            @endif
            <h1 style="font-size:clamp(20px,3vw,32px);font-weight:300;color:var(--lx-white);margin-bottom:12px;letter-spacing:-.3px;">{{ $product->name }}</h1>

            @if($af?->author)
                <div style="font-size:13px;color:var(--lx-muted);margin-bottom:12px;">
                    <i class="las la-user"></i> {{ $af->author->name }}
                </div>
            @endif

            <div style="margin-bottom:16px;">{!! render_star_rating_markup($product->rating ?? 0) !!}</div>

            {{-- Price --}}
            <div class="mb-4">
                <span class="lx-price" style="font-size:26px;font-weight:300;">{{ amount_with_currency_symbol($price) }}</span>
                @if($discount > 0 && $original)
                    <span class="lx-price-old ms-3" style="font-size:18px;">{{ amount_with_currency_symbol($original) }}</span>
                    <span style="margin-left:10px;font-size:11px;background:rgba(201,168,76,.15);color:var(--lx-gold);padding:3px 10px;letter-spacing:1px;">{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            {{-- Specs --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:24px;">
                @if($af?->format)
                <div style="padding:12px;border:1px solid var(--lx-border);background:var(--lx-surface);">
                    <div style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--lx-muted);">{{ __('Format') }}</div>
                    <div style="font-size:13px;color:var(--lx-white);margin-top:4px;">{{ $af->format->name ?? $af->format }}</div>
                </div>
                @endif
                @if($af?->language)
                <div style="padding:12px;border:1px solid var(--lx-border);background:var(--lx-surface);">
                    <div style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--lx-muted);">{{ __('Language') }}</div>
                    <div style="font-size:13px;color:var(--lx-white);margin-top:4px;">{{ $product->getLanguage?->name ?? '—' }}</div>
                </div>
                @endif
                @if($product->file_size)
                <div style="padding:12px;border:1px solid var(--lx-border);background:var(--lx-surface);">
                    <div style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--lx-muted);">{{ __('File Size') }}</div>
                    <div style="font-size:13px;color:var(--lx-white);margin-top:4px;">{{ $product->file_size }}</div>
                </div>
                @endif
                @if($product->downloads_count)
                <div style="padding:12px;border:1px solid var(--lx-border);background:var(--lx-surface);">
                    <div style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--lx-muted);">{{ __('Downloads') }}</div>
                    <div style="font-size:13px;color:var(--lx-white);margin-top:4px;">{{ $product->downloads_count }}</div>
                </div>
                @endif
            </div>

            {{-- Description excerpt --}}
            <div style="font-size:13px;color:var(--lx-muted);line-height:1.8;margin-bottom:24px;">
                {{ \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 200) }}
            </div>
        </div>

        {{-- Purchase sidebar --}}
        <div class="col-lg-3">
            <div style="position:sticky;top:100px;background:var(--lx-card);border:1px solid var(--lx-border);padding:24px;">
                <div class="lx-price" style="font-size:22px;font-weight:300;margin-bottom:20px;">
                    {{ amount_with_currency_symbol($price) }}
                </div>

                <form action="{{ theme_digital_product_add_to_cart_url() }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" class="lx-btn lx-btn-primary w-100 justify-content-center mb-3" style="padding:13px;">
                        <i class="las la-download"></i> {{ __('Get Now') }}
                    </button>
                </form>

                <button class="lx-btn lx-btn-outline w-100 justify-content-center add-to-wishlist-btn mb-4"
                        data-product_id="{{ $product->id }}" style="padding:11px;">
                    <i class="las la-heart"></i> {{ __('Save to Wishlist') }}
                </button>

                <div style="font-size:11px;color:var(--lx-muted);display:flex;flex-direction:column;gap:8px;">
                    <span><i class="las la-shield-alt" style="color:var(--lx-gold);"></i> {{ __('Secure checkout') }}</span>
                    <span><i class="las la-download" style="color:var(--lx-gold);"></i> {{ __('Instant download') }}</span>
                    <span><i class="las la-sync" style="color:var(--lx-gold);"></i> {{ __('Lifetime access') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="margin-top:56px;">
        <div style="display:flex;border-bottom:1px solid var(--lx-border);margin-bottom:24px;">
            <button class="lx-dp-tab-btn active" data-target="lx-dp-tab-desc">{{ __('Description') }}</button>
            <button class="lx-dp-tab-btn" data-target="lx-dp-tab-reviews">{{ __('Reviews') }}</button>
        </div>

        <div id="lx-dp-tab-desc" class="lx-dp-tab-panel active" style="color:var(--lx-muted);font-size:14px;line-height:1.8;">
            {!! $product->description !!}
        </div>

        <div id="lx-dp-tab-reviews" class="lx-dp-tab-panel">
            <div class="all-reviews">
                @forelse($product->reviews ?? [] as $review)
                <div style="display:flex;gap:16px;padding:20px 0;border-bottom:1px solid var(--lx-border);">
                    <div style="width:42px;height:42px;border-radius:50%;background:var(--lx-surface);border:1px solid var(--lx-border);display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:600;color:var(--lx-gold);flex-shrink:0;">
                        {{ strtoupper(substr($review->customer_name ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:var(--lx-white);">{{ $review->customer_name }}</div>
                        <div style="font-size:11px;color:var(--lx-gold);margin:3px 0;">
                            @for($i=1;$i<=5;$i++)<i class="{{ $i <= $review->rating ? 'las la-star' : 'lar la-star' }}"></i>@endfor
                        </div>
                        <p style="font-size:13px;color:var(--lx-muted);margin:6px 0 0;">{{ $review->review_text }}</p>
                    </div>
                </div>
                @empty
                <p style="color:var(--lx-muted);font-size:13px;">{{ __('No reviews yet.') }}</p>
                @endforelse
            </div>

            @auth('web')
            <div style="margin-top:24px;padding-top:20px;border-top:1px solid var(--lx-border);">
                <h6 style="font-size:12px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--lx-white);margin-bottom:16px;">{{ __('Write a Review') }}</h6>
                <div style="display:flex;gap:4px;margin-bottom:12px;" id="lx-dp-stars">
                    @for($s=1;$s<=5;$s++)
                    <i class="las la-star lx-dp-star-icon" data-value="{{ $s }}"
                       style="font-size:22px;cursor:pointer;color:var(--lx-border);transition:color .15s;"></i>
                    @endfor
                </div>
                <span class="rating-selected" data-value="" style="display:none;"></span>
                <textarea id="review-text" class="lg-form-control" rows="3"
                          placeholder="{{ __('Share your experience…') }}" style="margin-bottom:12px;"></textarea>
                <button id="review-submit-btn" class="lx-btn lx-btn-primary" style="font-size:12px;">
                    <i class="las la-paper-plane"></i> {{ __('Submit') }}
                </button>
            </div>
            @endauth
        </div>
    </div>

    {{-- Related --}}
    @if(!empty($related_products) && count($related_products) > 0)
    <div style="margin-top:56px;">
        <div class="lx-hero-eyebrow">{{ __('You May Also Like') }}</div>
        <div class="row g-3 mt-2">
            @foreach($related_products as $rp)
            @php
                $rdp = get_digital_product_dynamic_price($rp);
                $rimg = get_attachment_image_by_id($rp->image_id ?? null, 'grid');
                $rimg_url = $rimg['img_url'] ?? null;
                $rprice = $rdp['sale_price'] > 0 ? $rdp['sale_price'] : $rdp['regular_price'];
            @endphp
            <div class="col-6 col-md-3">
                <div class="lx-card">
                    <div class="lx-card-img">
                        @if($rimg_url)<img src="{{ $rimg_url }}" alt="{{ $rp->name }}" loading="lazy">
                        @else<div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:36px;color:var(--lx-gold);background:var(--lx-surface);"><i class="las la-file-download"></i></div>@endif
                        <div class="lx-card-actions">
                            <a href="{{ dynamicRoute($rp->slug) }}" class="lx-card-act-btn">
                                <i class="las la-eye"></i> {{ __('View') }}
                            </a>
                        </div>
                    </div>
                    <div class="lx-card-body">
                        <div class="lx-card-name">
                            <a href="{{ dynamicRoute($rp->slug) }}" style="color:inherit;text-decoration:none;">
                                {{ \Illuminate\Support\Str::words($rp->name, 6) }}
                            </a>
                        </div>
                        <div class="lx-price">{{ amount_with_currency_symbol($rprice) }}</div>
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
$(function(){
    $(document).on('click', '.lx-dp-tab-btn', function(){
        var target = $(this).data('target');
        $('.lx-dp-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.lx-dp-tab-panel').removeClass('active');
        $('#'+target).addClass('active');
    });

    $('#lx-dp-stars .lx-dp-star-icon').on('click', function(){
        var val = $(this).data('value');
        $('.rating-selected').attr('data-value', val);
        $('#lx-dp-stars .lx-dp-star-icon').each(function(i){
            $(this).css('color', i < val ? 'var(--lx-gold)' : 'var(--lx-border)');
        });
    });

    $('#review-submit-btn').on('click', function(e){
        e.preventDefault();
        var btn = $(this);
        $.ajax({
            url: '{{ theme_digital_product_review_url() }}', type: 'POST',
            data: { _token: '{{ csrf_token() }}', product_id: '{{ $product->id }}', review_text: $('#review-text').val(), rating: $('.rating-selected').data('value') },
            beforeSend: function(){ btn.text('{{ __("Submitting…") }}'); },
            success: function(data){
                if(data.type === 'success'){ toastr.success(data.msg); setTimeout(()=>location.reload(),300); }
                else { toastr.error(data.msg); }
                btn.text('{{ __("Submit") }}');
            },
            error: function(){ btn.text('{{ __("Submit") }}'); toastr.error('{{ __("An error occurred") }}'); }
        });
    });
});
</script>
@endsection
