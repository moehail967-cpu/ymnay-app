@php
    $pt    = !empty($data['padding_top']) ? (int)$data['padding_top'] : 0;
    $pb    = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 0;
    $items = $data['repeater_data']['repeater_label_'] ?? [];
@endphp

@if(!empty($items))
<div class="container" style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px;">
    <div class="ch-trust">
        @foreach($items as $key => $label)
            @php
                $icon = $data['repeater_data']['repeater_icon_'][$key] ?? 'mdi mdi-truck-fast-outline';
                $desc = $data['repeater_data']['repeater_description_'][$key] ?? '';
            @endphp
            <div class="ch-trust-item">
                <div class="ch-trust-icon">
                    <i class="{{ $icon }}"></i>
                </div>
                <div>
                    <div class="ch-trust-label">{{ \App\Helpers\SanitizeInput::esc_html($label) }}</div>
                    @if(!empty($desc))
                        <div class="ch-trust-sub">{{ \App\Helpers\SanitizeInput::esc_html($desc) }}</div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
