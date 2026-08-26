<!-- Rise -->
<section class="bg-primary py-[120px]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center">
            <span class="rounded-lg py-1 px-3 border border-borderCS bg-gradient-to-r from-[#FFFFFF]/20 to-[#E8C8FF]/20 text-base-100">
                {{ $badgeText }}
            </span>
            <h3 class="font-urbanist font-semibold text-white text-2xl sm:text-3xl md:text-4xl lg:text-5xl mt-4 lg:mt-6 leading-7 lg:leading-[3.25rem]">
                {{ $title }}
            </h3>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-[4.375rem]">
            {{-- Left: Highlight Card --}}
            <div class="col-span-1 lg:row-span-{{ count($statItems) }} bg-rise flex flex-col justify-center items-center rounded-xl">
                <div class="flex flex-col items-center justify-center py-10">
                    @if(!empty($highlight['icon']))
                        <div class="w-16 h-16 rounded-full flex justify-center items-center bg-primary p-4 md:h-24 md:w-24">
                            <img src="{{ $highlight['icon'] }}" alt="icon">
                        </div>
                    @endif
                    <div class="max-w-80 p-4 text-center mt-6 lg:mt-10">
                        <h4 class="font-urbanist text-2xl md:text-4xl lg:text-5xl font-semibold text-secondary">
                            {{ $highlight['counter_prefix'] }}<span data-count="{{ $highlight['counter_value'] }}" class="counter">0</span>{{ $highlight['counter_suffix'] }}
                        </h4>
                        <p class="text-[1.125rem] font-medium mt-8 text-secondary">
                            {{ $highlight['subtitle'] }}
                        </p>
                        <p class="leading-6 font-normal mt-3" style="color: var(--body-color, #666666)">
                            {{ $highlight['description'] }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Right: Stat Cards --}}
            @foreach($statItems as $stat)
                <div class="col-span-1 lg:col-span-2 p-8 rounded-xl" style="background-color: {{ $stat['card_bg'] }}">
                    <h4 class="font-urbanist text-2xl md:text-4xl lg:text-5xl font-semibold text-secondary">
                        {{ $stat['counter_prefix'] }}<span data-count="{{ $stat['counter_value'] }}" class="counter">0</span>{{ $stat['counter_suffix'] }}
                    </h4>
                    <h4 class="text-[1.125rem] font-medium mt-6 text-secondary">{{ $stat['title'] }}</h4>
                    <p class="leading-6 font-normal mt-3" style="color: var(--body-color, #666666)">{{ $stat['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
