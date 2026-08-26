@php
$features = [
    ['icon' => $icon1, 'title' => $title1, 'desc' => $desc1],
    ['icon' => $icon2, 'title' => $title2, 'desc' => $desc2],
    ['icon' => $icon3, 'title' => $title3, 'desc' => $desc3],
    ['icon' => $icon4, 'title' => $title4, 'desc' => $desc4],
];
@endphp
<section class="bp-features-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="bp-features-grid">
            @foreach($features as $i => $feat)
            <div class="bp-feature-item{{ $i < 3 ? ' bp-feature-divider' : '' }}">
                <i class="{{ $feat['icon'] }} bp-feature-icon"></i>
                <div class="bp-feature-title">{{ $feat['title'] }}</div>
                <div class="bp-feature-desc">{{ $feat['desc'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>
