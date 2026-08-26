<section class="header__one_section_wrapper">
    <div class="">
        <div class="relative h-auto overflow-hidden mx-auto w-full">
            <!-- Background Image/Video -->
            <div class="absolute w-full h-full inset-0 -z-1">
                @if(!empty($bgVideo))
                    <video class="w-full h-full aspect-video object-cover" autoplay muted loop>
                        <source src="{{ $bgVideo }}" type="video/mp4">
                    </video>
                @elseif(!empty($bgImage))
                    <img src="{{ $bgImage }}" alt="" class="w-full h-full object-cover">
                @endif
                <!-- Dark Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-br from-black/30 to-black"></div>
            </div>

            <!-- Content Container -->
            <div class="container mx-auto px-4 sm:px-6 sm:mt-36 md:mt-[6rem] lg:mt-[10rem] lg:px-8 flex flex-col justify-center items-center relative z-10 mb-[4.357rem] header_one_container_outer">
                <div class="max-w-[1000px] w-full h-full relative z-10 text-center flex flex-col items-center justify-between pt-[120px] sm:pt-0">
                    <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-[65px] text-white leading-10 lg:leading-[80px] font-urbanist font-bold text-center">
                        {!! $title !!}
                    </h1>

                    <div class="flex items-center justify-center flex-wrap gap-6 mt-8">
                        <a href="{{ $primaryBtnUrl }}"
                           class="secondary-btn text-base-200 font-medium px-4 sm:px-6 py-2 sm:py-2.5 lg:py-2.5 lg:px-6 text-sm sm:text-base rounded-[8px] transition-all duration-300 ease-in-out hover:shadow-xl hover:scale-105 active:scale-95 cursor-pointer group">
                            <span class="flex justify-center items-center gap-2 font-medium text-base-200 transition-transform duration-300 group-hover:-translate-x-1">
                                {{ $primaryBtnText }}
                                <i class="ti tabler-arrow-narrow-right text-black text-[20px]"></i>
                            </span>
                        </a>
                        <a href="{{ $secondaryBtnUrl }}"
                           class="primary-btn text-base-200 font-medium text-sm sm:text-base rounded-[8px] flex items-center gap-2 transition-all duration-300 ease-in-out hover:shadow-xl hover:scale-105 active:scale-95 cursor-pointer group py-2 sm:py-2.5 lg:py-2.5 px-4 sm:px-6 lg:px-6">
                            <span class="font-medium text-base-200 transition-transform duration-300 group-hover:-translate-x-1 flex items-center justify-center gap-2">
                                <img src="{{ asset('assets/new-landlord/images/home/video.svg') }}" alt="">
                                {{ $secondaryBtnText }}
                            </span>
                        </a>
                    </div>

                    <span class="mt-6 text-base-100 font-medium">{{ $subtitle }}</span>
                </div>
            </div>

            <!-- Showcase Slider -->
            @if(!empty($images))
                <div class="slider-wrap">
{{--                    <span class="side-nav prev">PREV</span>--}}
{{--                    <span class="side-nav next">NEXT</span>--}}
                    <div class="owl-carousel [h-773px]">
                        @foreach($images as $image)
                            <div class="min-w-[200px] h-[250px] md:h-[500px] lg:h-[773px]">
                                <img class="w-full h-full object-fill" src="{{ $image }}" alt="Showcase">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
