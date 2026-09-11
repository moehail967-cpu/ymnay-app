<section class="header_two_wrapper bg-center bg-cover bg-no-repeat w-full min-h-screen"
    style="@if(!empty($bgImage)) background-image: url('{{ $bgImage }}'); @endif">
    <div class="min-h-screen flex flex-col">
        <div class="relative flex-1 mx-auto w-full">
            <!-- Content Container -->
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-32 sm:mt-36 md:mt-[6rem] lg:mt-[10rem] flex flex-col justify-center items-center relative z-10">
                <!-- Top Section: Main Heading -->
                <div class="max-w-[1000px] w-full relative z-10 text-center flex flex-col items-center justify-between">
                    <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-[65px] text-white leading-10 lg:leading-[80px] font-urbanist font-bold text-center">
                        {!! $title !!}
                        @if(!empty($underlineImage))
                            <img class="w-64 hidden lg:block absolute top-[3.60rem] right-40"
                                 src="{{ $underlineImage }}" alt="">
                        @endif
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
                    <span class="mt-5 text-base-100 font-medium">{{ $subtitle }}</span>
                </div>

                @if(!empty($bannerImage))
                    <div class="w-full mt-20">
                        <img class="w-full h-auto" src="{{ $bannerImage }}" alt="hero-banner">
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
