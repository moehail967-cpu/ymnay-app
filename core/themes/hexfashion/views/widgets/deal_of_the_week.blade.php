<style>
.hf-deal {
    background-color: #fcf4ee;
    position: relative;
    overflow: hidden;
}
.hf-deal-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-height: 450px;
}
.hf-deal-content {
    flex: 0 0 50%;
    padding: 60px 40px 60px 0;
}
.hf-deal-title {
    font-size: 46px;
    font-weight: 700;
    color: #333333;
    margin-bottom: 15px;
    font-family: 'Outfit', sans-serif;
    line-height: 1.2;
}
.hf-deal-subtitle {
    font-size: 15px;
    color: #666666;
    line-height: 1.6;
    margin-bottom: 35px;
    max-width: 450px;
}
.hf-deal-btn {
    display: inline-block;
    background-color: #ff7857;
    color: #ffffff;
    font-size: 15px;
    font-weight: 500;
    padding: 14px 38px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}
.hf-deal-btn:hover {
    background-color: #e66646;
    color: #ffffff;
}
.hf-deal-image {
    flex: 0 0 50%;
    text-align: right;
    display: flex;
    justify-content: flex-end;
    align-items: flex-end;
    height: 100%;
    align-self: flex-end;
}
.hf-deal-image img {
    max-width: 100%;
    max-height: 550px;
    object-fit: contain;
    object-position: bottom right;
    display: block;
}

@media (max-width: 991px) {
    .hf-deal-wrapper {
        flex-direction: column;
        text-align: center;
    }
    .hf-deal-content {
        flex: 0 0 100%;
        padding: 60px 20px 30px;
    }
    .hf-deal-subtitle {
        margin-left: auto;
        margin-right: auto;
    }
    .hf-deal-image {
        flex: 0 0 100%;
        width: 100%;
        justify-content: center;
    }
    .hf-deal-image img {
        object-position: bottom center;
    }
}
</style>

{{-- HexFashion: Deal of the Week --}}
<section class="hf-deal" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="hf-deal-wrapper">
            
            {{-- Left: text content --}}
            <div class="hf-deal-content">
                <h2 class="hf-deal-title">{{ $title }}</h2>
                <p class="hf-deal-subtitle">{{ $subtitle }}</p>

                <a href="{{ $link_url }}" class="hf-deal-btn">
                    {{ $link_text }}
                </a>
            </div>

            {{-- Right: banner image --}}
            <div class="hf-deal-image">
                @if($banner_url)
                    <img src="{{ $banner_url }}" alt="{{ $title }}" loading="lazy">
                @else
                    <img src="https://via.placeholder.com/600x550/e6e6e6/a0a0a0?text=Right+Image" alt="Placeholder">
                @endif
            </div>

        </div>
    </div>
</section>
