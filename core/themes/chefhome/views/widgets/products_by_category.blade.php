@php $first_tab_id = $tabs->isNotEmpty() ? 'ch-tab-' . $tabs->first()['category']->id : null; @endphp

<section class="ch-products-by-cat-widget" style="background:var(--ch-warm); padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px;">
    <div class="container">
        <div class="ch-sec-heading">
            <div class="ch-sec-title">{{ $title }}</div>
            @if(!empty($view_all_url))
                <a href="{{ $view_all_url }}" class="ch-view-all">
                    {{ __('View All') }} <i class="las la-arrow-right"></i>
                </a>
            @endif
        </div>

        @if($tabs->isNotEmpty())
            <!-- Tab pills -->
            <div class="d-flex gap-2 flex-wrap mb-4" id="ch-tab-pills-{{ $widget_uid ?? uniqid() }}">
                @foreach($tabs as $tab)
                    @php $cat = $tab['category']; $tab_id = 'ch-tab-' . $cat->id; @endphp
                    <button class="ch-tab-pill {{ $loop->first ? 'active' : '' }}"
                            onclick="chSwitchTab(this, '{{ $tab_id }}')"
                            type="button">
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>

            <!-- Tab panels -->
            @foreach($tabs as $tab)
                @php $cat = $tab['category']; $products = $tab['products']; $tab_id = 'ch-tab-' . $cat->id; @endphp
                <div id="{{ $tab_id }}" class="ch-tab-panel {{ $loop->first ? 'active' : '' }}">
                    @if($products->isNotEmpty())
                        <div class="row g-3">
                            @foreach($products as $product)
                                @php
                                    $img_data = get_attachment_image_by_id($product->image_id ?? null);
                                    $img_url  = !empty($img_data) ? $img_data['img_url'] : global_asset('assets/common/img/placeholder.jpg');
                                    $price    = $product->sale_price ?? 0;
                                    $regular  = $product->regular_price ?? $price;
                                    $badge    = $product->badge ?? null;
                                    $prod_url = theme_product_url($product->slug);
                                @endphp
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="ch-card">
                                        <div class="ch-card-img">
                                            <a href="{{ $prod_url }}">
                                                <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                                            </a>
                                            @if($badge)
                                                <span class="ch-card-badge ch-badge-hot">{{ $badge->name }}</span>
                                            @endif
                                        </div>
                                        <div class="ch-card-body">
                                            <a href="{{ $prod_url }}" class="ch-card-title d-block text-decoration-none">{{ $product->name }}</a>
                                            <div class="ch-card-footer mt-2">
                                                <div>
                                                    <div class="ch-price">{{ amount_with_currency_symbol($price) }}</div>
                                                    @if($regular > $price)
                                                        <div class="ch-price-old">{{ amount_with_currency_symbol($regular) }}</div>
                                                    @endif
                                                </div>
                                                <button class="ch-add-btn add-to-cart-btn"
                                                        data-product_id="{{ $product->id }}"
                                                        title="{{ __('Add to cart') }}">
                                                    <i class="las la-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-muted py-4">{{ __('No products in this category.') }}</p>
                    @endif
                </div>
            @endforeach
        @else
            <p class="text-center text-muted py-5">{{ __('No categories found.') }}</p>
        @endif
    </div>
</section>

<script>
function chSwitchTab(btn, panelId) {
    var pills = btn.closest('.d-flex').querySelectorAll('.ch-tab-pill');
    pills.forEach(function(p){ p.classList.remove('active'); });
    btn.classList.add('active');

    document.querySelectorAll('.ch-tab-panel').forEach(function(el){ el.classList.remove('active'); });
    var panel = document.getElementById(panelId);
    if (panel) panel.classList.add('active');
}
</script>
