{{-- Electro: Category Banners --}}
<section class="hf-catbanner-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="hf-catbanner-row">
            @foreach($banners as $banner)
            <a href="{{ $banner['link_url'] }}" class="hf-catbanner-card">
                <div class="hf-catbanner-img-side">
                    @if($banner['image'])
                    <img src="{{ $banner['image'] }}" alt="{{ $banner['name'] }}" class="hf-catbanner-img" loading="lazy">
                    @else
                    <div class="hf-catbanner-img-ph"><i class="las la-laptop"></i></div>
                    @endif
                </div>
                <div class="hf-catbanner-text-side">
                    <h3 class="hf-catbanner-name">{{ $banner['name'] }}</h3>
                    <span class="hf-catbanner-link">{{ __('ShopNow') }}</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
