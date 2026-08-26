<div class="container mb-5" style="padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px;">
    <div class="ch-trust">
        @foreach($items as $item)
            <div class="ch-trust-item">
                <div class="ch-trust-icon">
                    <i class="{{ $item['icon'] ?? 'las la-check-circle' }}"></i>
                </div>
                <div>
                    <div class="ch-trust-label">{{ $item['label'] ?? '' }}</div>
                    @if(!empty($item['description']))
                        <div class="ch-trust-sub">{{ $item['description'] }}</div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
