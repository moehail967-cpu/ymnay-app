@php
    $quick_links    = theme_footer_menu_links();
    $policies_html  = theme_footer_widgets('footer_policies', false);
    $c_address      = get_static_option('topbar_address');
    $c_email        = get_static_option('topbar_email');
    $c_phone        = get_static_option('topbar_phone');
    $footer_desc    = get_static_option('footer_description') ?: get_static_option('site_description');
    $white_logo     = theme_white_logo_url() ?? theme_logo_url();
@endphp

<footer class="ar-footer">
    <div class="container">
        <div class="ar-footer-grid">

            {{-- Column 1: Brand --}}
            <div class="ar-footer-col ar-footer-col-brand">
                @if($white_logo)
                    <a href="{{ theme_home_url() }}" class="ar-footer-logo">
                        <img src="{{ $white_logo }}" alt="{{ get_static_option('site_title') }}">
                    </a>
                @else
                    <a href="{{ theme_home_url() }}" class="ar-footer-logo-text">
                        {{ get_static_option('site_title') }}
                    </a>
                @endif
                @if($footer_desc)
                    <p class="ar-footer-desc">{{ $footer_desc }}</p>
                @endif
                @if($all_social_icons->isNotEmpty())
                    <div class="ar-footer-socials">
                        @foreach($all_social_icons as $social)
                            <a href="{{ $social->url }}" target="_blank" rel="noopener noreferrer" class="ar-social-link">
                                <i class="{{ $social->icon }}"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Column 2: Quick Links (always render with fallback) --}}
            <div class="ar-footer-col">
                <h6 class="ar-footer-heading">{{ __('Quick Links') }}</h6>
                @if(!empty($quick_links))
                    @foreach($quick_links as $link)
                        <a href="{{ $link['url'] }}" class="ar-footer-link">{{ $link['label'] }}</a>
                    @endforeach
                @else
                    <a href="{{ theme_home_url() }}" class="ar-footer-link">{{ __('Home') }}</a>
                    <a href="{{ theme_shop_url() }}" class="ar-footer-link">{{ __('Shop') }}</a>
                @endif
            </div>

            {{-- Column 3: Policies (always render with fallback) --}}
            <div class="ar-footer-col">
                @if(trim($policies_html ?? ''))
                    {!! $policies_html !!}
                @else
                    <h6 class="ar-footer-heading">{{ __('Policies') }}</h6>
                    <a href="#" class="ar-footer-link">{{ __('Privacy Policy') }}</a>
                    <a href="#" class="ar-footer-link">{{ __('Return Policy') }}</a>
                    <a href="#" class="ar-footer-link">{{ __('Terms & Conditions') }}</a>
                    <a href="#" class="ar-footer-link">{{ __('FAQ') }}</a>
                @endif
            </div>

            {{-- Column 4: Contact (render if data exists) --}}
            @if($c_address || $c_email || $c_phone)
            <div class="ar-footer-col">
                <h6 class="ar-footer-heading">{{ __('Contact Us') }}</h6>
                @if($c_address)
                    <span class="ar-footer-link ar-footer-contact-item">
                        <i class="las la-map-marker"></i>{{ $c_address }}
                    </span>
                @endif
                @if($c_email)
                    <a href="mailto:{{ $c_email }}" class="ar-footer-link ar-footer-contact-item">
                        <i class="las la-envelope"></i>{{ $c_email }}
                    </a>
                @endif
                @if($c_phone)
                    <a href="tel:{{ $c_phone }}" class="ar-footer-link ar-footer-contact-item">
                        <i class="las la-phone-alt"></i>{{ $c_phone }}
                    </a>
                @endif
            </div>
            @endif

        </div>
    </div>

    <div class="ar-footer-bottom">
        <div class="ar-container ar-footer-bottom-inner">
            <span>{!! theme_footer_copyright() !!}</span>
            <div class="ar-footer-bottom-right">
                {!! theme_footer_widgets('footer_bottom_right', false) !!}
            </div>
        </div>
    </div>
</footer>
