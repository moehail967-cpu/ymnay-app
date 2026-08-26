<section class="relative bg-center bg-cover bg-no-repeat py-28"
    @if(!empty($bgImage)) style="background-image: url('{{ $bgImage }}')" @endif>
    <div class="absolute inset-0 bg-black/80 z-0"></div>

    <div class="container mx-auto px-4 flex items-center-center justify-between head relative z-10 pb-14">
        <div class="max-w-lg">
            <span class="inline-block rounded-lg py-1 px-3 border bg-[#E8C8FF]/15 text-base-100 border-borderCS">
                {{ $badgeText }}
            </span>
            <h3 class="font-urbanist font-bold text-white text-2xl sm:text-3xl md:text-4xl lg:text-5xl mt-4 lg:mt-6 leading-7 lg:leading-[3.25rem]">
                {{ $title }}
            </h3>
        </div>
        <div class="controls hidden lg:flex">
            <span id="prev" class="nav-btn bg-[#7B7B7B] group w-12 h-12 flex items-center justify-center rounded-full hover:bg-white" aria-label="Prev">
                <i class="icon-base ti tabler-arrow-left text-white group-hover:text-secondary"></i>
            </span>
            <span id="next" class="nav-btn bg-[#7B7B7B] group w-12 h-12 flex items-center justify-center rounded-full hover:bg-white" aria-label="Next">
                <i class="icon-base ti tabler-arrow-right text-white group-hover:text-secondary"></i>
            </span>
        </div>
    </div>
    <div class="container mx-auto px-4 md:px-6 lg:px8">
        <div class="track" id="track">
            @foreach($cards as $index => $card)
                <article class="project-card{{ $index === 0 ? ' container mx-auto px-4 md:px-6 lg:px8' : '' }}" {{ $index === 0 ? 'active' : '' }}>
                    <img class="project-card__bg" src="{{ $card['image'] }}" alt="{{ $card['title'] }}">
                    <div class="project-card__content">
                        <img class="project-card__thumb" src="{{ $card['image'] }}" alt="{{ $card['title'] }}">
                        <div class="space-y-5">
                            <h3 class="project-card__title font-bold">{{ $card['title'] }}</h3>
                            <a href="{{ $card['button_url'] }}" class="project-card__btn">{{ $card['button_text'] }}</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
    <div class="dots" id="dots"></div>
</section>
