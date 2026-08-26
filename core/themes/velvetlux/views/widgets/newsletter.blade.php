{{-- VelvetLux: Newsletter --}}
<section class="vl-newsletter-outer py-5">
    <div class="container">
        <div class="vl-newsletter">
            @if($title)
                <h3>{!! $title !!}</h3>
            @endif
            @if($subtitle)
                <p>{{ $subtitle }}</p>
            @endif
            <div class="form-message-show"></div>
            <form class="vl-newsletter-form newsletter-form footer-widget" method="POST"
                  action="{{ route('tenant.frontend.subscribe.newsletter') }}">
                @csrf
                <input type="email" name="email" class="email"
                       placeholder="{{ __('Your email address') }}" required>
                <button type="submit" class="newsletter-submit-btn">
                    {{ $button_text ?: __('SUBSCRIBE') }}
                </button>
            </form>
        </div>
    </div>
</section>
