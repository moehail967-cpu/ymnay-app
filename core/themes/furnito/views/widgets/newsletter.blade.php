{{-- Furnito: Newsletter --}}
<section class="fn-newsletter" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;background:#2c2c2c;">
    <div class="container">
        <div class="fn-newsletter-inner">
            <div class="fn-newsletter-text">
                <h2 class="fn-newsletter-title">{{ $title }}</h2>
                <p class="fn-newsletter-sub">{{ $subtitle }}</p>
            </div>
            <form class="fn-newsletter-form" onsubmit="return false;">
                <input type="email" class="fn-newsletter-input" placeholder="{{ $placeholder }}" required>
                <button type="submit" class="fn-newsletter-btn">{{ $button_text }}</button>
            </form>
        </div>
    </div>
</section>
<style>
.fn-newsletter { }
.fn-newsletter-inner { display:flex; align-items:center; justify-content:space-between; gap:40px; }
.fn-newsletter-title { font-size:clamp(1.4rem,3vw,2rem); font-weight:700; color:#fff; margin-bottom:8px; }
.fn-newsletter-sub { font-size:14px; color:#aaa; }
.fn-newsletter-form { display:flex; gap:0; flex-shrink:0; min-width:380px; }
.fn-newsletter-input { flex:1; padding:14px 20px; border:none; background:#fff; color:#1a1a1a; font-size:14px; outline:none; }
.fn-newsletter-btn { padding:14px 28px; background:#3D8870; color:#fff; border:none; font-size:13px; font-weight:700; letter-spacing:1px; text-transform:uppercase; cursor:pointer; transition:background .2s; white-space:nowrap; }
.fn-newsletter-btn:hover { background:#2f7060; }
@media(max-width:768px) { .fn-newsletter-inner { flex-direction:column; text-align:center; } .fn-newsletter-form { min-width:unset; width:100%; } }
</style>
