<!-- Themes -->
<section class="relative bg-cover bg-center min-h-[500px] py-32"
    @if(!empty($bgImage)) style="background-image: url('{{ $bgImage }}')" @endif>

    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/90 z-0"></div>

    <!-- Content -->
    <div class="relative z-10">
        <div class="max-w-2xl text-center container mx-auto px-4 sm:px-6 lg:px-8">
            <span
                class="inline-block rounded-lg py-1 px-3 border bg-[#E8C8FF]/15 text-base-100 border-borderCS">
                {{ $badgeText }}
            </span>
            <h3
                class="font-urbanist font-semibold text-white text-2xl sm:text-3xl md:text-4xl lg:text-5xl mt-4 lg:mt-6 leading-7 lg:leading-[3.25rem]">
                {{ $title }}
            </h3>
        </div>

        @if(!empty($rowOneImages))
        <div class="logo-row mt-[4.375rem]">
            <div class="marquee">
                <ul class="marquee-content">
                    @foreach($rowOneImages as $image)
                    <li><img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}"></li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        @if(!empty($rowTwoImages))
        <div class="logo-row">
            <div class="marquee reverse">
                <ul class="marquee-content">
                    @foreach($rowTwoImages as $image)
                    <li><img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}"></li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>
</section>
