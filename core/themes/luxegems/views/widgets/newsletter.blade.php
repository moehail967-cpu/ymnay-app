<section class="py-5" style="background:var(--lg-bg);">
    <div class="container">
        <div class="lg-newsletter">
            <div class="lg-line mx-auto"></div>
            <h3 class="lg-newsletter-title">{{ $title }}</h3>
            @if(!empty($subtitle))
                <p>{{ $subtitle }}</p>
            @endif
            @if(function_exists('tenant') && !is_null(tenant()))
                <div class="form-message-show mb-2" style="text-align:center;"></div>
                <form action="{{ theme_newsletter_subscribe_url() }}" method="POST"
                      class="newsletter-form lg-newsletter-form">
                    @csrf
                    <input type="email" name="email" class="email"
                           placeholder="{{ __('Your email address') }}" required>
                    <button type="submit" class="newsletter-submit-btn">{{ $button_text }}</button>
                </form>
            @endif
        </div>
    </div>
</section>
