{{-- SportZone: Newsletter Widget --}}
<section class="sz-newsletter-section">
    <div class="container">
        <div class="sz-newsletter">

            <span class="sz-section-tag"><i class="mdi mdi-lightning-bolt"></i> {{ __('Join The Team') }}</span>

            <h3>{{ $title }}</h3>

            @if($subtitle)
                <p class="sz-section-sub" style="color:rgba(255,255,255,.7);">{{ $subtitle }}</p>
            @endif

            <div class="form-message-show sz-nl-msg"></div>
            <form class="sz-newsletter-form newsletter-form" method="POST"
                  action="{{ route('tenant.frontend.subscribe.newsletter') }}">
                @csrf
                <input type="email"
                       name="email"
                       class="email"
                       placeholder="{{ __('Your email address') }}"
                       required>
                <button type="submit" class="newsletter-submit-btn">{{ $button_text }}</button>
            </form>

        </div>
    </div>
</section>
