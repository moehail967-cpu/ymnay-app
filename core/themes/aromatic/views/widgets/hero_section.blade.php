<section class="ar-hero-widget" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="ar-hero-inner">
            {{-- Image: LEFT (matches design) --}}
            <div class="ar-hero-media">
                <div class="ar-hero-img-wrap">
                    <div class="ar-hero-circle"></div>
                    @if(!empty($image_url))
                        <img src="{{ $image_url }}" alt="{{ strip_tags($title) }}" class="ar-hero-img">
                    @else
                        <div class="ar-hero-placeholder">
                            <i class="las la-flask"></i>
                        </div>
                    @endif
                </div>
            </div>
            {{-- Content: RIGHT (matches design) --}}
            <div class="ar-hero-content">
                @if(!empty($section_tag))
                    <span class="ar-section-tag">{{ $section_tag }}</span>
                @endif
                <h1 class="ar-hero-title">{!! $title !!}</h1>
                @if(!empty($subtitle))
                    <p class="ar-hero-sub">{{ $subtitle }}</p>
                @endif
                <div class="ar-hero-actions">
                    @if(!empty($button_text) && !empty($button_url))
                        <a href="{{ $button_url }}" class="ar-btn ar-btn-red">{{ $button_text }}</a>
                    @endif
                    @if(!empty($button2_text) && !empty($button2_url))
                        <a href="{{ $button2_url }}" class="ar-btn ar-btn-outline">{{ $button2_text }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
