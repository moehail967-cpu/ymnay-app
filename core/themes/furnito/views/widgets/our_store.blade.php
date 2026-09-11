{{-- Furnito: Our Store — tabbed product grid (Sale / Popular / New) --}}
@php $uid = 'fn-store-' . substr(md5(uniqid()), 0, 6); @endphp
<section class="fn-our-store" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px; background-color:#fff ;">
    <div class="container">
        <div class="fn-section-head">
            <h2 class="fn-section-title">{{ $title }}</h2>
            @if($subtitle)<p class="fn-section-sub">{{ $subtitle }}</p>@endif
        </div>

        {{-- Tab buttons --}}
        <div class="fn-store-tabs" id="{{ $uid }}-tabs">
            <button class="fn-store-tab fn-store-tab--active" data-target="{{ $uid }}-tab1">{{ $tab_1_label }}</button>
            <button class="fn-store-tab" data-target="{{ $uid }}-tab2">{{ $tab_2_label }}</button>
            <button class="fn-store-tab" data-target="{{ $uid }}-tab3">{{ $tab_3_label }}</button>
        </div>

        {{-- Tab panels --}}
        <div class="fn-store-panel fn-store-panel--active" id="{{ $uid }}-tab1">
            <div class="fn-product-grid">
                @forelse($tab_1_products as $product)
                    @include('theme-furnito::widgets.partials.product_card', ['product' => $product])
                @empty
                    <p class="text-muted py-3">{{ __('No products found.') }}</p>
                @endforelse
            </div>
        </div>
        <div class="fn-store-panel" id="{{ $uid }}-tab2">
            <div class="fn-product-grid">
                @forelse($tab_2_products as $product)
                    @include('theme-furnito::widgets.partials.product_card', ['product' => $product])
                @empty
                    <p class="text-muted py-3">{{ __('No products found.') }}</p>
                @endforelse
            </div>
        </div>
        <div class="fn-store-panel" id="{{ $uid }}-tab3">
            <div class="fn-product-grid">
                @forelse($tab_3_products as $product)
                    @include('theme-furnito::widgets.partials.product_card', ['product' => $product])
                @empty
                    <p class="text-muted py-3">{{ __('No products found.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</section>

<style>
.fn-store-tabs {
    display: flex;
    justify-content: center;
    gap: 0;
    margin-bottom: 40px;
    border-bottom: 2px solid #eee;
}
.fn-store-tab {
    background: none;
    border: none;
    padding: 12px 28px;
    font-size: 14px;
    font-weight: 600;
    color: #888;
    cursor: pointer;
    position: relative;
    transition: color 0.2s;
    letter-spacing: 0.3px;
}
.fn-store-tab::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: #3D8870;
    opacity: 0;
    transition: opacity 0.2s;
}
.fn-store-tab--active { color: #3D8870; }
.fn-store-tab--active::after { opacity: 1; }
.fn-store-tab:hover { color: #3D8870; }
.fn-store-panel { display: none; }
.fn-store-panel--active { display: block; }
</style>

<script>
(function() {
    var tabs = document.querySelectorAll('#{{ $uid }}-tabs .fn-store-tab');
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            tabs.forEach(function(t) { t.classList.remove('fn-store-tab--active'); });
            document.querySelectorAll('.fn-store-panel').forEach(function(p) { p.classList.remove('fn-store-panel--active'); });
            tab.classList.add('fn-store-tab--active');
            var target = document.getElementById(tab.dataset.target);
            if (target) target.classList.add('fn-store-panel--active');
        });
    });
})();
</script>
