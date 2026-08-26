<section class="kv-newsletter-section py-5">
    <div class="container">
        <div class="kv-newsletter-card">
            <h2 class="kv-newsletter-title">{{ $title }}</h2>
            @if(!empty($subtitle))
                <p class="kv-newsletter-sub">{{ $subtitle }}</p>
            @endif
            @if(function_exists('tenant') && !is_null(tenant()))
                <form action="{{ theme_newsletter_subscribe_url() }}" method="POST" class="newsletter-form kv-newsletter-form">
                    @csrf
                    <div class="form-message-show"></div>
                    <div class="kv-nl-row">
                        <input type="email" name="email" class="email kv-nl-input" placeholder="{{ __('Your email address') }}" required>
                        <button type="submit" class="newsletter-submit-btn kv-nl-btn">{{ $button_text }}</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</section>
