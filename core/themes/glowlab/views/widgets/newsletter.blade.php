<section class="gl-widget-newsletter">
<div class="container">
    <div class="gl-newsletter-inner">
        @if(!empty($tag_text))
            <div class="gl-newsletter-tag">{{ $tag_text }}</div>
        @endif
        <h2 class="gl-newsletter-title">{{ $title }}</h2>
        @if(!empty($subtitle))
            <p class="gl-newsletter-sub">{{ $subtitle }}</p>
        @endif
        @if(function_exists('tenant') && !is_null(tenant()))
            <form action="{{ theme_newsletter_subscribe_url() }}" method="POST" class="newsletter-form">
                @csrf
                <div class="form-message-show"></div>
                <div class="gl-nl-row">
                    <input type="email" name="email" class="email gl-nl-input" placeholder="{{ __('Your email address') }}" required>
                    <button type="submit" class="newsletter-submit-btn gl-nl-btn">{{ $button_text }}</button>
                </div>
            </form>
        @endif
    </div>
</div>
</section>
