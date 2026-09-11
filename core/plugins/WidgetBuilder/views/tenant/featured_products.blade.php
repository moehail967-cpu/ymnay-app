@php
    $col_class = match((int)$columns) {
        2 => 'col-6',
        3 => 'col-6 col-md-4',
        5 => 'col-6 col-md-4 col-lg',
        6 => 'col-6 col-md-4 col-lg-2',
        default => 'col-6 col-md-4 col-lg-3',
    };
@endphp

<section class="xgpb-featured-products" style="padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px;">
    <div class="container">
        @if(!empty($title))
            <div class="xgpb-section-header d-flex align-items-center justify-content-between mb-5">
                <div>
                    <h2 class="xgpb-section-title">{{ $title }}</h2>
                    @if(!empty($subtitle))<p class="xgpb-section-subtitle">{{ $subtitle }}</p>@endif
                </div>
                @if(!empty($view_all_text))
                    <a href="{{ $view_all_url }}" class="btn btn-outline-primary">{{ $view_all_text }}</a>
                @endif
            </div>
        @endif

        @if($products->isNotEmpty())
            <div class="row g-4">
                @foreach($products as $product)
                    @php
                        $img_data = get_attachment_image_by_id($product->image);
                        $img_url  = !empty($img_data) ? $img_data['img_url'] : global_asset('assets/common/img/placeholder.jpg');
                        $price    = $product->sale_price ?? 0;
                        $regular  = $product->regular_price ?? $price;
                    @endphp
                    <div class="{{ $col_class }}">
                        <div class="xgpb-product-card card h-100 border-0">
                            <a href="{{ route('tenant.products.single-quick-view', $product->slug) }}" class="xgpb-product-img-wrap d-block overflow-hidden">
                                <img src="{{ $img_url }}" alt="{{ $product->name }}" class="card-img-top w-100 xgpb-product-img" loading="lazy">
                            </a>
                            <div class="card-body p-3">
                                <a href="{{ route('tenant.products.single-quick-view', $product->slug) }}" class="xgpb-product-name d-block mb-2">
                                    {{ $product->name }}
                                </a>
                                <div class="xgpb-product-price d-flex align-items-center gap-2">
                                    <span class="xgpb-price-sale">{{ amount_with_currency_symbol($price) }}</span>
                                    @if($regular > $price)
                                        <span class="xgpb-price-regular text-decoration-line-through text-muted small">{{ amount_with_currency_symbol($regular) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-muted py-5">{{ __('No products found.') }}</p>
        @endif
    </div>
</section>

<style>
.xgpb-section-title { font-size: 1.75rem; font-weight: 700; margin-bottom: .25rem; }
.xgpb-section-subtitle { color: #6c757d; margin-bottom: 0; }
.xgpb-product-card { border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.07); transition: box-shadow .3s, transform .3s; }
.xgpb-product-card:hover { box-shadow: 0 8px 32px rgba(0,0,0,.14); transform: translateY(-4px); }
.xgpb-product-img-wrap { height: 220px; background: #f5f5f5; }
.xgpb-product-img { height: 100%; object-fit: cover; transition: transform .4s; }
.xgpb-product-card:hover .xgpb-product-img { transform: scale(1.05); }
.xgpb-product-name { font-size: .9rem; font-weight: 500; color: #1a1a2e; text-decoration: none; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.xgpb-price-sale { font-weight: 700; color: var(--main-color, #0d6efd); }
</style>
