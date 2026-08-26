<section class="ch-how-it-works-widget" style="padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px;">
    <div class="container">
        <div class="text-center mb-5">
            <div class="ch-sec-title justify-content-center">{{ $title }}</div>
            @if(!empty($subtitle))
                <p style="color:var(--ch-muted);margin-top:10px;font-size:15px;">{{ $subtitle }}</p>
            @endif
        </div>

        <div class="ch-steps">
            @foreach($steps as $index => $step)
                <div class="ch-step">
                    <div class="ch-step-icon-wrap">
                        @if(!empty($step['icon']))
                            <i class="{{ $step['icon'] }}" style="font-size:28px;color:var(--ch-red);"></i>
                        @else
                            {{ $index + 1 }}
                        @endif
                    </div>
                    <div class="ch-step-num">{{ __('Step') }} {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                    <div class="ch-step-title">{{ $step['title'] ?? '' }}</div>
                    @if(!empty($step['description']))
                        <div class="ch-step-desc">{{ $step['description'] }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
