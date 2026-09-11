{{-- Medicom: Service Features --}}
<section class="mc-features-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="mc-features">
            @foreach($features as $feature)
            <div class="mc-feature-item">
                <div class="mc-feature-icon"><i class="{{ $feature['icon'] }}"></i></div>
                <div class="mc-feature-text">
                    <div class="mc-feature-title">{{ $feature['title'] }}</div>
                    <div class="mc-feature-desc">{{ $feature['desc'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<style>
.mc-features { display:grid; grid-template-columns:repeat(4,1fr); gap:32px; }
.mc-feature-item { display:flex; align-items:flex-start; gap:16px; padding:20px; background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,.05); }
.mc-feature-icon { flex-shrink:0; width:48px; height:48px; background:#dbeeff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:22px; color:#1A85ED; }
.mc-feature-title { font-size:14px; font-weight:700; color:#1a1a1a; margin-bottom:4px; }
.mc-feature-desc { font-size:13px; color:#888; }
@media(max-width:768px) { .mc-features { grid-template-columns:repeat(2,1fr); } }
@media(max-width:480px) { .mc-features { grid-template-columns:1fr; } }
</style>
