{{-- Electro: Service Features --}}
<section class="el-services-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="el-services-grid">
            @foreach($features as $feature)
            <div class="el-service-item">
                <div class="el-service-icon-wrap">
                    <i class="{{ $feature['icon'] }} el-service-icon"></i>
                </div>
                <h4 class="el-service-title">{{ $feature['title'] }}</h4>
                <p class="el-service-desc">{{ $feature['description'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
