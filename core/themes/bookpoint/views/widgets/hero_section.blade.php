<section class="bp-hero-section"
         style="padding-top:{{ $padding_top }}px; padding-bottom:{{ $padding_bottom }}px;">

    <div class="bp-hero-inner">

        {{-- ── Left: Text column ── --}}
        <div class="bp-hero-content-col">
            <div class="bp-hero-deco"></div>
            <div class="bp-hero-content">
                <h1 class="bp-hero-title">{!! $title !!}</h1>

                @if(!empty($description))
                    <p class="bp-hero-sub">{{ $description }}</p>
                @endif

                @if(!empty($btn_text))
                    <a href="{{ $btn_url }}" class="bp-btn-primary">{{ $btn_text }}</a>
                @endif
            </div>
        </div>

        {{-- ── Right: Book fan column ── --}}
        <div class="bp-hero-media-col">

            {{-- Float-animated book fan --}}
            <div class="bp-book-stack">

                {{-- Left book — partially behind center --}}
                @if(!empty($book1_url))
                    <div class="bp-book-item bp-book-left">
                        <img src="{{ $book1_url }}" alt="Book" loading="lazy">
                    </div>
                @endif

                {{-- Center book — featured, largest, in front --}}
                @if(!empty($book2_url))
                    <div class="bp-book-item bp-book-center">
                        <img src="{{ $book2_url }}" alt="Book" loading="lazy">
                    </div>
                @endif

                {{-- Right book — partially behind center --}}
                @if(!empty($book3_url))
                    <div class="bp-book-item bp-book-right">
                        <img src="{{ $book3_url }}" alt="Book" loading="lazy">
                    </div>
                @endif

            </div>
        </div>

    </div>
</section>
