<section class="pf-newsletter-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="pf-newsletter-inner">
            <span class="pf-newsletter-emoji">💌</span>
            <h2 class="pf-newsletter-title">{{ $title }}</h2>
            @if($subtitle)
                <p class="pf-newsletter-sub">{{ $subtitle }}</p>
            @endif
            <form action="{{ theme_newsletter_subscribe_url() }}" method="POST" class="pf-newsletter-form newsletter-form footer-widget">
                @csrf
                <div class="form-message-show"></div>
                <div class="pf-newsletter-input-wrap">
                    <input type="email" name="email" class="email pf-newsletter-input"
                           placeholder="{{ __('Enter your email address') }}" required>
                    <button type="submit" class="newsletter-submit-btn pf-newsletter-submit">{{ $button_text }}</button>
                </div>
            </form>
            <p class="pf-newsletter-note">
                <i class="las la-shield-alt"></i>
                {{ __('No spam. Unsubscribe at any time. We never share your data.') }}
            </p>
        </div>
    </div>
</section>
