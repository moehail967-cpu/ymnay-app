{{-- PawHaus: Newsletter --}}
<section class="ph-newsletter-outer">
    <div class="container">
        <div class="ph-newsletter-card">
            <h2 class="ph-newsletter-title">{!! $title !!}</h2>
            @if(!empty($subtitle))
                <p class="ph-newsletter-sub">{{ $subtitle }}</p>
            @endif
            @if(function_exists('tenant') && !is_null(tenant()))
                <form action="{{ theme_newsletter_subscribe_url() }}" method="POST"
                      class="ph-newsletter-form newsletter-form">
                    @csrf
                    <div class="form-message-show"></div>
                    <div class="ph-nl-row">
                        <input type="email" name="email" class="ph-newsletter-input email"
                               placeholder="{{ __('Your email address') }}" required>
                        <button type="submit" class="newsletter-submit-btn ph-newsletter-btn">
                            {{ $button_text }}
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</section>
