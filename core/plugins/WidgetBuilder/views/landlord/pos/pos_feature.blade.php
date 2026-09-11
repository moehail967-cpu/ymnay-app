<!-- Pos Features -->
<section class="relative bg-cover bg-center min-h-[500px] py-32"
    @if(!empty($bgImage)) style="background-image: url('{{ $bgImage }}')" @endif>

    <div class="container mx-auto px-8">
        <div class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto text-center">
                <span
                    class="inline-block rounded-lg py-1 px-3 border bg-[#E8C8FF]/15 text-base-100 border-borderCS">
                    {{ $badgeText }}
                </span>
                <h3
                    class="font-urbanist font-semibold text-white text-2xl sm:text-3xl md:text-4xl lg:text-5xl mt-4 lg:mt-6 leading-7 lg:leading-[3.25rem]">
                    {{ $title }}
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-16">
                @foreach($cards as $card)
                <div class="bg-[#00252A] rounded-xl p-6 border border-quate">
                    @if(!empty($card['icon']))
                    <div
                        class="w-[48px] h-[48px] main-color-two rounded-2xl flex items-center justify-center shadow-md transition-transform duration-300 ease-out group-hover:scale-110">
                        <img src="{{ $card['icon'] }}" alt="{{ $card['title'] }}">
                    </div>
                    @endif

                    <h3 class="text-xl font-medium text-white mt-4">{{ $card['title'] }}</h3>

                    <p class="text-borderCS mt-2 font-normal">{{ $card['description'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
