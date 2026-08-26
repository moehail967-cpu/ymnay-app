{{-- Furnito: Testimonials — 3-column customer review cards --}}
<section class="fn-testimonials" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px; background-color:#fff ;">
    <div class="container">
        <div class="fn-testimonials-grid">
            @foreach($reviews as $i => $review)
            <div class="fn-review-card">
                <div class="fn-review-quote">&ldquo;</div>
                <p class="fn-review-text">{{ $review['text'] }}</p>
                <div class="fn-review-footer">
                    <div class="fn-review-stars">
                        @for($s = 1; $s <= 5; $s++)
                        <i class="{{ $s <= $review['stars'] ? 'las la-star fn-star--filled' : 'lar la-star fn-star--empty' }}"></i>
                        @endfor
                    </div>
                    <span class="fn-review-name">{{ $review['name'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<style>
.fn-testimonials {
    background: #EEF3F7;
}
.fn-testimonials-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}
.fn-review-card {
    background: #fff;
    padding: 40px 36px 32px;
    position: relative;
}
.fn-review-quote {
    font-size: 80px;
    line-height: 0.7;
    color: #3D8870;
    font-family: Georgia, serif;
    margin-bottom: 24px;
    opacity: 0.3;
    font-weight: 700;
}
.fn-review-text {
    font-size: 14px;
    line-height: 1.75;
    color: #555;
    margin: 0 0 28px;
    flex: 1;
}
.fn-review-footer {
    border-top: 1px solid #f0ebe3;
    padding-top: 20px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.fn-review-stars { display: flex; gap: 3px; }
.fn-star--filled { color: #f5a623; font-size: 14px; }
.fn-star--empty  { color: #ddd; font-size: 14px; }
.fn-review-name {
    font-size: 13px;
    font-weight: 700;
    color: #1a1a1a;
    text-transform: uppercase;
    letter-spacing: 1px;
}
@media (max-width: 992px) { .fn-testimonials-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 576px) { .fn-testimonials-grid { grid-template-columns: 1fr; } }
</style>
