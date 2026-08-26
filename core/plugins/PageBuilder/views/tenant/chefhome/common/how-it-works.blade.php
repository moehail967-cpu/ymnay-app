@php
    $pt    = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb    = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $items = $data['repeater_data']['repeater_title_'] ?? [];
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:#fff;">
    <div class="container">

        @if(!empty($data['title']))
            <div class="text-center" style="margin-bottom:48px;">
                <h2 class="ch-sec-title" style="justify-content:center;">{{ $data['title'] }}</h2>
                @if(!empty($data['subtitle']))
                    <p style="font-size:15px; color:var(--ch-muted); margin-top:10px; max-width:520px; margin-left:auto; margin-right:auto;">
                        {{ $data['subtitle'] }}
                    </p>
                @endif
            </div>
        @endif

        @if(!empty($items))
            <div class="row g-4 justify-content-center">
                @foreach($items as $key => $title)
                    @php
                        $icon = $data['repeater_data']['repeater_icon_'][$key] ?? 'mdi mdi-food';
                        $desc = $data['repeater_data']['repeater_description_'][$key] ?? '';
                        $step = $key + 1;
                    @endphp
                    <div class="col-md-4">
                        <div class="ch-step">
                            <div class="ch-step-icon">
                                <i class="{{ $icon }}"></i>
                            </div>
                            <h3 class="ch-step-title">{{ \App\Helpers\SanitizeInput::esc_html($title) }}</h3>
                            @if(!empty($desc))
                                <p class="ch-step-sub">{{ \App\Helpers\SanitizeInput::esc_html($desc) }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</section>
