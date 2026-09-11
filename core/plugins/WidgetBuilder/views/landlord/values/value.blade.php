<!-- Values -->
<section class="relative" style="padding-top: {{ $style['section_style']['padding_top'] ?? 20 }}px; padding-bottom: {{ $style['section_style']['padding_bottom'] ?? 128 }}px; margin-top: {{ $style['section_style']['margin_top'] ?? 0 }}px; margin-bottom: {{ $style['section_style']['margin_bottom'] ?? 0 }}px; background-color: {{ $sectionBg }};">
    <div class="container mx-auto px-8">
        <div class="max-w-2xl mx-auto text-center">
            <span class="rounded-lg py-1 px-3 border-2" style="background-color: {{ $badgeBg }}; color: {{ $badgeTextColor }}; border-color: {{ $badgeBorderColor }};">
                {{ $badgeText }}
            </span>
            <h3 class="font-urbanist font-semibold text-2xl sm:text-3xl md:text-4xl lg:text-5xl mt-4 lg:mt-6 leading-7 lg:leading-[3.25rem] text-secondary">
                {!! $heading !!}
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mt-16">
            @foreach($values as $value)
            <div class="rounded-xl p-6 border" style="background-color: {{ $cardBg }}; border-color: {{ $cardBorderColor }};">
                <!-- Icon -->
                @if(!empty($value['icon']))
                <div class="w-[48px] h-[48px] rounded-2xl flex items-center justify-center shadow-md transition-transform duration-300 ease-out group-hover:scale-110" style="background-color: {{ $iconBg }}">
                    <img src="{{ $value['icon'] }}" alt="{{ $value['title'] }}">
                </div>
                @endif

                <h3 class="text-[1.75rem] font-semibold font-urbanist mt-4 text-secondary">{{ $value['title'] }}</h3>

                <p class="text-base leading-6 mt-2 font-normal" style="color: {{ $descriptionColor }};">{{ $value['description'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
