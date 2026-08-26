{{-- Furnito: Featured Products --}}
<section class="fn-featured" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px; background-color:#fff ;">
    <div class="container">
        <div class="fn-section-head">
            <h2 class="fn-section-title">{{ $title }}</h2>
            @if($subtitle)<p class="fn-section-sub">{{ $subtitle }}</p>@endif
        </div>
        <div class="fn-product-grid">
            @forelse($products as $product)
                @include('theme-furnito::widgets.partials.product_card', ['product' => $product])
            @empty
                <p class="text-muted">{{ __('No products found.') }}</p>
            @endforelse
        </div>
    </div>
</section>
