<!-- Our Achievements -->
<section style="padding-top: {{ $style['section_style']['padding_top'] ?? 112 }}px; padding-bottom: {{ $style['section_style']['padding_bottom'] ?? 144 }}px; margin-top: {{ $style['section_style']['margin_top'] ?? 0 }}px; margin-bottom: {{ $style['section_style']['margin_bottom'] ?? 0 }}px; background-color: {{ $sectionBg }};">
    <div class="container mx-auto px-8 rounded-xl">
        <!-- Stats Grid -->
        <div class="grid grid-cols-2 sm:flex sm:justify-between sm:items-center sm:flex-wrap gap-4 p-6 rounded-lg" style="background-color: {{ $containerBg }};">
            @foreach($stats as $stat)
                <div class="text-center transition-transform duration-300 hover:-translate-y-1">
                    <p class="text-3xl lg:text-5xl mb-2 text-secondary">
                        <span class="counter font-medium font-urbanist" data-count="{{ $stat['count'] }}">0</span>{{ $stat['suffix'] }}
                    </p>
                    <p class="font-normal leading-7 text-sm lg:text-xl" style="color: var(--body-color, #666666)">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
