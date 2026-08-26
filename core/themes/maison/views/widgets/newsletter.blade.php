<section class="ms-widget-newsletter">
    <div class="container">
        <div class="ms-widget-newsletter-inner">
            <h2 class="ms-newsletter-title">{{ $title }}</h2>
            @if(!empty($subtitle))
                <p class="ms-newsletter-sub">{{ $subtitle }}</p>
            @endif
            @if(function_exists('tenant') && !is_null(tenant()))
                <form action="{{ theme_newsletter_subscribe_url() }}" method="POST" class="newsletter-form ms-newsletter-form">
                    @csrf
                    <div class="form-message-show"></div>
                    <div class="ms-nl-row">
                        <input type="email" name="email" class="email" placeholder="{{ __('Your email address') }}" required>
                        <button type="submit" class="newsletter-submit-btn ms-newsletter-btn">{{ $button_text }}</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</section>
