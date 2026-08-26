<section class="dk-widget-newsletter">
    <div class="container">
        <div class="dk-newsletter-inner">
            @if($tag)
            <span class="dk-section-tag d-inline-flex mb-3">
                <i class="las la-cog"></i> {!! $tag !!}
            </span>
            @endif
            @if($title)
            <h2 class="dk-section-title">{!! $title !!}</h2>
            @endif
            @if($subtitle)
            <p class="dk-section-sub">{{ $subtitle }}</p>
            @endif
            <form class="newsletter-form footer-widget" method="POST" action="{{ theme_newsletter_subscribe_url() }}">
                @csrf
                <div class="form-message-show"></div>
                <div class="dk-newsletter-form">
                    <input type="email" name="email" class="email" placeholder="{{ __('Your email address') }}" required>
                    <button type="submit" class="newsletter-submit-btn dk-btn dk-btn-red">{{ $button_text }}</button>
                </div>
            </form>
        </div>
    </div>
</section>
