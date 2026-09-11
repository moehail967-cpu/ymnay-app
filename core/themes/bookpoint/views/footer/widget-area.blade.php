<footer class="bp-footer">
    {{-- Top bar: logo + payment methods --}}
    <div class="bp-footer-top">
        <div class="container">
            <div class="bp-footer-top-inner">
                <a href="{{ theme_home_url() }}" class="bp-footer-logo">
                    @php $logo = theme_white_logo_url() ?? theme_logo_url(); @endphp
                    @if($logo)
                        <img src="{{ $logo }}" alt="{{ get_static_option('site_title') }}">
                    @else
                        <span class="bp-footer-logo-text"><i class="las la-book"></i> {{ get_static_option('site_title') }}</span>
                    @endif
                </a>
                <div class="bp-footer-payments">
                    <span class="bp-payment-badge">Google Pay</span>
                    <span class="bp-payment-badge">Apple Pay</span>
                    <span class="bp-payment-badge">Visa</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bp-footer-divider"></div>

    {{-- Main footer grid --}}
    <div class="bp-footer-main">
        <div class="container">
            <div class="row g-4">

                {{-- Col 1: Contact --}}
                <div class="col-lg-3 col-md-6">
                    <h6 class="bp-footer-heading">{{ __('Contact Us') }}</h6>
                    @php
                        $f_address = get_static_option('topbar_address');
                        $f_phone   = get_static_option('topbar_phone');
                        $f_email   = get_static_option('topbar_email');
                    @endphp
                    <div class="bp-contact-list">
                        @if($f_address)
                        <div class="bp-contact-item">
                            <span class="bp-contact-badge">A</span>
                            <div>
                                <div class="bp-contact-label">{{ __('Location') }}</div>
                                <div class="bp-contact-val">{{ $f_address }}</div>
                            </div>
                        </div>
                        @endif
                        @if($f_phone)
                        <div class="bp-contact-item">
                            <span class="bp-contact-badge"><i class="las la-phone-alt"></i></span>
                            <div>
                                <div class="bp-contact-label">{{ __('Any Question?') }}</div>
                                <div class="bp-contact-val">{{ $f_phone }}</div>
                            </div>
                        </div>
                        @endif
                        @if($f_email)
                        <div class="bp-contact-item">
                            <span class="bp-contact-badge"><i class="las la-envelope"></i></span>
                            <div>
                                <div class="bp-contact-label">{{ __('Contact') }}</div>
                                <div class="bp-contact-val">{{ $f_email }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Col 2: Shop --}}
                <div class="col-lg-2 col-md-3 col-6">
                    <h6 class="bp-footer-heading">{{ __('Shop') }}</h6>
                    <div class="bp-footer-links">
                        <a href="{{ theme_home_url() }}" class="bp-footer-link">{{ __('Home') }}</a>
                        <a href="#" class="bp-footer-link">{{ __('About Us') }}</a>
                        <a href="#" class="bp-footer-link">{{ __('Contact') }}</a>
                        <a href="{{ theme_shop_url() }}" class="bp-footer-link">{{ __('Shop') }}</a>
                        <a href="{{ theme_blog_url() }}" class="bp-footer-link">{{ __('Blog') }}</a>
                        <a href="{{ theme_digital_shop_url() }}" class="bp-footer-link">{{ __('Digital Product') }}</a>
                    </div>
                </div>

                {{-- Col 3: Category --}}
                <div class="col-lg-2 col-md-3 col-6">
                    <h6 class="bp-footer-heading">{{ __('Category') }}</h6>
                    <div class="bp-footer-links">
                        <a href="{{ theme_home_url() }}" class="bp-footer-link">{{ __('Home') }}</a>
                        <a href="#" class="bp-footer-link">{{ __('About Us') }}</a>
                        <a href="#" class="bp-footer-link">{{ __('Contact') }}</a>
                        <a href="{{ theme_shop_url() }}" class="bp-footer-link">{{ __('Shop') }}</a>
                        <a href="{{ theme_blog_url() }}" class="bp-footer-link">{{ __('Blog') }}</a>
                        <a href="{{ theme_digital_shop_url() }}" class="bp-footer-link">{{ __('Digital Product') }}</a>
                    </div>
                </div>

                {{-- Col 4: About --}}
                <div class="col-lg-2 col-md-3 col-6">
                    <h6 class="bp-footer-heading">{{ __('About') }}</h6>
                    <div class="bp-footer-links">
                        <a href="{{ theme_home_url() }}" class="bp-footer-link">{{ __('Home') }}</a>
                        <a href="#" class="bp-footer-link">{{ __('About Us') }}</a>
                        <a href="#" class="bp-footer-link">{{ __('Contact') }}</a>
                        <a href="{{ theme_shop_url() }}" class="bp-footer-link">{{ __('Shop') }}</a>
                        <a href="{{ theme_blog_url() }}" class="bp-footer-link">{{ __('Blog') }}</a>
                        <a href="{{ theme_digital_shop_url() }}" class="bp-footer-link">{{ __('Digital Product') }}</a>
                    </div>
                </div>

                {{-- Col 5: Newsletter --}}
                <div class="col-lg-3 col-md-6">
                    <h6 class="bp-footer-heading">{{ __('Newsletter') }}</h6>
                    <p class="bp-footer-newsletter-desc">{{ __('Be the first one to know about discounts, offers and events. Unsubscribe whenever you like.') }}</p>
                    {!! theme_newsletter_form('bp-newsletter-form', '<i class="las la-arrow-right"></i>') !!}
                    @if($all_social_icons->isNotEmpty())
                    <div class="bp-footer-socials">
                        @foreach($all_social_icons as $social)
                            <a href="{{ $social->url }}" target="_blank" rel="noopener" class="bp-social-icon">
                                <i class="{{ $social->icon }}"></i>
                            </a>
                        @endforeach
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Bottom copyright --}}
    <div class="bp-footer-bottom">
        <div class="container">
            <div class="bp-footer-copy">{!! theme_footer_copyright() !!}</div>
        </div>
    </div>
</footer>
