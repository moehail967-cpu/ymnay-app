<style>
.hf-hero {
    background-color: #fff5ef;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    min-height: 800px;
}
.hf-hero .container {
    position: relative;
    z-index: 2;
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    width: 100%;
}
.hf-hero-left {
    flex: 0 0 50%;
    max-width: 50%;
    padding-right: 30px;
}
.hf-hero-title {
    font-size: 64px;
    font-weight: 700;
    color: #2a2a2a;
    line-height: 1.2;
    margin-bottom: 20px;
    font-family: 'Outfit', sans-serif;
}
.hf-hero-title .text-orange {
    color: #ff7857;
}
.hf-hero-desc {
    font-size: 16px;
    color: #666;
    line-height: 1.6;
    margin-bottom: 40px;
    max-width: 80%;
}
.hf-hero-btn {
    display: inline-block;
    background-color: #ff7857;
    color: #fff;
    padding: 15px 40px;
    font-size: 16px;
    font-weight: 500;
    text-transform: uppercase;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}
.hf-hero-btn:hover {
    background-color: #e66646;
    color: #fff;
}
.hf-hero-right {
    flex: 0 0 50%;
    max-width: 50%;
    position: relative;
    display: flex;
    justify-content: center;
}
.hf-hero-circle {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 550px;
    height: 550px;
    background-color: #ffe8dd;
    border-radius: 50%;
    z-index: -1;
    overflow: hidden;
}
.hf-hero-circle::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background-image: repeating-linear-gradient(transparent, transparent 40px, rgba(255,120,87,0.1) 40px, rgba(255,120,87,0.1) 41px);
    border-radius: 50%;
}
.hf-hero-model {
    max-width: 100%;
    height: auto;
    position: relative;
    z-index: 2;
    margin-top: 50px;
}
.hf-hero-bg-text {
    position: absolute;
    right: -100px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 250px;
    font-weight: 800;
    color: transparent;
    -webkit-text-stroke: 2px rgba(255,120,87,0.3);
    z-index: 1;
    pointer-events: none;
    font-family: 'Outfit', sans-serif;
    letter-spacing: 5px;
}
.hf-hero-social {
    position: absolute;
    left: 30px;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    flex-direction: column;
    gap: 30px;
    z-index: 10;
}
.hf-hero-social a {
    color: #888;
    font-size: 14px;
    text-transform: uppercase;
    text-decoration: none;
    transform: rotate(-90deg);
    transition: color 0.3s;
    letter-spacing: 2px;
}
.hf-hero-social a:hover {
    color: #ff7857;
}

@media (max-width: 991px) {
    .hf-hero {
        flex-direction: column;
        text-align: center;
        padding: 80px 0;
    }
    .hf-hero .container {
        flex-direction: column;
    }
    .hf-hero-left, .hf-hero-right {
        flex: 0 0 100%;
        max-width: 100%;
        padding-right: 0;
    }
    .hf-hero-desc {
        max-width: 100%;
        margin: 0 auto 30px;
    }
    .hf-hero-social {
        display: none;
    }
    .hf-hero-bg-text {
        font-size: 150px;
        right: 50%;
        transform: translate(50%, -50%);
    }
}
</style>

<section class="hf-hero" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="hf-hero-social">
        <a href="#">Fb.</a>
        <a href="#">Tw.</a>
        <a href="#">Insta.</a>
        <a href="#">YT.</a>
    </div>

    <div class="container">
        <div class="hf-hero-left">
            <h1 class="hf-hero-title">{!! $title !!}</h1>
            <p class="hf-hero-desc">{{ $description }}</p>
            <a href="{{ $button_url }}" class="hf-hero-btn">{{ $button_text }}</a>
        </div>

        <div class="hf-hero-right">
            <div class="hf-hero-bg-text">{{ $bg_text }}</div>
            <div class="hf-hero-circle"></div>
            
            @if($hero_img)
                <img src="{{ $hero_img }}" alt="Hero Model" class="hf-hero-model" loading="eager">
            @endif
        </div>
    </div>
</section>
