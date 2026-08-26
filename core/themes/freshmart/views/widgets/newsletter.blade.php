<section class="fm-newsletter-section">
    <div class="container">
        <div class="fm-newsletter-inner">

            @if(!empty($tag_text))
                <div class="fm-newsletter-tag">{{ $tag_text }}</div>
            @endif

            <h2 class="fm-newsletter-title">{{ $title }}</h2>

            @if(!empty($subtitle))
                <p class="fm-newsletter-sub">{{ $subtitle }}</p>
            @endif

            @if(function_exists('tenant') && !is_null(tenant()))
                <form action="{{ theme_newsletter_subscribe_url() }}" method="POST" class="newsletter-form fm-newsletter-form">
                    @csrf
                    <div class="form-message-show mb-3"></div>
                    <div class="fm-nl-row">
                        <input type="email" name="email" class="email" placeholder="{{ __('Your email address') }}" required>
                        <button type="submit" class="newsletter-submit-btn">{{ $button_text }}</button>
                    </div>
                </form>
            @endif

        </div>
    </div>
</section>
