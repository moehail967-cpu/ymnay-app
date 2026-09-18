<section class="ar-insta-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="ar-section-head">
            <span class="ar-section-tag">INSTAGRAM</span>
            @if(!empty($title))<h2 class="ar-section-title">{{ $title }}</h2>@endif
        </div>
        @if(!empty($images))
            <div class="ar-insta-gallery">
                @foreach($images as $image)
                    <a href="{{ $instagram_url ?: '#' }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $title }}">
                        <img src="{{ $image }}" alt="{{ $title }}" loading="lazy">
                        <i class="lab la-instagram" aria-hidden="true"></i>
                    </a>
                @endforeach
            </div>
        @endif
        <a href="{{ $instagram_url ?: '#' }}" target="_blank" rel="noopener noreferrer" class="ar-insta-link">
            <i class="lab la-instagram ar-insta-icon" aria-hidden="true"></i>
            {{ __('تابعنا على إنستغرام') }}
        </a>
    </div>
</section>
