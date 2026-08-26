{{-- Casual: Footer — cream/warm 4-column layout --}}
<footer class="cs-footer">
    <div class="container">
        {{-- Main footer columns --}}
        <div class="cs-footer-main">

            {{-- Col 1: Brand + About --}}
            <div class="cs-footer-col cs-footer-brand-col">
                <a href="{{ theme_home_url() }}" class="cs-footer-logo-link">
                    @php
                        $logoUrl = theme_logo_url();
                        $whiteLogo = function_exists('theme_white_logo_url') ? theme_white_logo_url() : $logoUrl;
                    @endphp
                    @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ get_static_option('site_title') ?? config('app.name') }}" class="cs-footer-logo">
                    @else
                    <span class="cs-footer-logo-text">{{ get_static_option('site_title') ?? config('app.name') }}</span>
                    @endif
                </a>
                @php $about = get_static_option('footer_about_text') ?? get_static_option('site_description') ?? ''; @endphp
                @if($about)
                <p class="cs-footer-about">{{ Str::limit(strip_tags($about), 130) }}</p>
                @endif

                {{-- Social Icons --}}
                @if(!empty($all_social_icons))
                <div class="cs-footer-social">
                    @foreach($all_social_icons as $social)
                    @if(!empty($social->url))
                    <a href="{{ $social->url }}" class="cs-footer-social-icon" target="_blank" rel="noopener noreferrer">
                        <i class="{{ $social->icon ?? 'lab la-link' }}"></i>
                    </a>
                    @endif
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Col 2: Quick Links --}}
            <div class="cs-footer-col">
                <h4 class="cs-footer-col-title">{{ __('Quick Links') }}</h4>
                <ul class="cs-footer-links">
                    <li><a href="{{ theme_home_url() }}">{{ __('Home') }}</a></li>
                    <li><a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a></li>
                    @if(function_exists('theme_digital_shop_url'))
                    <li><a href="{{ theme_digital_shop_url() }}">{{ __('Digital Products') }}</a></li>
                    @endif
                    <li><a href="{{ theme_blog_url() }}">{{ __('Blog') }}</a></li>
                    @if(function_exists('theme_contact_url'))
                    <li><a href="{{ theme_contact_url() }}">{{ __('Contact') }}</a></li>
                    @endif
                    @if(function_exists('theme_about_url'))
                    <li><a href="{{ theme_about_url() }}">{{ __('About Us') }}</a></li>
                    @endif
                </ul>
            </div>

            {{-- Col 3: Policies --}}
            <div class="cs-footer-col">
                <h4 class="cs-footer-col-title">{{ __('Policies') }}</h4>
                <ul class="cs-footer-links">
                    @if(function_exists('theme_privacy_policy_url'))
                    <li><a href="{{ theme_privacy_policy_url() }}">{{ __('Privacy Policy') }}</a></li>
                    @endif
                    @if(function_exists('theme_terms_url'))
                    <li><a href="{{ theme_terms_url() }}">{{ __('Terms & Conditions') }}</a></li>
                    @endif
                    @if(function_exists('theme_refund_policy_url'))
                    <li><a href="{{ theme_refund_policy_url() }}">{{ __('Refund Policy') }}</a></li>
                    @endif
                    @if(function_exists('theme_cookie_policy_url'))
                    <li><a href="{{ theme_cookie_policy_url() }}">{{ __('Cookie Policy') }}</a></li>
                    @endif
                </ul>
            </div>

            {{-- Col 4: Newsletter --}}
            <div class="cs-footer-col cs-footer-newsletter-col">
                <h4 class="cs-footer-col-title">{{ __('Stay Updated') }}</h4>
                <p class="cs-footer-newsletter-text">{{ __('Subscribe to get the latest offers and style tips.') }}</p>
                @if(function_exists('theme_newsletter_form'))
                <div class="cs-footer-newsletter">
                    {!! theme_newsletter_form() !!}
                </div>
                @else
                <form class="cs-footer-newsletter-form" action="#" method="POST">
                    @csrf
                    <input type="email" name="email" placeholder="{{ __('Your email address') }}" class="cs-footer-email-input" required>
                    <button type="submit" class="cs-footer-subscribe-btn">{{ __('Subscribe') }}</button>
                </form>
                @endif
            </div>
        </div>

        {{-- Footer Bottom --}}
        <div class="cs-footer-bottom">
            <div class="cs-footer-copyright">
                {!! theme_footer_copyright() !!}
            </div>
        </div>
    </div>
</footer>
