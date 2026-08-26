<style>
.hf-promo {
    background-color: #ffffff;
    position: relative;
    overflow: hidden;
}
.hf-promo .container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 15px;
}
.hf-promo-wrapper {
    display: flex;
    align-items: stretch;
    justify-content: space-between;
    gap: 20px;
    position: relative;
}
.hf-promo-img-left,
.hf-promo-img-right {
    flex: 1;
    background-color: #fcf4ee;
    display: flex;
    align-items: flex-end; /* Align images to bottom if they are models */
    justify-content: center;
    padding-top: 40px;
}
.hf-promo-img-left img,
.hf-promo-img-right img {
    max-width: 100%;
    height: auto;
    display: block;
}
.hf-promo-center {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    background-color: #ffffff;
    padding: 26px 20px;
    text-align: center;
    z-index: 2;
    width: 428px;
}
.hf-promo-subtitle {
    color: #ff7857;
    font-size: 15px;
    font-weight: 500;
    margin-bottom: 12px;
    display: block;
}
.hf-promo-title {
    font-size: 44px;
    font-weight: 700;
    color: #333333;
    line-height: 1.1;
    margin-bottom: 20px;
    font-family: 'Outfit', sans-serif;
}
.hf-promo-btn {
    display: inline-block;
    background-color: #ff7857;
    color: #fff;
    padding: 14px 38px;
    font-size: 15px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    text-decoration: none;
}
.hf-promo-btn:hover {
    background-color: #e66646;
    color: #fff;
}

@media (max-width: 991px) {
    .hf-promo-wrapper {
        flex-direction: column;
        gap: 0;
    }
    .hf-promo-img-left,
    .hf-promo-img-right {
        flex: 0 0 100%;
        width: 100%;
        padding-top: 20px;
    }
    .hf-promo-center {
        position: relative;
        left: auto;
        top: auto;
        transform: none;
        margin: -80px auto 40px;
        width: 90%;
        min-width: 0;
        padding: 26px 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05); /* Add shadow only on mobile so it pops out */
    }
}
</style>

<section class="hf-promo" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="hf-promo-wrapper">
            <div class="hf-promo-img-left">
                @if($left_img)
                    <img src="{{ $left_img }}" alt="Promo Left" loading="lazy">
                @else
                    <img src="https://via.placeholder.com/500x650/e6e6e6/a0a0a0?text=Left+Image" alt="Placeholder">
                @endif
            </div>

            <div class="hf-promo-center">
                <span class="hf-promo-subtitle">{{ $subtitle }}</span>
                <h2 class="hf-promo-title">{{ $title }}</h2>
                <a href="{{ $button_url }}" class="hf-promo-btn">{{ $button_text }}</a>
            </div>

            <div class="hf-promo-img-right">
                @if($right_img)
                    <img src="{{ $right_img }}" alt="Promo Right" loading="lazy">
                @else
                    <img src="https://via.placeholder.com/500x650/e6e6e6/a0a0a0?text=Right+Image" alt="Placeholder">
                @endif
            </div>
        </div>
    </div>
</section>
