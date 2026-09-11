{{-- Furnito: Service Features --}}
<section class="fn-features-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;background:#fff;">
    <div class="container">
        <div class="fn-features">
            @foreach($features as $feature)
            <div class="fn-feature-item">
                <div class="fn-feature-icon"><i class="{{ $feature['icon'] }}"></i></div>
                <div class="fn-feature-text">
                    <div class="fn-feature-title">{{ $feature['title'] }}</div>
                    <div class="fn-feature-desc">{{ $feature['desc'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<style>
.fn-features { display:grid; grid-template-columns:repeat(4,1fr); gap:32px; }
.fn-feature-item { display:flex; align-items:flex-start; gap:16px; }
.fn-feature-icon { flex-shrink:0; width:48px; height:48px; background:#fff; border-radius:4px; display:flex; align-items:center; justify-content:center; font-size:22px; color:#3D8870; box-shadow:0 2px 8px rgba(0,0,0,.08); }
.fn-feature-title { font-size:14px; font-weight:700; color:#1a1a1a; margin-bottom:4px; }
.fn-feature-desc { font-size:13px; color:#888; }
@media(max-width:768px) { .fn-features { grid-template-columns:repeat(2,1fr); } }
@media(max-width:480px) { .fn-features { grid-template-columns:1fr; } }
</style>
