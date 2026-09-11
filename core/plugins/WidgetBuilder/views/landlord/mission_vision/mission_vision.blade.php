<!-- Mission Vision -->
<section style="padding-top: {{ $style['section_style']['padding_top'] ?? 0 }}px; padding-bottom: {{ $style['section_style']['padding_bottom'] ?? 128 }}px; margin-top: {{ $style['section_style']['margin_top'] ?? 0 }}px; margin-bottom: {{ $style['section_style']['margin_bottom'] ?? 0 }}px; background-color: {{ $sectionBg }};">
    <div class="container mx-auto px-8">
        <!-- Grid Container -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center justify-between">

            <!-- Mission Card -->
            <div class="order-1 lg:order-1 shadow-soft rounded-3xl overflow-hidden p-6 border" style="background-color: {{ $cardBg }}; border-color: {{ $cardBorderColor }};">
                @if(!empty($missionIcon))
                <div class="w-14 h-14 flex items-center justify-center rounded-lg mb-6" style="background-color: {{ $iconBg }};">
                    <img src="{{ $missionIcon }}" alt="{{ $missionTitle }}">
                </div>
                @endif
                <!-- Heading -->
                <h4 class="text-xl md:text-2xl lg:text-4xl font-urbanist font-semibold text-secondary">
                    {!! $missionTitle !!}
                </h4>
                <p class="text-base mt-4 font-normal" style="color: var(--body-color, #666666)">
                    {!! $missionDescription !!}
                </p>
            </div>

            <!-- Vision Card -->
            <div class="order-2 lg:order-2 shadow-soft rounded-3xl overflow-hidden p-6 border" style="background-color: {{ $cardBg }}; border-color: {{ $cardBorderColor }};">
                @if(!empty($visionIcon))
                <div class="w-14 h-14 flex items-center justify-center rounded-lg mb-6" style="background-color: {{ $iconBg }};">
                    <img src="{{ $visionIcon }}" alt="{{ $visionTitle }}">
                </div>
                @endif
                <!-- Heading -->
                <h4 class="text-xl md:text-2xl lg:text-4xl font-urbanist font-semibold text-secondary">
                    {!! $visionTitle !!}
                </h4>
                <p class="text-base mt-4 font-normal" style="color: var(--body-color, #666666)">
                    {!! $visionDescription !!}
                </p>
            </div>

        </div>
    </div>
</section>
