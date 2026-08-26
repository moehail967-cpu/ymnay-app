<section class="relative w-full overflow-hidden bg-cover bg-center"
    @if(!empty($bgImage)) style="background-image: url('{{ $bgImage }}')" @endif>
    <div class="bg-primary opacity-80 absolute inset-0"></div>

    <div
        class="max-w-[624px] w-full mx-auto relative z-10 px-8 lg:px-0 py-36 flex flex-col items-center justify-center h-full text-center pt-35 pb-35">
        <h3
            class="font-urbanist font-semibold text-white text-2xl sm:text-3xl md:text-4xl lg:text-5xl leading-7 lg:leading-[3.25rem]">
            {{ $title }}
        </h3>
{{--@dd($description)--}}
        @if(!empty($description))
        <p class="mt-[28px] text-base-100 font-normal">{{ $description }}</p>
        @endif

        <div class="flex items-center justify-center flex-wrap gap-6 mt-8">
            @if(!empty($primaryBtnText))
            <button
                class="primary-btn text-secondary font-medium px-4 sm:px-6 py-2 sm:py-2.5 lg:py-2.5 lg:px-6 text-sm sm:text-base rounded-[8px] transition-all duration-300 ease-in-out hover:shadow-xl hover:scale-105 active:scale-95 cursor-pointer group">
                <span
                    class="flex justify-center items-center gap-2 font-medium text-base-200 transition-transform duration-300 group-hover:-translate-x-1">
                    <a href="{{ $primaryBtnUrl }}">{{ $primaryBtnText }}</a>
                    <i class="ti tabler-arrow-narrow-right text-secondary text-[20px]"></i>
                </span>
            </button>
            @endif

            @if(!empty($secondaryBtnText))
            <button
                class="secondary-btn text-secondary font-medium px-4 sm:px-6 py-2 sm:py-2.5 lg:py-2.5 lg:px-6 text-sm sm:text-base rounded-[8px] flex items-center gap-2 transition-all duration-300 ease-in-out hover:shadow-xl hover:scale-105 active:scale-95 cursor-pointer group">
                <span
                    class="flex items-center justify-center gap-2 font-medium text-secondary transition-transform duration-300 group-hover:-translate-x-1">
                    @if(!empty($secondaryBtnIcon))
                    <img src="{{ $secondaryBtnIcon }}" alt="">
                    @endif
                    <a href="{{ $secondaryBtnUrl }}">{{ $secondaryBtnText }}</a>
                </span>
            </button>
            @endif
        </div>
    </div>
</section>
