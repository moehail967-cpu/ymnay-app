{{-- Electro: Service Features --}}
<section class="hf-services-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="hf-services-grid">
            @foreach($features as $feature)
            <div class="hf-service-item">
                <div class="hf-service-icon-wrap">
                    <i class="{{ $feature['icon'] }} hf-service-icon"></i>
                </div>
                <h4 class="hf-service-title">{{ $feature['title'] }}</h4>
                <p class="hf-service-desc">{{ $feature['description'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
