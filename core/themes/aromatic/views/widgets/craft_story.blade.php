<section class="ar-craft-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="ar-section-head">
            <span class="ar-section-tag">{{ __('رحلة الصنعة') }}</span>
            <h2 class="ar-section-title">{{ $title }}</h2>
            @if($subtitle)<p class="ar-section-subtitle">{{ $subtitle }}</p>@endif
        </div>
        <div class="ar-craft-grid">
            @foreach($items as $item)
                <article class="ar-craft-card">
                    @if($item['image'])<img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" loading="lazy">@endif
                    <div class="ar-craft-copy">
                        <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $item['title'] }}</h3>
                        <p>{{ $item['description'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
