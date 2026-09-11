<!-- Our Story -->
<section style="padding-top: {{ $style['section_style']['padding_top'] ?? 144 }}px; padding-bottom: {{ $style['section_style']['padding_bottom'] ?? 0 }}px; margin-top: {{ $style['section_style']['margin_top'] ?? 0 }}px; margin-bottom: {{ $style['section_style']['margin_bottom'] ?? 0 }}px; background-color: {{ $sectionBg }};">
    <div class="container mx-auto px-8">
        <!-- Grid Container -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 md:gap-10 lg:gap-16 items-center justify-between">

            <!-- Left: Image -->
            @if(!empty($image))
            <div class="order-1 lg:order-1 w-full h-auto rounded-3xl overflow-hidden shadow-soft">
                <div class="w-full h-full relative">
                    <img src="{{ $image }}" alt="{{ $imageAlt }}"
                         class="w-full h-auto rounded-3xl shadow-lg object-cover">
                </div>
            </div>
            @endif

            <!-- Right: Content -->
            <div class="order-2 lg:order-2 space-y-6 overflow-auto">

                <!-- Heading -->
                <h4 class="text-xl md:text-2xl lg:text-4xl font-urbanist font-semibold text-secondary">
                    {!! $heading !!}
                </h4>

                <!-- Body Text -->
                <div class="space-y-5 leading-6 font-normal">
                    @foreach($paragraphs as $paragraph)
                    <p class="text-base" style="color: var(--body-color, #666666)">
                        {!! $paragraph !!}
                    </p>
                    @endforeach
                </div>

            </div>

        </div>
    </div>
</section>
