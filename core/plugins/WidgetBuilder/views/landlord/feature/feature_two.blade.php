<!-- Detailed Features -->
<section class="pt-20 pb-32" style="background-color: var(--section-bg-3, #F4F8FB)">
    <div class="container mx-auto px-8">
        <div class="max-w-2xl mx-auto text-center">
            <span class="rounded-lg py-1 px-3 border-2 bg-subTitle text-base-200 border-borderCS">
                {{ $badgeText }}
            </span>
            <h3 class="font-urbanist font-semibold text-secondary text-2xl sm:text-3xl md:text-4xl lg:text-5xl mt-4 lg:mt-6 leading-7 lg:leading-[3.25rem]">
                {{ $title }}
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6 mt-[2.5rem] lg:mt-[4.375rem]">
            {{-- Top Row: 2 cards (each spans 3 cols) --}}
            @foreach($topCards as $card)
                <div class="lg:col-span-3 p-8 rounded-xl border border-borderCS" style="background-color: {{ $card['bg_color'] }}">
                    @if(!empty($card['image']))
                        <div class="flex justify-center items-center relative">
                            <img class="min-h-[339px] object-cover" src="{{ $card['image'] }}" alt="{{ $card['title'] }}">
                            <div class="inset-x-0 absolute -bottom-4 left-0 right-0 w-full h-32 bg-gradient-to-t to-transparent" style="--tw-gradient-from: {{ $card['bg_color'] }}; --tw-gradient-via: {{ $card['bg_color'] }}; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-via), var(--tw-gradient-to);">
                            </div>
                        </div>
                    @endif
                    <div class="relative z-10">
                        <h4 class="font-urbanist font-semibold text-secondary text-xl sm:text-xl md:text-2xl lg:text-3xl">
                            {{ $card['title'] }}
                        </h4>
                        <p class="font-normal mt-2" style="color: var(--body-color, #666666)">{{ $card['description'] }}</p>
                    </div>
                </div>
            @endforeach

            {{-- Bottom Row: 3 cards (each spans 2 cols) --}}
            @foreach($bottomCards as $card)
                <div class="lg:col-span-2 p-8 rounded-xl border border-borderCS" style="background-color: {{ $card['bg_color'] }}">
                    @if(!empty($card['image']))
                        <div class="flex justify-center items-center relative">
                            <img class="max-h-[232px] object-cover" src="{{ $card['image'] }}" alt="{{ $card['title'] }}">
                            <div class="inset-x-0 absolute -bottom-4 left-0 right-0 w-full h-24 bg-gradient-to-t to-transparent" style="--tw-gradient-from: {{ $card['bg_color'] }}; --tw-gradient-via: {{ $card['bg_color'] }}; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-via), var(--tw-gradient-to);">
                            </div>
                        </div>
                    @endif
                    <div class="relative z-10">
                        <h4 class="font-urbanist font-semibold text-secondary text-xl sm:text-xl md:text-2xl lg:text-3xl">
                            {{ $card['title'] }}
                        </h4>
                        <p class="font-normal mt-2" style="color: var(--body-color, #666666)">{{ $card['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
