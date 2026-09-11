<section class="gc-widget-newsletter">
<div class="container">
    <div class="gc-newsletter">
        <div class="gc-newsletter-inner">
            @if(!empty($tag_text))
                <div class="gc-section-tag">{{ $tag_text }}</div>
            @endif
            <h3>{{ $title }}</h3>
            <div class="gc-divider mx-auto"></div>
            @if(!empty($subtitle))
                <p>{{ $subtitle }}</p>
            @endif
            @if(function_exists('tenant') && !is_null(tenant()))
                <div class="form-message-show gc-nl-msg"></div>
                <form action="{{ theme_newsletter_subscribe_url() }}" method="POST" class="gc-newsletter-form newsletter-form">
                    @csrf
                    <input type="email" name="email" class="email" placeholder="{{ __('Your email address') }}" required>
                    <button type="submit" class="newsletter-submit-btn">{{ $button_text }}</button>
                </form>
            @endif
        </div>
    </div>
</div>
</section>
