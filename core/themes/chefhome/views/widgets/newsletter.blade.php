<section class="ch-newsletter-section" style="padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px;">
    <div class="container text-center">
        <h2 class="ch-newsletter-title">{{ $title }}</h2>
        @if(!empty($subtitle))
            <p class="ch-newsletter-sub">{{ $subtitle }}</p>
        @endif

        @if(function_exists('tenant') && !is_null(tenant()))
            <div class="form-message-show ch-nl-msg"></div>
            <form action="{{ theme_newsletter_subscribe_url() }}" method="POST" class="ch-newsletter-form justify-content-center">
                @csrf
                <input type="email" name="email" class="ch-newsletter-input"
                       placeholder="{{ __('Enter your email address') }}" required>
                <button type="submit" class="ch-btn ch-btn-primary">
                    {{ $button_text }}
                </button>
            </form>
        @endif
    </div>
</section>
