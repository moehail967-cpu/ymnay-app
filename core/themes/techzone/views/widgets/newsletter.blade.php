<section class="tz-newsletter-section">
    <div class="container">
        <div class="tz-newsletter">
            <span class="tz-section-tag"><i class="mdi mdi-bell-outline"></i> {{ __('Tech Alerts') }}</span>
            <h3>{{ $title }}</h3>
            @if(!empty($subtitle))
                <p class="tz-section-sub">{{ $subtitle }}</p>
            @endif

            {!! theme_newsletter_form('tz-newsletter-form') !!}
        </div>
    </div>
</section>
