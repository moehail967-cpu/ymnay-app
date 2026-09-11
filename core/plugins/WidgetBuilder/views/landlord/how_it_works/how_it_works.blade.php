<!-- How It Works -->
<section class="py-20" style="background-color: var(--section-bg-3, #F4F8FB)">
    <div class="container mx-auto px-8">
        <div class="max-w-2xl mx-auto text-center">
            <span class="rounded-lg py-1 px-3 border-2 bg-subTitle text-base-200 border-borderCS">
                {{ $badgeText }}
            </span>
            <h3 class="font-urbanist font-semibold text-secondary text-2xl sm:text-3xl md:text-4xl lg:text-5xl mt-4 lg:mt-6 leading-7 lg:leading-[3.25rem]">
                {{ $title }}
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-[70px]">
            {{-- Gradient connecting line --}}
            <div class="relative top-[139px] w-full col-span-1 md:col-span-2 lg:col-span-3 bg-gradient-to-l to-[#BEDBFF] via-[#E9D4FF] from-[#FCCEE8] h-[2px] hidden lg:block"></div>

            @foreach($steps as $step)
                <div class="w-full rounded-2xl p-12 overflow-hidden group shadow-md lg:shadow-none lg:hover:shadow-2xl transition-all duration-500 ease-out lg:hover:scale-105"
                     style="background-color: var(--section-bg-1, #FFFFFF)">

                    <div class="flex flex-col items-center text-center space-y-6">

                        {{-- Icon --}}
                        @if(!empty($step['icon']))
                            <div class="w-16 h-16 bg-primary rounded-2xl flex items-center justify-center shadow-md transition-transform duration-300 ease-out lg:group-hover:scale-110">
                                <img src="{{ $step['icon'] }}" alt="{{ $step['title'] }}">
                            </div>
                        @endif

                        {{-- Step number --}}
                        <div class="text-6xl font-semibold font-urbanist leading-none select-none" style="color: var(--extra-light-color, #E5E7EB)">
                            {{ $step['number'] }}
                        </div>

                        {{-- Title --}}
                        <h3 class="text-xl font-medium text-secondary tracking-tight">
                            {{ $step['title'] }}
                        </h3>

                        {{-- Description --}}
                        <p class="leading-relaxed text-base font-normal max-w-sm" style="color: var(--body-color, #666666)">
                            {{ $step['description'] }}
                        </p>

                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
