<section class="ar-insta-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="ar-insta-inner">
        <a href="{{ $instagram_url }}" target="_blank" rel="noopener noreferrer" class="ar-insta-link">
            <i class="lab la-instagram ar-insta-icon"></i>
            @if(!empty($title))
                <span class="ar-insta-title">{{ $title }}</span>
            @endif
        </a>
    </div>
</section>
