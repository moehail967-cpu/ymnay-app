<!-- Our Journey -->
<section class="relative" style="padding-top: {{ $style['section_style']['padding_top'] ?? 128 }}px; padding-bottom: {{ $style['section_style']['padding_bottom'] ?? 128 }}px; margin-top: {{ $style['section_style']['margin_top'] ?? 0 }}px; margin-bottom: {{ $style['section_style']['margin_bottom'] ?? 0 }}px; background-color: {{ $sectionBg }};">
    <div class="container max-w-6xl mx-auto px-8">
        <div class="max-w-2xl mx-auto text-center">
            <span class="rounded-lg py-1 px-3 border-2" style="background-color: {{ $badgeBg }}; color: {{ $badgeTextColor }}; border-color: {{ $badgeBorderColor }};">
                {{ $badgeText }}
            </span>
            <h3 class="font-urbanist font-semibold text-2xl sm:text-3xl md:text-4xl lg:text-5xl mt-4 lg:mt-6 leading-7 lg:leading-[3.25rem] text-secondary">
                {!! $heading !!}
            </h3>
        </div>

        <div class="mt-16">
            <!-- Timeline Container -->
            <div class="relative">
                <!-- Vertical Line - Desktop -->
                <div class="absolute left-1/2 transform -translate-x-1/2 w-1 h-full hidden lg:block" style="background-color: {{ $timelineLineColor }};"></div>

                <!-- Vertical Line - Mobile -->
                <div class="absolute left-6 w-0.5 h-full lg:hidden" style="background-color: {{ $timelineLineColor }};"></div>

                <!-- Timeline Items -->
                <div class="space-y-0">
                    @foreach($milestones as $index => $milestone)
                    @if($index % 2 === 0)
                    {{-- Left aligned card --}}
                    <div class="relative lg:flex lg:items-center lg:justify-between pb-4 lg:pb-4">
                        <!-- Card (Left on desktop) -->
                        <div class="lg:w-[50%] lg:text-right ml-16 lg:ml-0">
                            <div class="rounded-2xl border shadow-[0_1px_3px_rgba(0,0,0,0.08)] p-4 lg:p-6" style="background-color: {{ $cardBg }}; border-color: {{ $cardBorderColor }};">
                                <div class="inline-block rounded-lg px-4 py-1.5" style="background-color: {{ $yearBg }};">
                                    <span class="text-sm font-medium" style="color: {{ $yearTextColor }};">{{ $milestone['year'] }}</span>
                                </div>
                                <h3 class="text-lg lg:text-xl font-medium mt-4 mb-2 text-secondary">{{ $milestone['title'] }}</h3>
                                <p class="text-base leading-6" style="color: {{ $cardDescColor }};">{{ $milestone['description'] }}</p>
                            </div>
                        </div>
                        <!-- Spacer -->
                        <div class="hidden lg:block lg:w-[8%]"></div>
                        <!-- Empty Right Side -->
                        <div class="hidden lg:block lg:w-[46%]"></div>
                        <!-- Dot -->
                        <div class="main-color-two absolute lg:left-1/2 left-6 top-0 transform lg:-translate-x-1/2 -translate-x-1/2 w-4 h-4 rounded-full border-2 shadow-sm z-10 " style=" border-color: {{ $dotBorderColor }};"></div>
                    </div>
                    @else
                    {{-- Right aligned card --}}
                    <div class="relative lg:flex lg:items-center lg:justify-between pb-12 lg:pb-4">
                        <!-- Empty Left Side -->
                        <div class="hidden lg:block lg:w-[46%]"></div>
                        <!-- Spacer -->
                        <div class="hidden lg:block lg:w-[8%]"></div>
                        <!-- Card (Right on desktop) -->
                        <div class="lg:w-[50%] lg:text-left ml-16 lg:ml-0">
                            <div class="rounded-2xl border shadow-[0_1px_3px_rgba(0,0,0,0.08)] p-4 lg:p-6" style="background-color: {{ $cardBg }}; border-color: {{ $cardBorderColor }};">
                                <div class="inline-block rounded-lg px-4 py-1.5" style="background-color: {{ $yearBg }};">
                                    <span class="text-sm font-medium" style="color: {{ $yearTextColor }};">{{ $milestone['year'] }}</span>
                                </div>
                                <h3 class="text-lg lg:text-xl font-medium mt-4 mb-2 text-secondary">{{ $milestone['title'] }}</h3>
                                <p class="text-base leading-6" style="color: {{ $cardDescColor }};">{{ $milestone['description'] }}</p>
                            </div>
                        </div>
                        <!-- Dot -->
                        <div class="main-color-two absolute lg:left-1/2 left-6 top-0 transform lg:-translate-x-1/2 -translate-x-1/2 w-4 h-4 rounded-full border-2 shadow-sm z-10 " style=" border-color: {{ $dotBorderColor }};"></div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
