{{-- Pharmacy: Footer --}}
<footer class="pf-footer">
    <div class="container">
        <div class="row g-4 pf-footer-cols">

            {{-- Brand Column --}}
            <div class="col-lg-3 col-md-6">
                @php $white_logo = theme_white_logo_url() ?? theme_logo_url(); @endphp
                @if($white_logo)
                    <a href="{{ theme_home_url() }}" class="pf-footer-logo-link d-inline-block mb-3">
                        <img src="{{ $white_logo }}" alt="{{ get_static_option('site_title') }}" class="pf-footer-logo-img">
                    </a>
                @endif
                @if(get_static_option('site_description'))
                    <p class="pf-footer-desc">{{ get_static_option('site_description') }}</p>
                @endif
                @if($all_social_icons->isNotEmpty())
                    <div class="pf-footer-socials">
                        @foreach($all_social_icons as $social)
                            <a href="{{ $social->url }}" target="_blank" rel="noopener" class="pf-footer-social-btn">
                                <i class="{{ $social->icon }}"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
                @php $gphc = get_static_option('pharmacy_gphc_number'); @endphp
                @if($gphc)
                    <p class="pf-footer-gphc"><i class="las la-shield-alt"></i> {{ __('GPhC Registration No.') }} {{ $gphc }}</p>
                @endif
            </div>

            {{-- SHOP Column --}}
            @php $shop_links = theme_footer_menu_links(); @endphp
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="pf-footer-col-title">{{ __('Shop') }}</h6>
                @if($shop_links)
                    @foreach($shop_links as $link)
                        <a href="{{ $link['url'] }}" class="pf-footer-link d-block">{{ $link['label'] }}</a>
                    @endforeach
                @else
                    <a href="{{ theme_shop_url() }}" class="pf-footer-link d-block">{{ __('All Products') }}</a>
                    <a href="{{ theme_shop_url() }}?category=otc-medicines" class="pf-footer-link d-block">{{ __('OTC Medicines') }}</a>
                    <a href="{{ theme_shop_url() }}?category=vitamins" class="pf-footer-link d-block">{{ __('Vitamins & Supplements') }}</a>
                    <a href="{{ theme_shop_url() }}?category=medical-devices" class="pf-footer-link d-block">{{ __('Medical Devices') }}</a>
                    <a href="{{ theme_shop_url() }}?category=first-aid" class="pf-footer-link d-block">{{ __('First Aid') }}</a>
                    <a href="{{ theme_shop_url() }}?category=baby-mother" class="pf-footer-link d-block">{{ __('Baby & Mother') }}</a>
                    <a href="{{ theme_shop_url() }}?category=personal-care" class="pf-footer-link d-block">{{ __('Personal Care') }}</a>
                @endif
            </div>

            {{-- SERVICES Column --}}
            @php $services_html = theme_footer_widgets('footer_services', false); @endphp
            <div class="col-lg-2 col-md-3 col-6">
                @if(trim($services_html))
                    {!! $services_html !!}
                @else
                    <h6 class="pf-footer-col-title">{{ __('Services') }}</h6>
                    <a href="#" class="pf-footer-link d-block">{{ __('Prescription Upload') }}</a>
                    <a href="#" class="pf-footer-link d-block">{{ __('Pharmacist Advice') }}</a>
                    <a href="#" class="pf-footer-link d-block">{{ __('Medication Review') }}</a>
                    <a href="#" class="pf-footer-link d-block">{{ __('NHS Prescriptions') }}</a>
                    <a href="#" class="pf-footer-link d-block">{{ __('Health Testing') }}</a>
                    <a href="#" class="pf-footer-link d-block">{{ __('Travel Clinic') }}</a>
                @endif
            </div>

            {{-- SUPPORT Column --}}
            @php $support_html = theme_footer_widgets('footer_support', false); @endphp
            <div class="col-lg-2 col-md-3 col-6">
                @if(trim($support_html))
                    {!! $support_html !!}
                @else
                    <h6 class="pf-footer-col-title">{{ __('Support') }}</h6>
                    <a href="#" class="pf-footer-link d-block">{{ __('Track Order') }}</a>
                    <a href="#" class="pf-footer-link d-block">{{ __('Returns Policy') }}</a>
                    <a href="#" class="pf-footer-link d-block">{{ __('Delivery Info') }}</a>
                    <a href="#" class="pf-footer-link d-block">{{ __('FAQs') }}</a>
                    <a href="#" class="pf-footer-link d-block">{{ __('Contact Us') }}</a>
                    <a href="#" class="pf-footer-link d-block">{{ __('Live Chat') }}</a>
                @endif
            </div>

            {{-- CONTACT Column --}}
            @php
                $c_phone   = get_static_option('topbar_phone');
                $c_email   = get_static_option('topbar_email');
                $c_address = get_static_option('topbar_address');
                $c_hours   = get_static_option('topbar_hours') ?? get_static_option('topbar_open_time');
            @endphp
            <div class="col-lg-3 col-md-3 col-6">
                <h6 class="pf-footer-col-title">{{ __('Contact') }}</h6>
                @if($c_phone)
                    <a href="tel:{{ $c_phone }}" class="pf-footer-contact-item">
                        <i class="las la-phone-alt"></i> {{ $c_phone }}
                    </a>
                @endif
                @if($c_email)
                    <a href="mailto:{{ $c_email }}" class="pf-footer-contact-item">
                        <i class="las la-envelope"></i> {{ $c_email }}
                    </a>
                @endif
                @if($c_address)
                    <span class="pf-footer-contact-item">
                        <i class="las la-map-marker-alt"></i> {{ $c_address }}
                    </span>
                @endif
                @if($c_hours)
                    <span class="pf-footer-contact-item">
                        <i class="las la-clock"></i> {{ $c_hours }}
                    </span>
                @endif
            </div>

        </div>
    </div>

    <div class="pf-footer-bottom">
        <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span class="pf-footer-copy">{!! theme_footer_copyright() !!}</span>
            <div class="d-flex align-items-center gap-3">
                <a href="#" class="pf-footer-bottom-link">{{ __('Privacy Policy') }}</a>
                <a href="#" class="pf-footer-bottom-link">{{ __('Terms of Use') }}</a>
                <a href="#" class="pf-footer-bottom-link">{{ __('Cookie Settings') }}</a>
            </div>
            <div>
                <i class="las la-credit-card pf-footer-payment-icon"></i>
            </div>
        </div>
    </div>
</footer>
