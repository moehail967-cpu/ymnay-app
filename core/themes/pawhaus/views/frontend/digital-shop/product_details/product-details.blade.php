@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $product->name !!} @endsection
@section('page-title') {!! $product->name !!} @endsection
@section('meta-data') {!! render_page_meta_data($product) !!} @endsection

@section('content')
@php
    $data       = theme_product_price($product);
    $sale_price = $data['sale_price'];
    $reg_price  = $data['regular_price'];
    $discount   = $data['discount'];
    $img        = get_attachment_image_by_id($product->image_id ?? null, 'large');
    $img_url    = $img['img_url'] ?? null;
    $af         = $product->additionalFields ?? null;
@endphp

<div class="container" style="padding:20px 0 0;">
    <div class="ph-breadcrumb">
        <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
        <span class="sep">/</span>
        <a href="{{ theme_digital_shop_url() }}" style="color:var(--ph-terra);">{{ __('Digital Shop') }}</a>
        <span class="sep">/</span>
        <span class="current">{{ $product->name }}</span>
    </div>
</div>

<div class="container" style="padding-top:28px;padding-bottom:72px;">
    <div class="row g-5">

        {{-- Cover Image --}}
        <div class="col-lg-5">
            <div style="border-radius:var(--ph-radius);overflow:hidden;background:var(--ph-terra-light);border:2px solid var(--ph-border);aspect-ratio:1;display:flex;align-items:center;justify-content:center;">
                @if($img_url)
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <i class="las la-file-download" style="font-size:96px;color:var(--ph-terra);"></i>
                @endif
            </div>
        </div>

        {{-- Info --}}
        <div class="col-lg-7">
            <h1 style="font-size:clamp(20px,3vw,28px);font-weight:800;color:var(--ph-dark);margin-bottom:12px;">{{ $product->name }}</h1>

            <div class="d-flex align-items:center gap-3 mb-3">{!! theme_star_rating($product) !!}</div>

            <div class="mb-4">
                <span style="font-size:30px;font-weight:800;color:var(--ph-terra);">{{ amount_with_currency_symbol($sale_price) }}</span>
                @if($reg_price)
                    <span style="font-size:16px;color:var(--ph-muted);text-decoration:line-through;margin-left:10px;">{{ amount_with_currency_symbol($reg_price) }}</span>
                @endif
                @if($discount)
                    <span style="margin-left:10px;font-size:13px;background:var(--ph-sage-light);color:var(--ph-sage);padding:3px 10px;border-radius:20px;font-weight:700;">{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            {{-- Specs --}}
            @if($af)
            <div style="background:var(--ph-cream);border:2px solid var(--ph-border);border-radius:var(--ph-radius);padding:16px;margin-bottom:20px;">
                <div class="row g-2" style="font-size:13px;">
                    @if($af->file_size ?? false)
                    <div class="col-6 d-flex gap-2 align-items-center">
                        <i class="las la-hdd" style="color:var(--ph-terra);font-size:16px;"></i>
                        <span style="color:var(--ph-muted);">{{ __('File Size') }}:</span>
                        <strong>{{ $af->file_size }}</strong>
                    </div>
                    @endif
                    @if($af->getLanguage ?? false)
                    <div class="col-6 d-flex gap-2 align-items-center">
                        <i class="las la-language" style="color:var(--ph-terra);font-size:16px;"></i>
                        <span style="color:var(--ph-muted);">{{ __('Language') }}:</span>
                        <strong>{{ $af->getLanguage?->name ?? $af->getLanguage }}</strong>
                    </div>
                    @endif
                    @if($af->software_version ?? false)
                    <div class="col-6 d-flex gap-2 align-items-center">
                        <i class="las la-code-branch" style="color:var(--ph-terra);font-size:16px;"></i>
                        <span style="color:var(--ph-muted);">{{ __('Version') }}:</span>
                        <strong>{{ $af->software_version }}</strong>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="d-flex gap-3 flex-wrap">
                @auth('web')
                <button type="button" id="ph-dp-buy-btn"
                        class="ph-btn ph-btn-terra ph-dp-buy-now"
                        data-product_id="{{ $product->id }}"
                        style="flex:1;justify-content:center;max-width:240px;">
                    <i class="las la-download"></i> {{ amount_with_currency_symbol($sale_price) }} — {{ __('Buy Now') }}
                </button>
                @else
                <a href="{{ theme_login_url() }}" class="ph-btn ph-btn-terra" style="flex:1;justify-content:center;max-width:240px;">
                    <i class="las la-sign-in-alt"></i> {{ __('Sign In to Purchase') }}
                </a>
                @endauth
                <button class="add-to-wishlist-btn ph-btn ph-btn-outline" data-product_id="{{ $product->id }}">
                    <i class="las la-heart"></i> {{ __('Wishlist') }}
                </button>
            </div>

            <div class="d-flex gap-3 flex-wrap mt-3" style="font-size:12px;color:var(--ph-muted);">
                <span><i class="las la-download" style="color:var(--ph-sage);"></i> {{ __('Instant Download') }}</span>
                <span><i class="las la-infinity" style="color:var(--ph-terra);"></i> {{ __('Lifetime Access') }}</span>
                <span><i class="las la-shield-alt" style="color:var(--ph-terra);"></i> {{ __('Secure Payment') }}</span>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="margin-top:56px;">
        <div class="ph-tab-nav">
            <button class="ph-tab-nav-btn active" data-target="ph-dp-desc">{{ __('Description') }}</button>
            <button class="ph-tab-nav-btn" data-target="ph-dp-reviews">{{ __('Reviews') }}</button>
        </div>
        <div id="ph-dp-desc" class="ph-tab-panel active">
            <div style="font-size:15px;line-height:1.8;color:var(--ph-dark);">
                {!! $product->description ?? '<p style="color:var(--ph-muted);">'.__('No description available.').'</p>' !!}
            </div>
        </div>
        <div id="ph-dp-reviews" class="ph-tab-panel">
            @include(include_theme_path('shop.product_details.partials.product-reviews'))
        </div>
    </div>

    {{-- Related --}}
    @if($related_products->isNotEmpty())
    <div style="margin-top:56px;">
        <div class="ph-sec-heading">
            <div class="ph-section-title">{{ __('You May Also Like') }}</div>
        </div>
        <div class="row g-3 mt-2">
            @foreach($related_products as $rp)
                @php $rpd = theme_product_price($rp); $rpi = get_attachment_image_by_id($rp->image_id ?? null, 'grid'); @endphp
                <div class="col-6 col-md-3">
                    <div class="ph-card">
                        <div class="ph-card-img">
                            @if($rpi['img_url'] ?? false)<a href="{{ theme_product_url($rp->slug) }}"><img src="{{ $rpi['img_url'] }}" alt="{{ $rp->name }}" loading="lazy"></a>
                            @else<a href="{{ theme_product_url($rp->slug) }}" style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;"><i class="las la-file-download" style="font-size:40px;color:var(--ph-terra);"></i></a>@endif
                        </div>
                        <div class="ph-card-body">
                            <div class="ph-card-name"><a href="{{ theme_product_url($rp->slug) }}">{{ \Illuminate\Support\Str::words($rp->name, 6) }}</a></div>
                            <div class="ph-card-price"><span class="ph-price-sale">{{ amount_with_currency_symbol($rpd['sale_price']) }}</span></div>
                            <a href="{{ theme_product_url($rp->slug) }}" class="ph-card-atc"><i class="las la-eye"></i> {{ __('View') }}</a>
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
    $(document).on('click', '.ph-tab-nav-btn', function(){
        var target = $(this).data('target');
        $('.ph-tab-nav-btn').removeClass('active');
        $(this).addClass('active');
        $('.ph-tab-panel').removeClass('active');
        $('#' + target).addClass('active');
    });

    // Redirect to checkout after digital buy-now cart add
    $(document).on('click', '.ph-dp-buy-now', function(){
        var btn = $(this);
        var productId = btn.data('product_id');
        btn.prop('disabled', true).html('<i class="las la-spinner la-spin"></i> {{ __("Processing…") }}');
        $.ajax({
            url: '{{ theme_digital_product_add_to_cart_url() }}',
            type: 'POST',
            data: { product_id: productId, _token: '{{ csrf_token() }}' },
            success: function(data) {
                if (data.quantity_msg || data.error_msg) {
                    toastr.warning(data.quantity_msg || data.error_msg);
                    btn.prop('disabled', false).html('<i class="las la-download"></i> {{ amount_with_currency_symbol($sale_price) }} — {{ __("Buy Now") }}');
                } else {
                    window.location.href = '{{ theme_checkout_url() }}';
                }
            },
            error: function() {
                toastr.error('{{ __("An error occurred") }}');
                btn.prop('disabled', false).html('<i class="las la-download"></i> {{ amount_with_currency_symbol($sale_price) }} — {{ __("Buy Now") }}');
            }
        });
    });
});
</script>
@endsection
