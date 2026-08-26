<style>
    .hf-testimonials {
        background-color: #ffffff;
        overflow: hidden;
    }

    .hf-testimonials-wrapper {
        display: flex;
        align-items: center;
        gap: 50px;
    }

    .hf-testimonials-left {
        flex: 0 0 350px;
        padding-right: 20px;
    }

    .hf-testimonials-title {
        font-size: 32px;
        font-weight: 700;
        color: #333333;
        font-family: 'Outfit', sans-serif;
        margin-bottom: 20px;
    }

    .hf-testimonials-subtitle {
        font-size: 14px;
        color: #666666;
        line-height: 1.6;
        margin-bottom: 40px;
    }

    .hf-testimonials-arrows {
        display: flex;
        gap: 10px;
    }

    .hf-testimonials-arr {
        width: 44px;
        height: 44px;
        border: 1px solid #e0e0e0;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #333333;
        font-size: 16px;
        transition: all 0.3s;
    }

    .hf-testimonials-arr:hover {
        background: #ff7857;
        border-color: #ff7857;
        color: #ffffff;
    }

    .hf-testimonials-right {
        flex: 1;
        min-width: 0;
    }

    .hf-testimonials-slider {
        display: flex;
        gap: 30px;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -ms-overflow-style: none;
        scrollbar-width: none;
        padding-bottom: 10px;
    }

    .hf-testimonials-slider::-webkit-scrollbar {
        display: none;
    }

    .hf-review-card {
        min-width: 380px;
        max-width: 380px;
        scroll-snap-align: start;
        padding: 35px;
        position: relative;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }

    .hf-review-card:nth-child(odd) {
        background-color: #fff5ef;
    }

    .hf-review-card:nth-child(even) {
        background-color: #ffffff;
    }

    .hf-review-header {
        margin-bottom: 20px;
    }

    .hf-review-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        background: #e0e0e0;
    }

    .hf-review-text {
        font-size: 13px;
        line-height: 1.8;
        color: #666666;
        margin-bottom: 30px;
        flex: 1;
    }

    .hf-review-footer {
        display: flex;
        flex-direction: column;
        gap: 6px;
        position: relative;
        z-index: 2;
    }

    .hf-review-name {
        font-size: 14px;
        font-weight: 700;
        color: #333333;
    }

    .hf-review-stars {
        display: flex;
        gap: 2px;
    }

    .hf-star--filled {
        color: #f5a623;
        font-size: 13px;
    }

    .hf-star--empty {
        color: #cccccc;
        font-size: 13px;
    }

    .hf-review-quote {
        position: absolute;
        bottom: 25px;
        right: 35px;
        font-size: 60px;
        line-height: 0.7;
        color: #ff7857;
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
        letter-spacing: -2px;
        z-index: 1;
    }

    @media (max-width: 991px) {
        .hf-testimonials-wrapper {
            flex-direction: column;
            gap: 30px;
        }

        .hf-testimonials-left {
            flex: 0 0 auto;
            padding-right: 0;
            text-align: center;
        }

        .hf-testimonials-arrows {
            justify-content: center;
        }
    }

    @media (max-width: 576px) {
        .hf-review-card {
            min-width: 300px;
            max-width: 300px;
            padding: 25px;
        }
    }
</style>

{{-- HexFashion: Testimonials Slider --}}
@php $uid = 'hftest_' . substr(md5(uniqid()), 0, 8); @endphp
<section class="hf-testimonials" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="hf-testimonials-wrapper">

            {{-- Left Side: Text and Arrows --}}
            <div class="hf-testimonials-left">
                <h2 class="hf-testimonials-title">{{ $title }}</h2>
                <p class="hf-testimonials-subtitle">{{ $subtitle }}</p>
                <div class="hf-testimonials-arrows">
                    <button class="hf-testimonials-arr hf-test-prev" id="{{ $uid }}-prev"
                        aria-label="{{ __('Previous') }}">
                        <i class="las la-angle-left"></i>
                    </button>
                    <button class="hf-testimonials-arr hf-test-next" id="{{ $uid }}-next" aria-label="{{ __('Next') }}">
                        <i class="las la-angle-right"></i>
                    </button>
                </div>
            </div>

            {{-- Right Side: Slider --}}
            <div class="hf-testimonials-right">
                <div class="hf-testimonials-slider" id="{{ $uid }}">
                    @foreach($testimonials as $testimonial)
                        @if(!empty($testimonial->description))
                            <div class="hf-review-card">
                                <div class="hf-review-header">
                                    @if(!empty($testimonial->image))
                                        @php
                                            $img_details = get_attachment_image_by_id($testimonial->image);
                                        @endphp
                                        @if(!empty($img_details['img_url']))
                                            <img src="{{ $img_details['img_url'] }}" alt="{{ $testimonial->name }}"
                                                class="hf-review-avatar" loading="lazy">
                                        @else
                                            <div class="hf-review-avatar"
                                                style="display:flex;align-items:center;justify-content:center;background:#f0ebe5;color:#aaa;">
                                                <i class="las la-user"></i></div>
                                        @endif
                                    @else
                                        <div class="hf-review-avatar"
                                            style="display:flex;align-items:center;justify-content:center;background:#f0ebe5;color:#aaa;">
                                            <i class="las la-user"></i></div>
                                    @endif
                                </div>
                                <p class="hf-review-text">{{ $testimonial->description }}</p>

                                <div class="hf-review-footer">
                                    <span class="hf-review-name">{{ $testimonial->name }}</span>
                                    <div class="hf-review-stars">
                                        @php $rating = (int) $testimonial->rating ?: 5; @endphp
                                        @for($s = 1; $s <= 5; $s++)
                                            <i
                                                class="{{ $s <= $rating ? 'las la-star hf-star--filled' : 'lar la-star hf-star--empty' }}"></i>
                                        @endfor
                                    </div>
                                </div>

                                <div class="hf-review-quote">{{ $quote_text }}</div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>

<script>
    (function () {
        var wrap = document.getElementById('{{ $uid }}');
        var prev = document.getElementById('{{ $uid }}-prev');
        var next = document.getElementById('{{ $uid }}-next');
        if (!wrap) return;
        var scrollAmt = 410; // 380px card + 30px gap
        if (window.innerWidth <= 576) {
            scrollAmt = 330; // 300px card + 30px gap
        }
        if (prev) prev.addEventListener('click', function () { wrap.scrollBy({ left: -scrollAmt, behavior: 'smooth' }); });
        if (next) next.addEventListener('click', function () { wrap.scrollBy({ left: scrollAmt, behavior: 'smooth' }); });
    })();
</script>