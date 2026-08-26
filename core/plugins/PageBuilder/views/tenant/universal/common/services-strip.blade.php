@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 60;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 60;
    $items = $data['repeater_data']['repeater_title_'] ?? [];
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:#fff; border-top:1px solid #f0f0f0; border-bottom:1px solid #f0f0f0;">
    <div class="container">
        @if(!empty($data['title']))
            <div class="text-center mb-5">
                <h2 style="font-size:2rem; font-weight:700; color:#1a1a2e; margin-bottom:.5rem;">{{ $data['title'] }}</h2>
                <div style="width:56px; height:4px; background:var(--sectionC,#0d6efd); margin:0 auto; border-radius:2px;"></div>
            </div>
        @endif

        @if(!empty($items))
            <div class="row g-4">
                @foreach($items as $key => $title)
                    @php
                        $icon = $data['repeater_data']['repeater_icon_'][$key] ?? 'ti tabler-star';
                        $desc = $data['repeater_data']['repeater_description_'][$key] ?? '';
                    @endphp
                    <div class="col-6 col-md-3">
                        <div class="text-center p-4" style="border-radius:12px; transition:background .2s;">
                            <div class="mb-3" style="width:56px; height:56px; border-radius:50%; background:rgba(var(--sectionC-rgb,13,110,253),.1); display:flex; align-items:center; justify-content:center; margin:0 auto;">
                                <i class="{{ $icon }}" style="font-size:1.5rem; color:var(--sectionC,#0d6efd);"></i>
                            </div>
                            <h5 style="font-weight:600; font-size:1rem; color:#1a1a2e; margin-bottom:.5rem;">
                                {{ \App\Helpers\SanitizeInput::esc_html($title) }}
                            </h5>
                            @if(!empty($desc))
                                <p style="font-size:.875rem; color:#6c757d; margin:0;">
                                    {{ \App\Helpers\SanitizeInput::esc_html($desc) }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
