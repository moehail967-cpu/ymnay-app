{{-- Furnito: New Arrival — light split banner: left image, right text --}}
<section class="fn-new-arrival" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="fn-arrival-wrap">

        {{-- Left: product image --}}
        <div class="fn-arrival-media">
            @if($image)
                <img src="{{ $image }}" alt="{{ $title }}" class="fn-arrival-img" loading="lazy">
            @else
                <div class="fn-arrival-ph"><i class="las la-couch"></i></div>
            @endif
        </div>

        {{-- Right: content --}}
        <div class="fn-arrival-content">
            <h2 class="fn-arrival-title">{{ $title }}@if($year) {{ $year }}@endif</h2>
            @if($description)
            <p class="fn-arrival-desc">{{ $description }}</p>
            @endif
            <a href="{{ $button_url ?: '#' }}" class="fn-arrival-btn">{{ $button_text }}</a>
        </div>

    </div>
</section>

<style>
/* ── New Arrival Banner ── */
.fn-new-arrival {
    background: #EEF3F7;
}
.fn-arrival-wrap {
    display: flex;
    align-items: flex-end;
    min-height: 460px;
    max-width: 1320px;
    margin: 0 auto;
    padding: 0 40px;
}

/* Left: image — occupies 55%, bottom-aligned */
.fn-arrival-media {
    flex: 0 0 55%;
    max-width: 55%;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    padding: 48px 0 0;
    align-self: stretch;
}
.fn-arrival-img {
    display: block;
    max-height: 420px;
    width: auto;
    max-width: 100%;
    object-fit: contain;
    object-position: bottom center;
}
.fn-arrival-ph {
    width: 100%;
    min-height: 300px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    font-size: 7rem;
    color: #3D8870;
    opacity: .2;
    padding-bottom: 20px;
}

/* Right: text */
.fn-arrival-content {
    flex: 0 0 45%;
    max-width: 45%;
    padding: 60px 40px 60px 20px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.fn-arrival-title {
    font-size: clamp(2rem, 3.8vw, 3.2rem);
    font-weight: 300;
    color: #2c2c2c;
    line-height: 1.2;
    letter-spacing: -0.5px;
    margin: 0 0 20px;
}
.fn-arrival-desc {
    font-size: 14px;
    line-height: 1.75;
    color: #888;
    margin: 0 0 32px;
    max-width: 360px;
}
.fn-arrival-btn {
    display: inline-block;
    padding: 14px 32px;
    background: #3D8870;
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 0.3px;
    text-decoration: none;
    border-radius: 0;
    transition: background .2s;
    width: fit-content;
}
.fn-arrival-btn:hover {
    background: #2f7060;
    color: #fff;
    text-decoration: none;
}

/* Responsive */
@media (max-width: 992px) {
    .fn-arrival-wrap {
        flex-direction: column;
        align-items: center;
        padding: 40px 24px 0;
        min-height: auto;
    }
    .fn-arrival-media {
        flex: none;
        max-width: 100%;
        width: 100%;
        padding-top: 0;
        justify-content: center;
    }
    .fn-arrival-img { max-height: 320px; }
    .fn-arrival-content {
        flex: none;
        max-width: 100%;
        padding: 32px 0 40px;
        text-align: center;
        align-items: center;
    }
    .fn-arrival-desc { max-width: 100%; }
}
@media (max-width: 576px) {
    .fn-arrival-title { font-size: 2rem; }
    .fn-arrival-img   { max-height: 240px; }
}
</style>
