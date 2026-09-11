<section class="brand-marquee">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="">
            <div class="slides-wrapper">
                @foreach($logos as $logo)
                    <div class="slide-box">
                        <div class="box-content">
                            @if(!empty($logo['link']))
                                <a href="{{ $logo['link'] }}" target="_blank" rel="noopener">
                                    <img src="{{ $logo['url'] }}" alt="{{ $logo['name'] }}">
                                </a>
                            @else
                                <img src="{{ $logo['url'] }}" alt="{{ $logo['name'] }}">
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
