<!-- Features -->
<section class="pt-10" style="background-color: var(--section-bg-3, #F4F8FB)">
    <div class="container mx-auto px-8">
        <div class="max-w-2xl mx-auto text-center">
            <span class="rounded-lg py-1 px-3 border-2 bg-subTitle text-base-200 border-borderCS">
                {{ $badgeText }}
            </span>
            <h3 class="font-urbanist font-semibold text-secondary text-2xl sm:text-3xl md:text-4xl lg:text-5xl mt-4 lg:mt-6 leading-7 lg:leading-[3.25rem]">
                {{ $title }}
            </h3>
        </div>
    </div>
    <div class="py-4 md:py-[4.375rem] px-8">
        <div class="relative">
            <div class="swiper mySwiper overflow-hidden">
                <div class="swiper-wrapper">
                    @foreach($cards as $card)
                        <div class="swiper-slide">
                            <div class="bg-cover bg-center bg-no-repeat max-w-[424px] h-[506px] w-full p-6 rounded-xl flex flex-col overflow-hidden"
                                 @if(!empty($card['bg_image']))
                                     style="background-image: url('{{ $card['bg_image'] }}')"
                                 @else
                                     style="background-color: var(--main-color-one, #0D4D54)"
                                @endif
                            >
                                <div class="shrink-0">
                                    <h4 class="font-urbanist text-[1rem] lg:text-[1.75rem] font-semibold text-white">
                                        {{ $card['title'] }}
                                    </h4>
                                    <p class="text-borderCS font-normal mt-2 lg:mt-4 line-clamp-3">
                                        {{ $card['description'] }}
                                    </p>
                                </div>
                                @if(!empty($card['image']))
                                    <div class="flex items-center justify-center mt-auto pt-6">
                                        <img src="{{ $card['image'] }}" alt="{{ $card['title'] }}" class="w-[300px] h-[250px] object-contain">
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
            <div class="absolute md:-bottom-4 right-4 flex gap-3 z-10">
                <button type="button" id="prevBtn" class="nav-btn text-white">
                    <i class="ti tabler-arrow-left w-7 h-7"></i>
                </button>
                <button type="button" id="nextBtn" class="nav-btn">
                    <i class="ti text-white tabler-arrow-right w-7 h-7"></i>
                </button>
            </div>
        </div>
    </div>
</section>
