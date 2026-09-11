@php
    $pt          = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb          = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url    = $data['view_all_url'] ?? route('tenant.shop');
    $placeholder = global_asset('assets/common/img/placeholder.jpg');
    $uid         = 'ch-tabs-' . uniqid();
    $firstTab    = $data['tabs']->first();
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:var(--ch-warm);">
    <div class="container">

        @if(!empty($data['title']))
            <div class="ch-sec-heading">
                <h2 class="ch-sec-title">{{ $data['title'] }}</h2>
                <a href="{{ $shop_url }}" class="ch-view-all">
                    {{ __('Browse All') }} <i class="mdi mdi-arrow-right"></i>
                </a>
            </div>
        @endif

        @if($data['tabs']->isNotEmpty())

            {{-- Tab pills --}}
            <div class="ch-tab-pills" id="{{ $uid }}-nav" style="margin-bottom:28px;">
                @foreach($data['tabs'] as $tabIndex => $tab)
                    <button class="ch-tab-pill {{ $tabIndex === 0 ? 'active' : '' }}"
                        onclick="chSwitchTab('{{ $uid }}', {{ $tabIndex }}, this)">
                        {{ $tab['category']->name }}
                    </button>
                @endforeach
            </div>

            {{-- Tab panels --}}
            @foreach($data['tabs'] as $tabIndex => $tab)
                <div id="{{ $uid }}-panel-{{ $tabIndex }}"
                     class="ch-tab-panel {{ $tabIndex !== 0 ? 'd-none' : '' }}">

                    @if($tab['products']->isNotEmpty())
                        <div class="row g-4">
                            @foreach($tab['products'] as $product)
                                @php
                                    $img         = get_attachment_image_by_id($product->image);
                                    $img_url     = !empty($img) ? $img['img_url'] : $placeholder;
                                    $sale_price  = $product->sale_price ?? 0;
                                    $reg_price   = $product->regular_price ?? $sale_price;
                                    $badge       = $product->badge ?? null;
                                    $product_url = route('tenant.products.single-quick-view', $product->slug);
                                @endphp
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="ch-card">
                                        <div class="ch-card-img">
                                            <a href="{{ $product_url }}">
                                                <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                                            </a>
                                            @if($badge)
                                                <span class="ch-card-badge
                                                    @if(strtolower($badge->name) === 'hot') ch-badge-hot
                                                    @elseif(strtolower($badge->name) === 'new') ch-badge-new
                                                    @else ch-badge-veg @endif">
                                                    {{ $badge->name }}
                                                </span>
                                            @endif
                                            <button class="ch-card-wish" title="{{ __('Wishlist') }}"
                                                onclick="addToWishlist({{ $product->id }}, this)">
                                                <i class="mdi mdi-heart-outline"></i>
                                            </button>
                                        </div>
                                        <div class="ch-card-body">
                                            <div class="ch-card-meta">
                                                @if($product->rating_count > 0)
                                                    <span style="color:var(--ch-amber);">
                                                        <i class="mdi mdi-star" style="font-size:12px;"></i>
                                                        {{ number_format($product->average_rating, 1) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <a href="{{ $product_url }}" class="ch-card-title" style="text-decoration:none; display:block;">
                                                {{ Str::limit($product->name, 40) }}
                                            </a>
                                            <div class="ch-card-footer">
                                                <div>
                                                    <span class="ch-price" style="font-size:15px;">{{ amount_with_currency_symbol($sale_price) }}</span>
                                                    @if($reg_price > $sale_price)
                                                        <span class="ch-price-old">{{ amount_with_currency_symbol($reg_price) }}</span>
                                                    @endif
                                                </div>
                                                <button class="ch-add-btn" title="{{ __('Add to Cart') }}"
                                                    onclick="addToCart({{ $product->id }})">
                                                    <i class="mdi mdi-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p style="color:var(--ch-muted); text-align:center; padding:40px 0;">
                            {{ __('No dishes in this category yet.') }}
                        </p>
                    @endif

                </div>{{-- /panel --}}
            @endforeach

        @endif

    </div>
</section>

<script>
function chSwitchTab(uid, idx, btn) {
    document.querySelectorAll('#' + uid + '-nav .ch-tab-pill').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('[id^="' + uid + '-panel-"]').forEach(p => p.classList.add('d-none'));
    document.getElementById(uid + '-panel-' + idx).classList.remove('d-none');
}
</script>
