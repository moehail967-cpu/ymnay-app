{{-- HexFashion: Newsletter --}}
<section class="hf-newsletter" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;background:#111;">
    <div class="container">
        <div class="hf-newsletter-inner">
            <h2 class="hf-newsletter-title">{{ $title }}</h2>
            <p class="hf-newsletter-sub">{{ $subtitle }}</p>
            <form class="hf-newsletter-form" onsubmit="return false;">
                <input type="email" class="hf-newsletter-input" placeholder="{{ $placeholder }}" required>
                <button type="submit" class="hf-newsletter-btn">{{ $button_text }}</button>
            </form>
        </div>
    </div>
</section>
<style>
.hf-newsletter { }
.hf-newsletter-inner { text-align:center; max-width:560px; margin:0 auto; }
.hf-newsletter-title { font-size:clamp(1.6rem,3vw,2.4rem); font-weight:800; color:#fff; margin-bottom:12px; }
.hf-newsletter-sub { font-size:14px; color:#999; margin-bottom:32px; }
.hf-newsletter-form { display:flex; gap:0; }
.hf-newsletter-input { flex:1; padding:15px 20px; border:1px solid #333; border-right:none; background:#1a1a1a; color:#fff; font-size:14px; outline:none; }
.hf-newsletter-input::placeholder { color:#666; }
.hf-newsletter-btn { padding:15px 32px; background:#fff; color:#111; border:none; font-size:12px; font-weight:800; letter-spacing:2px; text-transform:uppercase; cursor:pointer; transition:background .2s,color .2s; white-space:nowrap; }
.hf-newsletter-btn:hover { background:#ccc; }
</style>
