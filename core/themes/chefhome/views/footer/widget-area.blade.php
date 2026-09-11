{{-- ChefHome: Footer --}}
<footer class="ch-footer">
    <div class="container">
        <div class="row g-4 pb-4">

            {{-- Brand --}}
            <div class="col-lg-4 col-md-6">
                @php $white_logo = theme_white_logo_url() ?? theme_logo_url(); @endphp
                @if($white_logo)
                    <a href="{{ theme_home_url() }}" class="ch-footer-logo d-inline-block mb-3">
                        <img src="{{ $white_logo }}" alt="{{ get_static_option('site_title') }}">
                    </a>
                @else
                    <div class="ch-footer-brand mb-3">{{ get_static_option('site_title') }}</div>
                @endif

                @if(get_static_option('site_description'))
                    <p class="ch-footer-desc">{{ get_static_option('site_description') }}</p>
                @endif

                @if($all_social_icons->isNotEmpty())
                    <div class="d-flex gap-3 mt-4">
                        @foreach($all_social_icons as $social)
                            <a href="{{ $social->url }}" class="ch-footer-link p-0 mb-0"
                               style="font-size:20px;" target="_blank" rel="noopener">
                                <i class="{{ $social->icon }}"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Quick Links --}}
            @php $quick_links = theme_footer_menu_links(); @endphp
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="ch-footer-h">{{ __('Quick Links') }}</h6>
                @if($quick_links)
                    @foreach($quick_links as $link)
                        <a href="{{ $link['url'] }}" class="ch-footer-link">{{ $link['label'] }}</a>
                    @endforeach
                @else
                    <a href="{{ theme_home_url() }}" class="ch-footer-link">{{ __('Home') }}</a>
                    <a href="{{ theme_shop_url() }}" class="ch-footer-link">{{ __('Shop') }}</a>
                @endif
            </div>

            {{-- Policies --}}
            @php $policies_html = theme_footer_widgets('footer_policies', false); @endphp
            <div class="col-lg-2 col-md-3 col-6">
                @if(trim($policies_html))
                    {!! $policies_html !!}
                @else
                    <h6 class="ch-footer-h">{{ __('Policies') }}</h6>
                    <a href="#" class="ch-footer-link">{{ __('Privacy Policy') }}</a>
                    <a href="#" class="ch-footer-link">{{ __('Return Policy') }}</a>
                    <a href="#" class="ch-footer-link">{{ __('Terms & Conditions') }}</a>
                    <a href="#" class="ch-footer-link">{{ __('FAQ') }}</a>
                @endif
            </div>

            {{-- Contact --}}
            @php
                $c_address = get_static_option('topbar_address');
                $c_email   = get_static_option('topbar_email');
                $c_phone   = get_static_option('topbar_phone');
            @endphp
            @if($c_address || $c_email || $c_phone)
                <div class="col-lg-4 col-md-6">
                    <h6 class="ch-footer-h">{{ __('Contact Us') }}</h6>
                    @if($c_address)
                        <span class="ch-footer-link d-block">
                            <i class="las la-map-marker me-2"></i>{{ $c_address }}
                        </span>
                    @endif
                    @if($c_email)
                        <a href="mailto:{{ $c_email }}" class="ch-footer-link">
                            <i class="las la-envelope me-2"></i>{{ $c_email }}
                        </a>
                    @endif
                    @if($c_phone)
                        <a href="tel:{{ $c_phone }}" class="ch-footer-link">
                            <i class="las la-phone-alt me-2"></i>{{ $c_phone }}
                        </a>
                    @endif
                </div>
            @endif

        </div>
    </div>

    <div class="ch-footer-bottom">
        <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span class="ch-footer-bottom-text">{!! theme_footer_copyright() !!}</span>
            <span class="ch-footer-bottom-text">{{ __('Powered by Nazmart') }}</span>
        </div>
    </div>
</footer>
