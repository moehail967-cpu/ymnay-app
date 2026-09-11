{{-- TrailCo: Newsletter Widget --}}
<section class="tc-newsletter-outer py-5">
    <div class="container">
        <div class="tc-newsletter">
            <span class="tc-section-tag">{{ __('Trail Bulletin') }}</span>
            @if($title)
                <h3>{!! $title !!}</h3>
            @endif
            @if($subtitle)
                <p>{{ $subtitle }}</p>
            @endif
            <div class="form-message-show"></div>
            <form class="tc-newsletter-form newsletter-form footer-widget" method="POST"
                  action="{{ route('tenant.frontend.subscribe.newsletter') }}">
                @csrf
                <input type="email" name="email" class="email"
                       placeholder="{{ __('Your email address') }}" required>
                <button type="submit" class="newsletter-submit-btn">
                    {{ $button_text ?: __('Subscribe') }}
                </button>
            </form>
        </div>
    </div>
</section>
