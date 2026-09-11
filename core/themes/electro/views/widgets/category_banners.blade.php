{{-- Electro: Category Banners --}}
<section class="el-catbanner-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="el-catbanner-row">
            @foreach($banners as $banner)
            <a href="{{ $banner['link_url'] }}" class="el-catbanner-card">
                <div class="el-catbanner-img-side">
                    @if($banner['image'])
                    <img src="{{ $banner['image'] }}" alt="{{ $banner['name'] }}" class="el-catbanner-img" loading="lazy">
                    @else
                    <div class="el-catbanner-img-ph"><i class="las la-laptop"></i></div>
                    @endif
                </div>
                <div class="el-catbanner-text-side">
                    <h3 class="el-catbanner-name">{{ $banner['name'] }}</h3>
                    <span class="el-catbanner-link">{{ __('ShopNow') }}</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
