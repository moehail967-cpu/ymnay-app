@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:#fff;">
    <div class="container">
        @if(!empty($data['title']))
            <div class="text-center mb-5">
                <h2 style="font-size:2rem; font-weight:700; color:#1a1a2e; margin-bottom:.5rem;">{{ $data['title'] }}</h2>
                <div style="width:56px; height:4px; background:var(--sectionC,#0d6efd); margin:0 auto; border-radius:2px;"></div>
            </div>
        @endif

        @if($data['products']->isNotEmpty())
            <div class="row g-4">
                @foreach($data['products'] as $product)
                    @php
                        $img = get_attachment_image_by_id($product->image);
                        $img_url = !empty($img) ? $img['img_url'] : global_asset('assets/common/img/placeholder.jpg');
                        $sale_price = $product->sale_price ?? 0;
                        $regular_price = $product->regular_price ?? $sale_price;
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card h-100 border-0" style="border-radius:12px; box-shadow:0 2px 16px rgba(0,0,0,.08); overflow:hidden; transition:box-shadow .3s;">
                            <a href="{{ route('tenant.products.single-quick-view', $product->slug) }}" class="d-block overflow-hidden" style="height:200px;">
                                <img src="{{ $img_url }}" alt="{{ $product->name }}"
                                     class="card-img-top w-100 h-100"
                                     style="object-fit:cover; transition:transform .3s;"
                                     onmouseover="this.style.transform='scale(1.05)'"
                                     onmouseout="this.style.transform='scale(1)'">
                            </a>
                            <div class="card-body p-3">
                                <a href="{{ route('tenant.products.single-quick-view', $product->slug) }}"
                                   style="text-decoration:none; color:#1a1a2e; font-size:.9rem; font-weight:500; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; margin-bottom:.5rem; display:block;">
                                    {{ $product->name }}
                                </a>
                                <div class="d-flex align-items-center gap-2">
                                    <span style="font-weight:700; color:var(--sectionC,#0d6efd);">
                                        {{ amount_with_currency_symbol($sale_price) }}
                                    </span>
                                    @if($regular_price > $sale_price)
                                        <span style="font-size:.8rem; color:#adb5bd; text-decoration:line-through;">
                                            {{ amount_with_currency_symbol($regular_price) }}
                                        </span>
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
