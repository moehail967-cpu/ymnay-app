<section class="fp-newsletter-section">
    <div class="container">
        <div class="fp-nl-card text-center">

            @if(!empty($tag_text))
                <div class="fp-nl-badge mb-3">
                    <i class="mdi mdi-lightning-bolt"></i> {{ $tag_text }}
                </div>
            @endif

            @if(!empty($title))
                <h2 class="fp-newsletter-title">
                    @php
                        $accent = $title_accent ?? '';
                        $plain  = $title ?? '';
                        echo $accent && str_contains($plain, $accent)
                            ? str_replace($accent, '<span class="fp-nl-accent">'.$accent.'</span>', e($plain))
                            : e($plain);
                    @endphp
                </h2>
            @endif

            @if(!empty($subtitle))
                <p class="fp-newsletter-sub">{{ $subtitle }}</p>
            @endif

            @if(function_exists('tenant') && !is_null(tenant()))
                <form action="{{ theme_newsletter_subscribe_url() }}" method="POST" class="newsletter-form">
                    @csrf
                    <div class="form-message-show"></div>
                    <div class="fp-nl-form">
                        <input type="email" name="email" class="fp-nl-input email"
                               placeholder="{{ __('Enter your email') }}" required>
                        <button type="submit" class="fp-nl-btn newsletter-submit-btn">
                            {{ $button_text }}
                        </button>
                    </div>
                </form>
            @endif

        </div>
    </div>
</section>
